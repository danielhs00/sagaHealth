// =========================================================
// 1. FUNGSI GLOBAL handleGoogleSignIn UNTUK GIS (FIXED REDIRECT & SESSION STORAGE)
// =========================================================
function handleGoogleSignIn(response) {
    if (response.credential) {
        const idToken = response.credential;

        // Kirim ID Token ke endpoint PHP (auth_functions.php) untuk verifikasi server-side
        fetch('../includes/auth_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'google_login', 
                id_token: idToken
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                
                // *** PERBAIKAN PENTING: UPDATE CLIENT-SIDE SESSION ***
                sessionStorage.setItem('isLoggedIn', 'true');
                sessionStorage.setItem('userId', data.user.id);
                sessionStorage.setItem('userName', data.user.name);
                sessionStorage.setItem('userEmail', data.user.email);
                
                // Tambahkan flag agar redirect langsung bekerja tanpa alert
                window.location.href = '../user/plan.php'; 
                
            } else {
                // Tampilkan alert hanya jika ada error
                console.error('Login Google Error:', data.message);
                alert('Gagal masuk dengan Google: ' + data.message); 
            }
        })
        .catch(error => {
            console.error('Error Koneksi:', error);
            alert('Terjadi kesalahan koneksi saat login Google.');
        });
    }
}


// app.js — Mood in 30 Days (vanilla JS) + Emoji fix

