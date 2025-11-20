// app.js — Mood in 30 Days (vanilla JS) + Emoji fix + Supabase sync

// ===== Local state (browser) =====
const LS_KEY = "mood-tracker-vanilla";
const todayISO = () => new Date().toISOString().slice(0, 10);

// Matikan fitur backup lokal (ekspor/impor JSON)
const ENABLE_BACKUP = false;

const MOODS = [
  { score: 1, label: "Buruk",  icon: "🙁" },
  { score: 2, label: "Kurang", icon: "😕" },
  { score: 3, label: "Biasa",  icon: "😐" },
  { score: 4, label: "Baik",   icon: "🙂" },
  { score: 5, label: "Hebat",  icon: "😁" },
];

const PHASES = [
  { range: [1, 10],  title: "Kesadaran Diri",        gradient: "linear-gradient(90deg,#0284c7,#22d3ee)" },
  { range: [11, 20], title: "Regulasi Emosi",        gradient: "linear-gradient(90deg,#6366f1,#8b5cf6)" },
  { range: [21, 30], title: "Optimisme & Kebiasaan", gradient: "linear-gradient(90deg,#10b981,#14b8a6)" },
];

const pickPhase = (day) => PHASES.find(p => day >= p.range[0] && day <= p.range[1]) || PHASES[0];

function generateProgram() {
  const base = {
    "Kesadaran Diri": [
      "Tuliskan 3 hal yang kamu rasakan sekarang",
      "Tarik napas 4-4-6 selama 3 menit",
      "Jalan kaki ringan 5-10 menit",
      "Catat 1 pemicu emosi hari ini",
    ],
    "Regulasi Emosi": [
      "Reframe: ubah 1 pikiran negatif jadi netral",
      "Meditasi hening 5 menit",
      "Sapa teman/keluarga via chat",
      "Peregangan ringan 5 menit",
    ],
    "Optimisme & Kebiasaan": [
      "Tulis 3 hal yang kamu syukuri",
      "Rencanakan 1 hal kecil menyenangkan",
      "Latihan senyum 60 detik di cermin",
      "Beri diri sendiri apresiasi singkat",
    ],
  };
  return Array.from({ length: 30 }, (_, i) => {
    const day = i + 1;
    const phase = pickPhase(day);
    const shuffled = [...base[phase.title]].sort(() => Math.random() - 0.5);
    return { day, phase: phase.title, tasks: shuffled.slice(0, 3) };
  });
}

// ===== State & helpers =====
const defaultState = () => ({
  startedAt: todayISO(),
  name: "",
  dark: false,
  reminders: false,
  program: generateProgram(),
  moods: {}, // 'YYYY-MM-DD': { score, note, day, tasksDone: [i] }
});

function load() {
  try { return JSON.parse(localStorage.getItem(LS_KEY)) || null; } catch { return null; }
}
function save(s) {
  try { localStorage.setItem(LS_KEY, JSON.stringify(s)); } catch {}
}
let state = load() || defaultState();

const dayIndexFrom = (startISO, refISO) => {
  const a = new Date(startISO), b = new Date(refISO);
  const diff = Math.floor((b - a) / 86400000);
  return Math.min(29, Math.max(0, diff));
};
const avg = arr => arr.length ? arr.reduce((a,b)=>a+b,0)/arr.length : 0;
const streakDays = (moods) => {
  let c=0;
  for (let i=0;i<365;i++) {
    const d = new Date(); d.setDate(d.getDate()-i);
    const id = d.toISOString().slice(0,10);
    if (moods[id]) c++; else break;
  }
  return c;
};

// ===== DOM refs =====
const views = {
  today:    document.getElementById("view-today"),
  program:  document.getElementById("view-program"),
  journal:  document.getElementById("view-journal"),
  stats:    document.getElementById("view-stats"),
  settings: document.getElementById("view-settings"),
};
const notifStatus = document.getElementById("notifStatus");
const themeToggle = document.getElementById("themeToggle");
const btnLogin = document.getElementById("btnLogin");

