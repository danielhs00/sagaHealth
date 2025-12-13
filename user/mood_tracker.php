<?php /*
 Single-file Mood Tracker Web App (PHP)
 - Inspired by "Six Pack in 30 Days" flow
 - React via CDN + Babel (for JSX), Tailwind via CDN
 - No server dependencies; all data in localStorage
 - Save this as mood_tracker.php and open in a PHP-capable server (or directly; PHP not required here)
*/ ?>
<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
  <meta name="description" content="Mood Tracker SagaHealth"> 
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🙂</text></svg>">
  <!-- Tailwind (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' };</script>
  <!-- React 18 + ReactDOM (UMD) -->
  <script crossorigin src="https://unpkg.com/react@18/umd/react.development.js"></script>
  <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.development.js"></script>
  <!-- Babel (for in-browser JSX) -->
  <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
  <!-- Recharts (UMD) for charts -->
  <script src="https://unpkg.com/recharts/umd/Recharts.min.js"></script>
  <!-- Framer Motion UMD -->
  <script src="https://unpkg.com/framer-motion/dist/framer-motion.umd.js"></script>
  <style>
    html, body, #root { height: 100%; }
    .card { @apply bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm; }
    .btn { @apply inline-flex items-center justify-center h-10 px-4 rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition; }
    .btn-primary { @apply bg-slate-900 text-white hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 border-transparent; }
    .badge { @apply text-xs px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800; }
    .progress { height: 12px; }
  </style>
</head>
<body class="h-full bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 text-slate-900 dark:text-slate-100">
  <div id="root"></div>

  <script type="text/babel">
    const { useEffect, useMemo, useState } = React;
    const { ResponsiveContainer, LineChart, Line, XAxis, YAxis, Tooltip, CartesianGrid, BarChart, Bar } = Recharts;
    const { motion, AnimatePresence } = window.framerMotion;

    const LS_KEY = 'mood-tracker-single';
    const todayISO = () => new Date().toISOString().slice(0,10);

    const moodScale = [
      { score: 1, label: 'Buruk', icon: '🙁' },
      { score: 2, label: 'Kurang', icon: '😕' },
      { score: 3, label: 'Biasa', icon: '😐' },
      { score: 4, label: 'Baik',  icon: '🙂' },
      { score: 5, label: 'Hebat', icon: '😁' },
    ];

    const PHASES = [
      { range: [1,10],  title: 'Kesadaran Diri',        gradient: 'from-sky-500 to-cyan-500' },
      { range: [11,20], title: 'Regulasi Emosi',        gradient: 'from-indigo-500 to-violet-500' },
      { range: [21,30], title: 'Optimisme & Kebiasaan', gradient: 'from-emerald-500 to-teal-500' },
    ];

    const pickPhase = (day) => PHASES.find(p => day >= p.range[0] && day <= p.range[1]) || PHASES[0];

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
      return Array.from({length:30}, (_,i)=>{
        const day = i+1; const phase = pickPhase(day);
        const shuffled = [...base[phase.title]].sort(()=>Math.random()-0.5);
        return { day, phase: phase.title, tasks: shuffled.slice(0,3) };
      });
    }

    const defaultState = () => ({
      startedAt: todayISO(),
      dark: false,
      reminders: true,
      name: '',
      program: generateProgram(),
      moods: {}, // 'YYYY-MM-DD': { score, note, day, tasksDone: [i] }
    });

    const load = () => { try { const j = localStorage.getItem(LS_KEY); return j? JSON.parse(j): null; } catch { return null; } };
    const save = (s) => { try { localStorage.setItem(LS_KEY, JSON.stringify(s)); } catch {} };

    const dayIndexFrom = (startISO, refISO) => {
      const a = new Date(startISO), b = new Date(refISO);
      const diff = Math.floor((b - a) / 86400000);
      return Math.min(29, Math.max(0, diff));
    };

    function Progress({value}){
      return (
        <div className="w-full bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden progress">
          <div style={{width: `${value}%`}} className="h-full bg-slate-900 dark:bg-white"></div>
        </div>
      );
    }

    function App(){
      const [state, setState] = useState(()=> load() ?? defaultState());
      const [tab, setTab] = useState('today');
      const [importErr, setImportErr] = useState('');

      useEffect(()=>{ save(state); document.documentElement.classList.toggle('dark', state.dark); }, [state]);

      const idxToday = dayIndexFrom(state.startedAt, todayISO());
      const todayDay = state.program[idxToday];
      const todayKey = todayISO();
      const todayMood = state.moods[todayKey] ?? { score:0, note:'', day: idxToday+1, tasksDone: [] };
      const phase = pickPhase(idxToday+1);
      const progress = ((idxToday+1)/30)*100;

      const moodEntries = useMemo(()=> Object.entries(state.moods).map(([date, v])=>({date,...v})).sort((a,b)=>a.date.localeCompare(b.date)), [state.moods]);
      const hasMoodEntries = moodEntries.length > 0;

      const last14 = useMemo(()=>{
        const days=[]; for(let i=13;i>=0;i--){ const d=new Date(); d.setDate(d.getDate()-i); const id=d.toISOString().slice(0,10); const e=state.moods[id]; days.push({ date:id, score:e?.score ?? 0 }); }
        return days;
      }, [state.moods]);

      const avg = (arr)=> arr.length? (arr.reduce((a,b)=>a+b,0)/arr.length): 0;
      const avg7 = useMemo(()=> avg(last14.slice(-7).map(d=>d.score).filter(Boolean)), [last14]);
      const avg14 = useMemo(()=> avg(last14.map(d=>d.score).filter(Boolean)), [last14]);

      const streak = useMemo(()=>{
        let c=0; for(let i=0;i<365;i++){ const d=new Date(); d.setDate(d.getDate()-i); const id=d.toISOString().slice(0,10); if(state.moods[id]) c++; else break; } return c;
      }, [state.moods]);

      const setTodayScore = (score)=> setState(s=>({ ...s, moods: { ...s.moods, [todayKey]: { ...(s.moods[todayKey] ?? {score:0, note:'', day: idxToday+1, tasksDone:[]}), score }}}));
      const setTodayNote = (note)=> setState(s=>({ ...s, moods: { ...s.moods, [todayKey]: { ...(s.moods[todayKey] ?? {score:0, note:'', day: idxToday+1, tasksDone:[]}), note }}}));
      const toggleTask = (i)=> setState(s=>{ const m = s.moods[todayKey] ?? { score:0, note:'', day: idxToday+1, tasksDone:[] }; const set = new Set(m.tasksDone); set.has(i)? set.delete(i): set.add(i); return { ...s, moods: { ...s.moods, [todayKey]: { ...m, tasksDone: [...set] } } }; });

      const resetProgram = ()=> { if(!confirm('Mulai ulang program 30 hari?')) return; setState(defaultState()); setTab('today'); };

      const goBackToDashboard = (e)=> {
        if (window.history.length > 1) {
          e.preventDefault();
          window.history.back();
        } else {
          window.location.href = 'dashboard_basic.php';
        }
      };

      const exportJSON = ()=>{
        const blob = new Blob([JSON.stringify(state,null,2)], {type:'application/json'});
        const url = URL.createObjectURL(blob); const a = document.createElement('a');
        a.href = url; a.download = `mood-tracker-export-${todayISO()}.json`; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
      };

      const importJSON = (file)=>{
        const reader = new FileReader(); reader.onload = ()=>{ try { const parsed = JSON.parse(String(reader.result)); if(!parsed || !parsed.program || !parsed.moods) throw new Error('Format tidak valid'); setState(parsed); setImportErr(''); } catch(e){ setImportErr('Gagal impor: '+(e?.message||'Error')); } }; reader.readAsText(file);
      };

      useEffect(()=>{
        if(state.reminders && 'Notification' in window){
          Notification.requestPermission().then(p=>{ if(p==='granted'){ /* optionally schedule */ } });
        }
      }, []);

      return (
        <div className="min-h-full">
          {/* Topbar */}
          <header className="sticky top-0 z-40 border-b border-slate-200 dark:border-slate-800 bg-white/70 dark:bg-slate-950/50 backdrop-blur">
            <div className="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className={`h-9 w-9 rounded-2xl grid place-items-center text-white shadow bg-gradient-to-br ${phase.gradient}`}>🫶</div>
                <div>
                  <div className="font-semibold leading-tight">Mood in 30 Days</div>
                  <div className="text-xs text-slate-500">Versi web single-file</div>
                </div>
              </div>
              <div className="flex items-center gap-4 text-sm">
                <a className="btn" href="dashboard_basic.php" onClick={goBackToDashboard}>
                Dashboard
                </a>
                <div className="hidden md:flex items-center gap-2"><span>🔔</span><span>{state.reminders? 'Pengingat aktif':'Pengingat mati'}</span></div>
                <button className="btn" onClick={()=> setState(s=>({...s, dark: !s.dark}))}>{state.dark? '☾ Gelap':'☀︎ Terang'}</button>
              </div>
            </div>
          </header>

          {/* Tabs */}
          <main className="max-w-6xl mx-auto px-4 py-6">
            <div className="grid grid-cols-4 gap-2 text-sm">
              {['today','program','stats','settings'].map((t,i)=> (
                <button key={t} className={`btn ${tab===t? 'btn-primary':''}`} onClick={()=> setTab(t)}>
                  {t==='today'&&'🏠 Hari ini'}
                  {t==='program'&&'📅 Program'}
                  {t==='stats'&&'📊 Statistik'}
                  {t==='settings'&&'⚙️ Pengaturan'}
                </button>
              ))}
            </div>

            {/* TODAY */}
            {tab==='today' && (
              <div className="grid md:grid-cols-3 gap-6 mt-6">
                <div className="md:col-span-2 space-y-6">
                  <section className="card overflow-hidden">
                    <div className={`px-6 py-4 text-white bg-gradient-to-r ${phase.gradient}`}>
                      <div className="flex items-center justify-between text-lg font-semibold">
                        <span>Hari {todayDay.day} · {todayDay.phase}</span>
                        <span className="badge text-slate-900 bg-white">{Math.round(progress)}%</span>
                      </div>
                    </div>
                    <div className="p-6 space-y-5">
                      <div>
                        <div className="text-sm text-slate-500">Tantangan harian</div>
                        <ul className="mt-2 space-y-2">
                          {todayDay.tasks.map((t, i)=>{
                            const checked = todayMood.tasksDone?.includes(i);
                            return (
                              <li key={i} className="flex items-center gap-3">
                                <button onClick={()=> toggleTask(i)} className={`h-6 w-6 rounded-full grid place-items-center border transition ${checked? 'bg-emerald-500 text-white border-emerald-500':'hover:bg-slate-100 dark:hover:bg-slate-800 border-slate-300 dark:border-slate-700'}`}>
                                  {checked? '✓':'·'}
                                </button>
                                <span className={checked? 'line-through text-slate-500':''}>{t}</span>
                              </li>
                            );
                          })}
                        </ul>
                      </div>

                      <div>
                        <div className="text-sm text-slate-500 mb-2">Mood hari ini</div>
                        <div className="grid grid-cols-4 gap-2">
                          {moodScale.map(m => (
                            <button key={m.score} className={`btn ${todayMood.score===m.score? 'btn-primary':''}`} onClick={()=> setTodayScore(m.score)}>{m.icon} {m.label}</button>
                          ))}
                        </div>
                      </div>

                      <div>
                        <div className="text-sm text-slate-500 mb-2">Catatan</div>
                        <textarea className="w-full h-28 p-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900" placeholder="Bagaimana harimu? Ada pemicu tertentu?" value={todayMood.note} onChange={(e)=> setTodayNote(e.target.value)}></textarea>
                      </div>
                    </div>
                  </section>

                  <section className="card">
                    <div className="px-6 py-4 font-semibold">Tips singkat</div>
                    <div className="px-6 pb-6 text-sm text-slate-600 dark:text-slate-300 space-y-2">
                      <p>• Hindari menilai emosi; cukup amati dan beri nama.</p>
                      <p>• Konsistensi > kesempurnaan. 10 menit setiap hari sudah bagus.</p>
                      <p>• Jika gejala berat/berkepanjangan, pertimbangkan konsultasi profesional.</p>
                    </div>
                  </section>
                </div>

                <div className="space-y-6">
                  <section className="card p-6">
                    <div className="font-semibold mb-3">Progres 30 Hari</div>
                    <div className="text-sm text-slate-500 mb-2">Dari {new Date(state.startedAt).toLocaleDateString()}</div>
                    <Progress value={progress} />
                    <div className="mt-2 text-sm">Hari ke-{idxToday+1} dari 30</div>
                    <div className="mt-1 text-xs text-slate-500">Fase: {phase.title}</div>
                  </section>

                  <section className="card p-6 text-sm space-y-2">
                    <div className="flex justify-between"><span>Rata-rata 7 hari</span><strong>{avg7? avg7.toFixed(1): '-'}</strong></div>
                    <div className="flex justify-between"><span>Rata-rata 14 hari</span><strong>{avg14? avg14.toFixed(1): '-'}</strong></div>
                    <div className="flex justify-between"><span>Streak harian</span><strong>{streak} hari</strong></div>
                  </section>

                  <section className="card p-6 space-y-3">
                    <div className="font-semibold">Ekspor / Impor</div>
                    <div className="flex gap-2">
                      <button className="btn btn-primary flex-1" onClick={exportJSON}>⬇️ Ekspor JSON</button>
                      <label className="btn flex-1 cursor-pointer">
                        ⬆️ Impor
                        <input type="file" accept="application/json" className="hidden" onChange={(e)=> e.target.files?.[0] && importJSON(e.target.files[0])} />
                      </label>
                    </div>
                    {importErr && <div className="text-sm text-red-500">{importErr}</div>}
                  </section>
                </div>
              </div>
            )}

            {/* PROGRAM */}
            {tab==='program' && (
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                {state.program.map(item=> (
                  <div key={item.day} className="card p-4">
                    <div className="flex items-center justify-between text-lg font-semibold">
                      <span>Hari {item.day} · {item.phase}</span>
                      {item.day===idxToday+1 && <span className="badge">Hari ini</span>}
                    </div>
                    <ul className="list-disc pl-5 mt-3 text-sm text-slate-600 dark:text-slate-300">
                      {item.tasks.map((t,i)=> <li key={i}>{t}</li>)}
                    </ul>
                  </div>
                ))}
              </div>
            )}

            {/* STATS */}
            {tab==='stats' && (
              <div className="grid lg:grid-cols-2 gap-6 mt-6">
                <section className="card p-4 h-[360px]">
                  <div className="font-semibold mb-2">Trend 14 Hari</div>
                  <div className="h-[300px]">
                  {hasMoodEntries ? (
                    <ResponsiveContainer width="100%" height="100%">
                      <LineChart data={last14} margin={{ top: 10, right: 16, left: 0, bottom: 0 }}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="date" tickFormatter={(v)=> v.slice(5)} />
                        <YAxis domain={[0,5]} allowDecimals={false} />
                        <Tooltip labelFormatter={(v)=> new Date(v).toLocaleDateString()} />
                        <Line type="monotone" dataKey="score" strokeWidth={2} dot={{ r: 3 }} />
                      </LineChart>
                    </ResponsiveContainer>
                  )}
                  </div>
                </section>

                <section className="card p-4 h-[360px]">
                  <div className="font-semibold mb-2">Distribusi Skor</div>
                  <div className="h-[300px]">
                  {hasMoodEntries ? (
                                        <ResponsiveContainer width="100%" height="100%">
                      <BarChart data={moodScale.map(m=>({ label:m.label, score:m.score, count:moodEntries.filter(e=>e.score===m.score).length }))}>
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="label" />
                        <YAxis allowDecimals={false} />
                        <Tooltip />
                        <Bar dataKey="count" />
                      </BarChart>
                    </ResponsiveContainer>
                  )}
                  </div>
                </section>

                <section className="card p-6">
                  <div className="font-semibold mb-2">Insight Sederhana</div>
                  <p>Rata-rata 7 hari: <strong>{avg7? avg7.toFixed(2): '-'}</strong> · 14 hari: <strong>{avg14? avg14.toFixed(2): '-'}</strong></p>
                  <p>Streak pengisian: <strong>{streak} hari</strong></p>
                  <p className="text-sm text-slate-500">Kiat: isi di jam yang sama setiap hari untuk insight yang konsisten.</p>
                  {!hasMoodEntries && <p className="text-xs text-slate-500 mt-2">Statistik akan muncul setelah ada minimal satu input mood.</p>}
                </section>
              </div>
            )}

            {/* SETTINGS */}
            {tab==='settings' && (
              <div className="grid md:grid-cols-2 gap-6 mt-6">
                <section className="card p-6 space-y-3">
                  <div className="font-semibold">Profil</div>
                  <div className="grid grid-cols-4 items-center gap-2">
                    <div className="col-span-1 text-sm text-slate-500">Nama</div>
                    <input className="col-span-3 h-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3" value={state.name} onChange={(e)=> setState(s=>({...s, name: e.target.value}))} placeholder="Nama panggilan" />
                  </div>
                  <div className="grid grid-cols-4 items-center gap-2">
                    <div className="col-span-1 text-sm text-slate-500">Mulai</div>
                    <input type="date" className="col-span-3 h-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3" value={state.startedAt} onChange={(e)=> setState(s=>({...s, startedAt: e.target.value}))} />
                  </div>
                </section>

                <section className="card p-6 space-y-3">
                  <div className="font-semibold">Preferensi</div>
                  <div className="flex items-center justify-between">
                    <div>
                      <div className="font-medium">Pengingat harian</div>
                      <div className="text-sm text-slate-500">Gunakan notifikasi browser</div>
                    </div>
                    <button className="btn" onClick={async()=>{
                      const v = !state.reminders; 
                      if(v && 'Notification' in window){ try{ const perm = await Notification.requestPermission(); if(perm==='granted'){ new Notification('Pengingat mood', { body:'Jangan lupa update mood hari ini.' }); } } catch{} }
                      setState(s=>({...s, reminders: v }));
                    }}>{state.reminders? 'ON':'OFF'}</button>
                  </div>

                  <div className="flex items-center justify-between">
                    <div>
                      <div className="font-medium">Tema gelap</div>
                      <div className="text-sm text-slate-500">Nyaman untuk malam hari</div>
                    </div>
                    <button className="btn" onClick={()=> setState(s=>({...s, dark: !s.dark}))}>{state.dark? 'Aktif':'Nonaktif'}</button>
                  </div>

                  <div className="pt-2 flex gap-2">
                    <button className="btn" onClick={resetProgram}>Mulai Ulang</button>
                  </div>
                </section>
              </div>
            )}
          </main>

          <footer className="max-w-6xl mx-auto px-4 py-10 text-center text-xs text-slate-500">
            Dibuat sebagai versi web single-file. Data disimpan lokal di peramban Anda.
          </footer>
        </div>
      );
    }

    ReactDOM.createRoot(document.getElementById('root')).render(<App/>);
  </script>
</body>
</html>