function initMoodTracker() {
  const viewToday = document.getElementById('view-today');
  const viewProgram = document.getElementById('view-program');
  const viewStats = document.getElementById('view-stats');
  const viewSettings = document.getElementById('view-settings');

  if (!viewToday || !viewProgram || !viewStats || !viewSettings) return;

  const LS_KEY = 'mood-tracker-vanilla';
  const MOOD_API = '../includes/mood_api.php';
  const todayISO = () => new Date().toISOString().slice(0, 10);
  const ENABLE_BACKUP = false; // kept for parity; nilai tidak digunakan

  const MOODS = [
    { score: 1, label: 'Buruk', icon: '🙁' },
    { score: 2, label: 'Kurang', icon: '😕' },
    { score: 3, label: 'Biasa', icon: '😐' },
    { score: 4, label: 'Baik', icon: '🙂' },
    { score: 5, label: 'Hebat', icon: '😁' },
  ];

  const PHASES = [
    { range: [1, 10], title: 'Kesadaran Diri', gradient: 'linear-gradient(90deg,#0284c7,#22d3ee)' },
    { range: [11, 20], title: 'Regulasi Emosi', gradient: 'linear-gradient(90deg,#6366f1,#8b5cf6)' },
    { range: [21, 30], title: 'Optimisme & Kebiasaan', gradient: 'linear-gradient(90deg,#10b981,#14b8a6)' },
  ];

  const pickPhase = (day) => PHASES.find((p) => day >= p.range[0] && day <= p.range[1]) || PHASES[0];

  function generateProgram() {
    const base = {
      'Kesadaran Diri': [
        'Tuliskan 3 hal yang kamu rasakan sekarang',
        'Tarik napas 4-4-6 selama 3 menit',
        'Jalan kaki ringan 5-10 menit',
        'Catat 1 pemicu emosi hari ini',
      ],
      'Regulasi Emosi': [
        'Reframe: ubah 1 pikiran negatif jadi netral',
        'Meditasi hening 5 menit',
        'Sapa teman/keluarga via chat',
        'Peregangan ringan 5 menit',
      ],
      'Optimisme & Kebiasaan': [
        'Tulis 3 hal yang kamu syukuri',
        'Rencanakan 1 hal kecil menyenangkan',
        'Latihan senyum 60 detik di cermin',
        'Beri diri sendiri apresiasi singkat',
      ],
    };

    return Array.from({ length: 30 }, (_, i) => {
      const day = i + 1;
      const phase = pickPhase(day);
      const shuffled = [...base[phase.title]].sort(() => Math.random() - 0.5);
      return { day, phase: phase.title, tasks: shuffled.slice(0, 3) };
    });
  }

  const defaultState = () => ({
    startedAt: todayISO(),
    name: '',
    dark: false,
    reminders: false,
    program: generateProgram(),
    moods: {},
  });

  function load() {
    try {
      return JSON.parse(localStorage.getItem(LS_KEY)) || null;
    } catch (err) {
      console.error('Gagal memuat data mood lokal', err);
      return null;
    }
  }

  const loadPrefs = () => {
    try {
      const stored = JSON.parse(localStorage.getItem(PREF_KEY)) || {};
      return {
        startedAt: stored.startedAt || todayISO(),
        name: stored.name || '',
        dark: Boolean(stored.dark),
        reminders: Boolean(stored.reminders),
      };
    } catch (err) {
      console.error('Gagal memuat preferensi mood lokal', err);
      return { startedAt: todayISO(), name: '', dark: false, reminders: false };
      }
  };

  const savePrefs = (prefs) => {
    try {
      localStorage.setItem(PREF_KEY, JSON.stringify(prefs));
    } catch (err) {
      console.error('Gagal menyimpan preferensi mood lokal', err);
    }
  };

  const persistPrefs = () =>
    savePrefs({
      startedAt: state.startedAt,
      name: state.name,
      dark: state.dark,
      reminders: state.reminders,
    });
  
  const prefs = loadPrefs();
  let state = { ...defaultState(), ...prefs, moods: {} };
  let syncTimer = null;

  const normalizeTasks = (list) => (Array.isArray(list) ? list.map((n) => Number(n)) : []);

  const dayIndexFrom = (startISO, refISO) => {
    const a = new Date(startISO);
    const b = new Date(refISO);
    const diff = Math.floor((b - a) / 86400000);
    return Math.min(29, Math.max(0, diff));
  };

  const avg = (arr) => (arr.length ? arr.reduce((a, b) => a + b, 0) / arr.length : 0);

  const streakDays = (moods) => {
    let c = 0;
    for (let i = 0; i < 365; i++) {
      const d = new Date();
      d.setDate(d.getDate() - i);
      const id = d.toISOString().slice(0, 10);
      if (moods[id]) c += 1; else break;
    }
    return c;
  };

  async function hydrateFromServer() {
    try {
      const res = await fetch(MOOD_API);
      const data = await res.json();
      if (data?.status === 'success' && Array.isArray(data.entries)) {
        const merged = { ...state.moods };
        data.entries.forEach((entry) => {
          merged[entry.date] = {
            score: Number(entry.score) || 0,
            note: entry.note || '',
            day: Number(entry.day) || dayIndexFrom(state.startedAt, entry.date) + 1,
            tasksDone: normalizeTasks(entry.tasksDone),
          };
        });
        state = { ...state, moods: merged };
        save(state);
        render();
      }
    } catch (err) {
      console.error('Gagal mengambil data mood dari server', err);
    }
  }


  async function persistMoodEntry(dateKey) {
    const entry = state.moods[dateKey];
    if (!entry) return;

    try {
      const res = await fetch(MOOD_API, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          date: dateKey,
          score: entry.score || 0,
          note: entry.note || '',
          day: entry.day || dayIndexFrom(state.startedAt, dateKey) + 1,
          tasksDone: entry.tasksDone || [],
        }),
      });

      const data = await res.json();
      if (data?.status === 'success' && Array.isArray(data.entries)) {
        const merged = { ...state.moods };
        data.entries.forEach((e) => {
          merged[e.date] = {
            score: Number(e.score) || 0,
            note: e.note || '',
            day: Number(e.day) || dayIndexFrom(state.startedAt, e.date) + 1,
            tasksDone: normalizeTasks(e.tasksDone),
          };
        });
        state = { ...state, moods: merged };
        save(state);
        render();
      }
    } catch (err) {
      console.error('Gagal menyimpan mood ke server', err);
    }
  
  }

  const queuePersistMood = (dateKey) => {
    clearTimeout(syncTimer);
    syncTimer = setTimeout(() => persistMoodEntry(dateKey), 350);
  };

  const views = {
    today: viewToday,
    program: viewProgram,
    stats: viewStats,
    settings: viewSettings,
  };

  const notifStatus = document.getElementById('notifStatus');
  const themeToggle = document.getElementById('themeToggle');


  const fallbackBoxHTML = (text) => `<div class="muted small" style="padding:8px 0;">${text}</div>`;
  const fallbackBoxElement = (text) => {
    const d = document.createElement('div');
    d.className = 'muted small';
    d.style.padding = '8px 0';
    d.textContent = text;
    return d;
  };

  document.documentElement.classList.toggle('dark', state.dark);
  if (themeToggle) {
    themeToggle.textContent = state.dark ? '☾ Gelap' : '☀︎ Terang';
    themeToggle.addEventListener('click', () => {
      state.dark = !state.dark;
      save(state);
      document.documentElement.classList.toggle('dark', state.dark);
      themeToggle.textContent = state.dark ? '☾ Gelap' : '☀︎ Terang';
    });
  }

  document.querySelectorAll('.tabs .tab').forEach((btn) => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.tabs .tab').forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      const target = btn.dataset.tab;
      Object.entries(views).forEach(([k, el]) => el.classList.toggle('active', k === target));
      render();
    });
  });

  function renderToday() {
    const idx = dayIndexFrom(state.startedAt, todayISO());
    const todayDay = state.program[idx] || { day: idx + 1, phase: pickPhase(idx + 1).title, tasks: [] };
    const key = todayISO();
    const today = state.moods[key] || { score: 0, note: '', day: idx + 1, tasksDone: [] };
    const progress = Math.round(((idx + 1) / 30) * 100);
    const phase = pickPhase(idx + 1);

    views.today.innerHTML = `
      <section class="card">
        <div class="card-head" style="background:${phase.gradient}">
          <div class="kpi">
            <div>Hari ${todayDay.day} · ${todayDay.phase}</div>
            <span class="badge">${progress}%</span>
          </div>
        </div>

        <div class="card-body">
          <div class="kpi">
            <span class="muted small">Progres 30 Hari</span>
            <span class="muted small">Mulai ${new Date(state.startedAt).toLocaleDateString()}</span>
          </div>
          <div class="progress" style="margin:8px 0 4px;"><div style="width:${progress}%"></div></div>
          <div class="muted small">Hari ke-${idx + 1} dari 30 · Fase: ${phase.title}</div>

          <div class="divider"></div>

          <div class="grid-2">
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

    const list = document.getElementById('taskList');
    list.innerHTML = todayDay.tasks
      .map((t, i) => {
        const checked = today.tasksDone.includes(i);
        return `<li style="margin:6px 0; display:flex; gap:8px; align-items:center;">
          <button data-i="${i}" class="btn ${checked ? 'primary' : ''}" style="width:36px">${checked ? '✓' : '·'}</button>
          <span class="${checked ? 'muted' : ''}">${t}</span>
        </li>`;
      })
      .join('');

    list.querySelectorAll('button').forEach((b) => {
      b.addEventListener('click', () => {
        const i = Number(b.dataset.i);
        const set = new Set(state.moods[key]?.tasksDone || []);
        if (set.has(i)) set.delete(i); else set.add(i);
        state.moods[key] = { ...(state.moods[key] || today), tasksDone: [...set] };
        save(state);
        render();
        queuePersistMood(key);
      });
    });

    const row = document.getElementById('moodRow');
    row.innerHTML = MOODS.map((m) => `
      <button class="mood-btn ${today.score === m.score ? 'active' : ''}" data-score="${m.score}">
        <span class="emoji">${m.icon}</span>
        <span class="label">${m.label}</span>
      </button>
    `).join('');

    row.querySelectorAll('button').forEach((btn) => {
      btn.addEventListener('click', () => {
        const score = Number(btn.dataset.score);
        state.moods[key] = { ...(state.moods[key] || today), score };
        save(state);
        render();
        queuePersistMood(key);
      });
    });

    const note = document.getElementById('note');
    note.value = today.note || '';
    note.addEventListener('input', () => {
      state.moods[key] = { ...(state.moods[key] || today), note: note.value };
      save(state);
      queuePersistMood(key);
    });
  }

  function renderProgram() {
    const idx = dayIndexFrom(state.startedAt, todayISO());
    views.program.innerHTML = `
      <div class="grid-3">
        ${state.program
          .map((item) => `
            <section class="card">
              <div class="card-head" style="background:${pickPhase(item.day).gradient}">
                <div>Hari ${item.day} · ${item.phase}</div>
                ${item.day === idx + 1 ? '<span class="badge">Hari ini</span>' : ''}
              </div>
              <div class="card-body">
                <ul class="list">
                  ${item.tasks.map((t) => `<li>${t}</li>`).join('')}
                </ul>
              </div>
            </section>
          `)
          .join('')}
      </div>
    `;
  }

  function renderStats() {
    const last14 = [];
    for (let i = 13; i >= 0; i--) {
      const d = new Date();
      d.setDate(d.getDate() - i);
      const id = d.toISOString().slice(0, 10);
      const e = state.moods[id];
      last14.push({ date: id, score: e?.score || 0 });
    }

    const avg7 = avg(last14.slice(-7).map((d) => d.score).filter(Boolean));
    const avg14 = avg(last14.map((d) => d.score).filter(Boolean));
    const streak = streakDays(state.moods);
    const hasEntries = Object.keys(state.moods).length > 0;

    views.stats.innerHTML = `
      <div class="grid-2">
        <section class="card">
          <div class="card-body">
            <div style="font-weight:600; margin-bottom:6px;">Trend 14 Hari</div>
            ${hasEntries ? '<canvas id="chartLine" height="240"></canvas>' : fallbackBoxHTML('Belum ada data mood, isi terlebih dahulu')}
          </div>
        </section>

        <section class="card">
          <div class="card-body">
            <div style="font-weight:600; margin-bottom:6px;">Distribusi Skor</div>
            ${hasEntries ? '<canvas id="chartBar" height="240"></canvas>' : fallbackBoxHTML('Belum ada data untuk ditampilkan.')}
          </div>
        </section>

        <section class="card">
          <div class="card-body">
            <div style="font-weight:600; margin-bottom:6px;">Insight Sederhana</div>
            <div class="small">Rata-rata 7 hari: <strong>${avg7 ? avg7.toFixed(2) : '-'}</strong> · 14 hari: <strong>${avg14 ? avg14.toFixed(2) : '-'}</strong></div>
            <div class="small">Streak pengisian: <strong>${streak} hari</strong></div>
            <div class="small muted">Kiat: isi di jam yang sama setiap hari untuk insight yang konsisten.</div>
          </div>
        </section>
      </div>
    `;

    if (window.Chart && hasEntries) {
      const lineCanvas = document.getElementById('chartLine');
      const barCanvas = document.getElementById('chartBar');

      if (lineCanvas) {
        const ctx1 = lineCanvas.getContext('2d');
        new Chart(ctx1, {
          type: 'line',
          data: {
            labels: last14.map((d) => d.date.slice(5)),
            datasets: [{ label: 'Mood', data: last14.map((d) => d.score) }],
          },
          options: { scales: { y: { suggestedMin: 0, suggestedMax: 5, ticks: { stepSize: 1 } } } },
        });
      }

      if (barCanvas) {
        const counts = MOODS.map((m) => last14.filter((d) => d.score === m.score).length);
        const ctx2 = barCanvas.getContext('2d');
        new Chart(ctx2, {
          type: 'bar',
          data: {
            labels: MOODS.map((m) => m.label),
            datasets: [{ label: 'Jumlah hari', data: counts }],
          },
        });
      }
    } else if (!window.Chart) {
      const line = document.getElementById('chartLine');
      const bar = document.getElementById('chartBar');
      if (line) line.replaceWith(fallbackBoxElement('Grafik tidak tersedia (CDN diblok/offline)'));
      if (bar) bar.replaceWith(fallbackBoxElement('Grafik tidak tersedia (CDN diblok/offline)'));
    }
  }

  function renderSettings() {
    views.settings.innerHTML = `
      <div class="grid-2">
        <section class="card">
          <div class="card-body">
            <div style="font-weight:600; margin-bottom:8px;">Profil</div>
            <label class="small muted">Nama</label>
            <input id="nameInput" class="input" placeholder="Nama panggilan" value="${state.name || ''}">
            <div style="height:8px"></div>
            <label class="small muted">Mulai</label>
            <input id="startInput" type="date" class="date" value="${state.startedAt}">
          </div>
        </section>

        <section class="card">
          <div class="card-body">
            <div style="font-weight:600; margin-bottom:8px;">Preferensi</div>
            <div class="kpi"><span>Pengingat harian</span><button id="notifToggle" class="btn">${state.reminders ? 'ON' : 'OFF'}</button></div>
            <div style="height:8px"></div>
            <div class="kpi"><span>Tema gelap</span><button id="darkToggle" class="btn">${state.dark ? 'Aktif' : 'Nonaktif'}</button></div>
            <div style="height:8px"></div>
            <button id="resetBtn" class="btn">Mulai Ulang Program</button>
          </div>
        </section>
      </div>
    `;

    const nameInput = document.getElementById('nameInput');
    const startInput = document.getElementById('startInput');
    const darkToggle = document.getElementById('darkToggle');
    const notifToggle = document.getElementById('notifToggle');
    const resetBtn = document.getElementById('resetBtn');

    if (nameInput) {
      nameInput.addEventListener('input', (e) => {
        state.name = e.target.value;
        save(state);
      });
    }

    if (startInput) {
      startInput.addEventListener('change', (e) => {
        state.startedAt = e.target.value;
        save(state);
        render();
      });
    }

    if (darkToggle) {
      darkToggle.addEventListener('click', () => {
        state.dark = !state.dark;
        save(state);
        document.documentElement.classList.toggle('dark', state.dark);
        if (themeToggle) themeToggle.textContent = state.dark ? '☾ Gelap' : '☀︎ Terang';
        renderSettings();
      });
    }

    if (notifToggle) {
      notifToggle.addEventListener('click', async () => {
        const target = !state.reminders;
        if (target && 'Notification' in window) {
          try {
            const perm = await Notification.requestPermission();
            if (perm === 'granted') {
              state.reminders = true;
              save(state);
              new Notification('Pengingat mood', { body: 'Jangan lupa update mood hari ini.' });
            } else {
              state.reminders = false;
              save(state);
            }
          } catch (err) {
            console.error('Gagal meminta izin notifikasi', err);
            state.reminders = false;
            save(state);
          }
        } else {
          state.reminders = false;
          save(state);
        }
        renderSettings();
        updateNotifStatus();
      });
    }

    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        if (!confirm('Mulai ulang program 30 hari?')) return;
        state = defaultState();
        save(state);
        render();
      });
    }
  }

  function updateNotifStatus() {
    if (notifStatus) {
      notifStatus.textContent = state.reminders ? '🔔 Pengingat aktif' : '🔔 Pengingat mati';
    }
  }

  function render() {
    save(state);
    renderToday();
    renderProgram();
    renderStats();
    renderSettings();
    updateNotifStatus();
  }

  render();
  hydrateFromServer();
}

document.addEventListener('DOMContentLoaded', initMoodTracker);


// =========================================================
// 2. LOGIC DOMContentLoaded (DIGABUNGKAN)
// =========================================================
document.addEventListener('DOMContentLoaded', function() {
    // === 2.1 MOBILE MENU TOGGLE (HAMBURGER) ===
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mainNavMenu = document.getElementById('main-nav-menu');

    if (mobileMenuToggle && mainNavMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            // Toggle class 'active' untuk menampilkan/menyembunyikan menu
            mainNavMenu.classList.toggle('active');

            // Mengubah ikon dari hamburger (fa-bars) menjadi close (fa-times)
            const icon = mobileMenuToggle.querySelector('i');
            if (mainNavMenu.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }
    // assets/js/plan.js
document.addEventListener('DOMContentLoaded', function() {
    // Asumsikan tombol di plan.php memiliki class 'select-plan-btn'
    document.querySelectorAll('.select-plan-btn').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();

            // Ambil data dari data attribute tombol
            const planName = this.dataset.planName || 'Plan Default';
            const price = this.dataset.price || '0';
            const planType = this.dataset.planType || 'monthly';
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

            try {
                const response = await fetch('../payment/create_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        plan_name: planName,
                        price: price,
                        plan_type: planType
                    })
                });

                const data = await response.json();

                if (data.status === 'success' && data.redirect_url) {
                    // Berhasil, arahkan pengguna ke halaman pembayaran DANA
                    window.location.href = data.redirect_url;
                } else {
                    alert('Pembayaran gagal diinisiasi: ' + (data.message || 'Error tidak diketahui.'));
                    console.error('Payment Initiation Error:', data);
                }

            } catch (error) {
                alert('Terjadi kesalahan koneksi saat memulai pembayaran.');
                console.error('Fetch Error:', error);
            } finally {
                this.disabled = false;
                this.innerHTML = 'Pilih Paket'; // Kembalikan teks asli (sesuaikan)
            }
        });
    });
});

    // === 2.2 IMAGE CAROUSEL (Poster) ===
    const carouselTrack = document.querySelector('.carousel-track');
    if (carouselTrack) {
        const carouselItems = document.querySelectorAll('.carousel-item');
        const prevButton = document.querySelector('.prev-button');
        const nextButton = document.querySelector('.next-button');
        const indicatorsContainer = document.querySelector('.carousel-indicators');

        let currentIndex = 0;
        // Asumsi semua item memiliki lebar yang sama (dihitung setelah DOM load)
        const itemWidth = carouselItems.length > 0 ? carouselItems[0].offsetWidth : 0;
        
        // Fungsi untuk menggeser carousel
        const moveToSlide = (targetIndex) => {
            if (carouselItems.length > 0) {
                // Mengatur properti transform untuk menggeser track
                carouselTrack.style.transform = `translateX(-${targetIndex * itemWidth}px)`;
                updateIndicators(targetIndex);
            }
        };

        // Fungsi untuk mengupdate indikator dot
        const updateIndicators = (targetIndex) => {
            document.querySelectorAll('.indicator-dot').forEach(dot => dot.classList.remove('active'));
            if (indicatorsContainer && indicatorsContainer.children[targetIndex]) {
                indicatorsContainer.children[targetIndex].classList.add('active');
            }
        };

        // Membuat indikator dot secara dinamis dan menambahkan event listener
        if (indicatorsContainer) {
            carouselItems.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.classList.add('indicator-dot');
                if (index === 0) {
                    dot.classList.add('active');
                }
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    moveToSlide(currentIndex);
                });
                indicatorsContainer.appendChild(dot);
            });
        }

        // Fungsionalitas Tombol Next
        if (nextButton) {
            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % carouselItems.length;
                moveToSlide(currentIndex);
            });
        }

        // Fungsionalitas Tombol Previous
        if (prevButton) {
            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
                moveToSlide(currentIndex);
            });
        }

        // Optional: Auto-play carousel
        if (carouselItems.length > 1) {
             setInterval(() => {
                currentIndex = (currentIndex + 1) % carouselItems.length;
                moveToSlide(currentIndex);
            }, 5000); // Ganti slide setiap 5 detik
        }
    }

    // === 2.3 TOGGLE VISIBILITAS PASSWORD ===
    // Target semua tombol dengan class .password-toggle-btn
    const passwordToggleButtons = document.querySelectorAll('.password-toggle-btn');

    passwordToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Asumsi: Input password adalah sibling (saudara) terdekat sebelum tombol
            // Dalam struktur Anda: <button> adalah sibling dari <input> yang di wrap oleh <div class="input-with-icon">
            // Kita perlu menargetkan input di dalam wrapper
            const wrapper = button.parentElement;
            const passwordInput = wrapper.querySelector('input[type="password"], input[type="text"]');
            const icon = button.querySelector('i');

            if (passwordInput && icon) {
                // Toggle tipe input antara 'password' dan 'text'
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle ikon mata
                if (type === 'text') {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash'); // Mata tertutup
                } else {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye'); // Mata terbuka
                }
            }
        });
    });
});