// ===== Theme init =====
document.documentElement.classList.toggle("dark", state.dark);
themeToggle.textContent = state.dark ? "☾ Gelap" : "☀︎ Terang";
themeToggle.addEventListener("click", async () => {
  state.dark = !state.dark; save(state);
  document.documentElement.classList.toggle("dark", state.dark);
  themeToggle.textContent = state.dark ? "☾ Gelap" : "☀︎ Terang";
  await cloudSaveSettings(); // sync bila sudah login
});

// ===== Tabs =====
document.querySelectorAll(".tabs .tab").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".tabs .tab").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    const target = btn.dataset.tab;
    Object.entries(views).forEach(([k, el]) => el.classList.toggle("active", k===target));
    render();
  });
});

// ===== Supabase (auth + sync) =====
let currentUser = null; // akan diisi saat login

function setLoginButtonFor(sessionUser){
  if (sessionUser) {
    btnLogin.textContent = "Tersinkron (Logout)";
    btnLogin.onclick = async () => { await sb.auth.signOut(); location.reload(); };
  } else {
    btnLogin.textContent = "Masuk / Simpan ke Cloud";
    btnLogin.onclick = async () => {
      if (!window.sb) { alert("Supabase belum termuat."); return; }
      const email = prompt("Masukkan email untuk menerima tautan login:");
      if (!email) return;
      const { error } = await sb.auth.signInWithOtp({ email });
      if (error) alert(error.message);
      else alert("Cek email kamu untuk menyelesaikan login.");
    };
  }
}

// pantau perubahan sesi
if (window.sb) {
  sb.auth.onAuthStateChange(async (_event, session) => {
    currentUser = session?.user || null;
    setLoginButtonFor(currentUser);
    if (currentUser) {
      await ensureProfile();
      await cloudLoad();     // tarik data dari cloud
    }
  });
  // set state awal tombol
  sb.auth.getSession().then(({ data }) => {
    currentUser = data.session?.user || null;
    setLoginButtonFor(currentUser);
  });
}

async function ensureProfile(){
  try {
    await sb.from("profiles").upsert({ id: currentUser.id, name: state.name || null });
  } catch {}
}

async function cloudSaveSettings() {
  if (!currentUser || !window.sb) return;
  try {
    await sb.from("settings").upsert({
      user_id: currentUser.id,
      started_at: state.startedAt,
      dark: state.dark,
      reminders: state.reminders
    });
  } catch {}
}

async function cloudSaveProfileName() {
  if (!currentUser || !window.sb) return;
  try {
    await sb.from("profiles").upsert({ id: currentUser.id, name: state.name || null });
  } catch {}
}

async function cloudSaveMood(dateISO, entry) {
  if (!currentUser || !window.sb) return;
  try {
    await sb.from("moods").upsert({
      user_id: currentUser.id,
      date: dateISO,
      score: entry.score || null,
      note: entry.note || null,
      tasks_done: JSON.stringify(entry.tasksDone || [])
    });
  } catch {}
}

async function cloudLoad() {
  if (!currentUser || !window.sb) return;
  // settings
  const { data: s } = await sb.from("settings").select("*").eq("user_id", currentUser.id).maybeSingle();
  if (s) {
    state.startedAt = s.started_at || state.startedAt;
    state.dark = !!s.dark;
    state.reminders = !!s.reminders;
    document.documentElement.classList.toggle("dark", state.dark);
    themeToggle.textContent = state.dark ? "☾ Gelap" : "☀︎ Terang";
  }
  // moods (60 hari terakhir)
  const from = new Date(); from.setDate(from.getDate()-60);
  const { data: ms } = await sb.from("moods")
    .select("date,score,note,tasks_done")
    .eq("user_id", currentUser.id)
    .gte("date", from.toISOString().slice(0,10));
  if (ms) {
    ms.forEach(m => {
      state.moods[m.date] = {
        score: m.score || 0,
        note: m.note || "",
        tasksDone: Array.isArray(m.tasks_done) ? m.tasks_done : []
      };
    });
  }
  save(state); render();
}

