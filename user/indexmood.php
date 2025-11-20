<?php
// index.php — Mood in 30 Days (Vanilla JS)
?><!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>SagaHealth</title>
  <link rel="icon" href="../assets/img/tittle.png" type="image/png">
  <meta name="description" content="Mood Tracker SagaHealth">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🙂</text></svg>">

  <!-- Font sebaiknya dideklarasikan sebelum CSS agar cepat terpakai -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style/mood.css" />
</head>
<body class="page">
  <header class="topbar">
    <div class="container row between center">
      <div class="row center gap-12">
        <div class="logo">🫶</div>
        <div>
          <div class="title">Mood in 30 Days</div>
          <div class="subtitle">Versi web sederhana (HTML • CSS • JS • PHP)</div>
        </div>
      </div>
      <div class="row center gap-12">
        <div id="notifStatus" class="muted small">🔔 Pengingat mati</div>
        <button id="btnLogin" class="btn">Masuk / Simpan ke Cloud</button>
        <button id="themeToggle" class="btn">☀︎ Terang</button>
      </div>
    </div>
  </header>

  <main class="container">
    <!-- Tabs (5 item persis agar grid rapi) -->
    <nav class="tabs">
      <button data-tab="today" class="tab active">
        <span class="emoji">🏠</span><span class="label">Hari ini</span>
      </button>
      <button data-tab="program" class="tab">
        <span class="emoji">📅</span><span class="label">Program</span>
      </button>
      <button data-tab="journal" class="tab">
        <span class="emoji">📔</span><span class="label">Jurnal</span>
      </button>
      <button data-tab="stats" class="tab">
        <span class="emoji">📊</span><span class="label">Statistik</span>
      </button>
      <button data-tab="settings" class="tab">
        <span class="emoji">⚙️</span><span class="label">Pengaturan</span>
      </button>
    </nav>

    <!-- Views -->
    <section id="view-today" class="view active"></section>
    <section id="view-program" class="view"></section>
    <section id="view-journal" class="view"></section>
    <section id="view-stats" class="view"></section>
    <section id="view-settings" class="view"></section>
  </main>

  <footer class="container footer muted small">
    Dibuat sebagai mood tracker 30 hari. Data disimpan di <strong>localStorage</strong> browser Anda.
  </footer>

  <!-- (Opsional) Chart.js via CDN; kalau diblok/putus, grafik akan disembunyikan -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <!-- Supabase SDK -->
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <script>
    const SUPABASE_URL = "https://lurcyplmawbbfttnhegz.supabase.co";
    const SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imx1cmN5cGxtYXdiYmZ0dG5oZWd6Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjIzNTc2MjcsImV4cCI6MjA3NzkzMzYyN30.vGNGmoxHZbnrmEvmsM0ePhP0YxOEBOoM2LhQ8tjer8M";
    window.sb = supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
  </script>

  <!-- Script utama -->
  <script src="../assets/js/app.js"></script>
</body>
</html>