// ===== Rendering =====
function renderToday() {
  const idx = dayIndexFrom(state.startedAt, todayISO());
  const todayDay = state.program[idx];
  const key = todayISO();
  const today = state.moods[key] || { score:0, note:"", day: idx+1, tasksDone: [] };
  const progress = Math.round(((idx+1)/30)*100);
  const phase = pickPhase(idx+1);

  // === Satu kartu besar ===
  views.today.innerHTML = `
    <section class="card">
      <div class="card-head" style="background:${phase.gradient}">
        <div class="kpi">
          <div>Hari ${todayDay.day} · ${todayDay.phase}</div>
          <span class="badge">${progress}%</span>
        </div>
      </div>

      <div class="card-body">
        <!-- Bar progres di bagian atas kartu -->
        <div class="kpi">
          <span class="muted small">Progres 30 Hari</span>
          <span class="muted small">Mulai ${new Date(state.startedAt).toLocaleDateString()}</span>
        </div>
        <div class="progress" style="margin:8px 0 4px;"><div style="width:${progress}%"></div></div>
        <div class="muted small">Hari ke-${idx+1} dari 30 · Fase: ${phase.title}</div>

        <div class="divider"></div>

        <!-- Konten 2 kolom di dalam satu kartu -->
        <div class="grid-2">
          <!-- Kiri: tantangan + mood + catatan -->
          <div>
            <div class="muted small">Tantangan harian</div>
            <ul class="list" id="taskList" style="margin-top:6px;"></ul>

            <div style="margin-top:14px;">
              <div class="muted small" style="margin-bottom:6px;">Mood hari ini</div>
              <div class="mood-row" id="moodRow"></div>
            </div>

            <div style="margin-top:14px;">
              <div class="muted small" style="margin-bottom:6px;">Catatan</div>
              <textarea id="note" class="textarea" placeholder="Bagaimana harimu? Ada pemicu tertentu?"></textarea>
            </div>
          </div>

          <!-- Kanan: tips -->
          <div>
            <div class="info">
              <div class="muted small">
                • Hindari menilai emosi; cukup amati dan beri nama.<br>
                • Konsistensi > kesempurnaan. 10 menit setiap hari sudah bagus.<br>
                • Jika gejala berat/berkepanjangan, pertimbangkan konsultasi profesional.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  `;

  // ====== Tasks ======
  const list = document.getElementById("taskList");
  list.innerHTML = todayDay.tasks.map((t,i)=>{
    const checked = today.tasksDone.includes(i);
    return `<li style="margin:6px 0; display:flex; gap:8px; align-items:center;">
      <button data-i="${i}" class="btn ${checked?'primary':''}" style="width:36px">${checked?'✓':'·'}</button>
      <span class="${checked?'muted':''}">${t}</span>
    </li>`;
  }).join("");
  list.querySelectorAll("button").forEach(b=>{
    b.addEventListener("click", async ()=>{
      const i = Number(b.dataset.i);
      const set = new Set((state.moods[key]?.tasksDone)||[]);
      if (set.has(i)) set.delete(i); else set.add(i);
      state.moods[key] = { ...(state.moods[key]||today), tasksDone:[...set] };
      save(state);
      await cloudSaveMood(key, state.moods[key]);
      renderToday();
    });
  });

  // ====== Mood buttons ======
  const row = document.getElementById("moodRow");
  row.innerHTML = MOODS.map(m=>`
    <button class="mood-btn ${today.score===m.score?'active':''}" data-score="${m.score}">
      <span class="emoji">${m.icon}</span>
      <span class="label">${m.label}</span>
    </button>
  `).join("");
  row.querySelectorAll("button").forEach(btn=>{
    btn.addEventListener("click", async ()=>{
      const score = Number(btn.dataset.score);
      state.moods[key] = { ...(state.moods[key]||today), score };
      save(state);
      await cloudSaveMood(key, state.moods[key]);
      renderToday();
    });
  });

  // ====== Note ======
  const note = document.getElementById("note");
  note.value = today.note || "";
  note.addEventListener("input", async ()=>{
    state.moods[key] = { ...(state.moods[key]||today), note: note.value };
    save(state);
    await cloudSaveMood(key, state.moods[key]);
  });
}

function renderProgram() {
  const idx = dayIndexFrom(state.startedAt, todayISO());
  views.program.innerHTML = `
    <div class="grid-3">
      ${state.program.map(item => `
        <section class="card">
          <div class="card-head" style="background:${pickPhase(item.day).gradient}">
            <div>Hari ${item.day} · ${item.phase}</div>
            ${item.day===idx+1 ? `<span class="badge">Hari ini</span>` : ""}
          </div>
          <div class="card-body">
            <ul class="list">
              ${item.tasks.map(t=>`<li>${t}</li>`).join("")}
            </ul>
          </div>
        </section>
      `).join("")}
    </div>
  `;
}

function renderJournal() {
  const entries = Object.entries(state.moods)
    .map(([date, v]) => ({date, ...v}))
    .sort((a,b)=>a.date.localeCompare(b.date));

  views.journal.innerHTML = `
    <div class="grid-2">
      <section class="card">
        <div class="card-body">
          <div style="overflow-x:auto;">
          <table class="table" style="width:100%; border-collapse:collapse;">
            <thead>
              <tr style="color:#64748b;">
                <th style="text-align:left; padding:8px 6px;">Tanggal</th>
                <th style="text-align:left; padding:8px 6px;">Mood</th>
                <th style="text-align:left; padding:8px 6px;">Catatan</th>
              </tr>
            </thead>
            <tbody id="journalRows">
              ${entries.length ? entries.map(e=>{
                const icon = (MOODS.find(m=>m.score===e.score)||{}).icon || "–";
                return `<tr style="border-top:1px solid var(--border);">
                  <td style="padding:8px 6px;">${new Date(e.date).toLocaleDateString()}</td>
                  <td style="padding:8px 6px;">${icon} ${e.score||"-"}</td>
                  <td style="padding:8px 6px; color:var(--muted);">${e.note||""}</td>
                </tr>`;
              }).join("") : `<tr><td class="muted" style="padding:12px 6px;" colspan="3">Belum ada entri.</td></tr>`}
            </tbody>
          </table>
          </div>
        </div>
      </section>

      <section class="card">
        <div class="card-body">
          <div class="muted small">Catatan Cepat</div>
          <textarea id="quickNote" class="textarea" placeholder="Tuliskan refleksi singkat di sini…"></textarea>
          <div class="small muted" style="margin-top:6px;">Tips: catat pemicu, responsmu, dan 1 langkah kecil memperbaiki suasana hati.</div>
        </div>
      </section>
    </div>
  `;

  // sinkronkan dengan catatan hari ini (opsional)
  const key = todayISO();
  const qn = document.getElementById("quickNote");
  qn.value = state.moods[key]?.note || "";
  qn.addEventListener("blur", async ()=>{
    const idx = dayIndexFrom(state.startedAt, key);
    const base = { score:0, note:"", day: idx+1, tasksDone: [] };
    state.moods[key] = { ...(state.moods[key]||base), note: qn.value };
    save(state);
    await cloudSaveMood(key, state.moods[key]);
  });
}

function renderStats() {
  // data 14 hari
  const last14 = [];
  for (let i=13;i>=0;i--) {
    const d = new Date(); d.setDate(d.getDate()-i);
    const id = d.toISOString().slice(0,10);
    const e = state.moods[id];
    last14.push({ date:id, score: e?.score || 0 });
  }
  const avg7 = avg(last14.slice(-7).map(d=>d.score).filter(Boolean));
  const avg14 = avg(last14.map(d=>d.score).filter(Boolean));
  const streak = streakDays(state.moods);

  views.stats.innerHTML = `
    <div class="grid-2">
      <section class="card">
        <div class="card-body">
          <div style="font-weight:600; margin-bottom:6px;">Trend 14 Hari</div>
          <canvas id="chartLine" height="240"></canvas>
        </div>
      </section>

      <section class="card">
        <div class="card-body">
          <div style="font-weight:600; margin-bottom:6px;">Distribusi Skor</div>
          <canvas id="chartBar" height="240"></canvas>
        </div>
      </section>

      <section class="card">
        <div class="card-body">
          <div style="font-weight:600; margin-bottom:6px;">Insight Sederhana</div>
          <div class="small">Rata-rata 7 hari: <strong>${avg7 ? avg7.toFixed(2) : "-"}</strong> · 14 hari: <strong>${avg14 ? avg14.toFixed(2) : "-"}</strong></div>
          <div class="small">Streak pengisian: <strong>${streak} hari</strong></div>
          <div class="small muted">Kiat: isi di jam yang sama setiap hari untuk insight yang konsisten.</div>
        </div>
      </section>
    </div>
  `;

  // render chart jika Chart.js tersedia
  if (window.Chart) {
    const ctx1 = document.getElementById("chartLine").getContext("2d");
    new Chart(ctx1, {
      type: "line",
      data: {
        labels: last14.map(d=>d.date.slice(5)),
        datasets: [{ label: "Mood", data: last14.map(d=>d.score) }]
      },
      options: { scales:{ y:{ suggestedMin:0, suggestedMax:5, ticks:{ stepSize:1 }}}}
    });

    const counts = MOODS.map(m => last14.filter(d=>d.score===m.score).length);
    const ctx2 = document.getElementById("chartBar").getContext("2d");
    new Chart(ctx2, {
      type: "bar",
      data: {
        labels: MOODS.map(m=>m.label),
        datasets: [{ label: "Jumlah hari", data: counts }]
      }
    });
  } else {
    // fallback tanpa grafik
    document.getElementById("chartLine").replaceWith(fallbackBox("Grafik tidak tersedia (CDN diblok/offline)"));
    document.getElementById("chartBar").replaceWith(fallbackBox("Grafik tidak tersedia (CDN diblok/offline)"));
  }
}
function fallbackBox(text) {
  const d = document.createElement("div");
  d.className = "muted small";
  d.style.padding = "8px 0";
  d.textContent = text;
  return d;
}

function renderSettings() {
  views.settings.innerHTML = `
    <div class="grid-2">
      <section class="card">
        <div class="card-body">
          <div style="font-weight:600; margin-bottom:8px;">Profil</div>
          <label class="small muted">Nama</label>
          <input id="nameInput" class="input" placeholder="Nama panggilan" value="${state.name||""}">
          <div style="height:8px"></div>
          <label class="small muted">Mulai</label>
          <input id="startInput" type="date" class="date" value="${state.startedAt}">
        </div>
      </section>

      <section class="card">
        <div class="card-body">
          <div style="font-weight:600; margin-bottom:8px;">Preferensi</div>
          <div class="kpi"><span>Pengingat harian</span><button id="notifToggle" class="btn">${state.reminders?"ON":"OFF"}</button></div>
          <div style="height:8px"></div>
          <div class="kpi"><span>Tema gelap</span><button id="darkToggle" class="btn">${state.dark?"Aktif":"Nonaktif"}</button></div>
          <div style="height:8px"></div>
          <button id="resetBtn" class="btn">Mulai Ulang Program</button>
        </div>
      </section>
    </div>
  `;

  document.getElementById("nameInput").addEventListener("input", async (e)=>{
    state.name = e.target.value; save(state); await cloudSaveProfileName();
  });
  document.getElementById("startInput").addEventListener("change", async (e)=>{
    state.startedAt = e.target.value; save(state); await cloudSaveSettings(); render();
  });

  document.getElementById("darkToggle").addEventListener("click", async ()=>{
    state.dark = !state.dark; save(state);
    document.documentElement.classList.toggle("dark", state.dark);
    themeToggle.textContent = state.dark ? "☾ Gelap" : "☀︎ Terang";
    await cloudSaveSettings();
    renderSettings();
  });

  document.getElementById("notifToggle").addEventListener("click", async ()=>{
    const target = !state.reminders;
    if (target && "Notification" in window) {
      try {
        const perm = await Notification.requestPermission();
        if (perm === "granted") {
          state.reminders = true; save(state);
          new Notification("Pengingat mood", { body: "Jangan lupa update mood hari ini." });
        } else { state.reminders = false; save(state); }
      } catch { state.reminders = false; save(state); }
    } else {
      state.reminders = false; save(state);
    }
    await cloudSaveSettings();
    renderSettings(); updateNotifStatus();
  });

  document.getElementById("resetBtn").addEventListener("click", async ()=>{
    if (!confirm("Mulai ulang program 30 hari?")) return;
    state = defaultState(); save(state);
    await cloudSaveSettings();
    render();
  });
}

function updateNotifStatus() {
  notifStatus.textContent = (state.reminders ? "🔔 Pengingat aktif" : "🔔 Pengingat mati");
}

function render() {
  save(state);
  renderToday();
  renderProgram();
  renderJournal();
  renderStats();
  renderSettings();
  updateNotifStatus();
}

// initial render

render();