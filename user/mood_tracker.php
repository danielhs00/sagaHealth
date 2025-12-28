<?php
session_start();
require_once '../includes/koneksi.php';

// --- 1. SECURITY CHECK ---
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}

// Cek Paket Premium (Opsional: Aktifkan jika ingin membatasi akses)
if (isset($_SESSION['plan_type']) && $_SESSION['plan_type'] !== 'premium') {
    // header("Location: dashboard_basic.php"); 
    // exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$message = "";

// --- 2. HANDLE REQUEST (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // A. SIMPAN MOOD
    if (isset($_POST['action']) && $_POST['action'] === 'save_mood') {
        $mood_level = $_POST['mood_level'] ?? 0;
        $raw_note = $_POST['note'] ?? '';
        
        // Gabungkan Tugas Harian ke dalam Catatan agar tersimpan di DB tanpa ubah struktur tabel
        $tasks = isset($_POST['tasks']) ? implode(", ", $_POST['tasks']) : '';
        $final_note = $raw_note;
        if (!empty($tasks)) {
            $final_note .= " [Tugas Selesai: $tasks]";
        }

        if ($mood_level > 0) {
            $stmt = $conn->prepare("INSERT INTO mood_entries (user_id, mood_level, note) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $mood_level, $final_note);
            if ($stmt->execute()) {
                $message = "Mood berhasil dicatat! Program hari ini selesai.";
            } else {
                $message = "Gagal menyimpan data.";
            }
        } else {
            $message = "Silakan pilih emotikon mood Anda terlebih dahulu.";
        }
    }

    // B. RESET DATA (ZONA BAHAYA)
    if (isset($_POST['action']) && $_POST['action'] === 'reset_data') {
        $stmt = $conn->prepare("DELETE FROM mood_entries WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $message = "Semua riwayat mood berhasil dihapus. Program dimulai dari nol.";
        }
    }
}

// --- 3. AMBIL DATA DARI DATABASE ---
// Ambil semua data urut dari yang terlama (untuk Grafik & Program)
$query = "SELECT * FROM mood_entries WHERE user_id = $user_id ORDER BY created_at ASC";
$result = $conn->query($query);

$history = [];
$levels = [];
$dates = [];
$totalScore = 0;

while ($row = $result->fetch_assoc()) {
    $history[] = $row;
    $levels[] = $row['mood_level'];
    $dates[] = date('d/m', strtotime($row['created_at']));
    $totalScore += $row['mood_level'];
}

// Hitung Statistik Sederhana
$totalEntries = count($history);
$averageMood = $totalEntries > 0 ? round($totalScore / $totalEntries, 1) : 0;

// Logika Program 30 Hari
$currentDay = $totalEntries + 1;
if ($currentDay > 30) $currentDay = 30; 

// --- 4. HANDLE EXPORT JSON (GET) ---
if (isset($_GET['export']) && $_GET['export'] == 'json') {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="mood-history.json"');
    echo json_encode($history, JSON_PRETTY_PRINT);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>SagaHealth - Sehat Fisik & Mental</title>
    <link rel="icon" href="../assets/img/tittle.png" type="image/png">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <link rel="stylesheet" href="../assets/style/styles.css">
    <link rel="stylesheet" href="../user/style/mood_tracker.css">
</head>
<body>

    <?php include 'partials/header.php'; ?>

    <main class="mood-wrapper">
        
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1F2937; margin: 0;">Jurnal Kesehatan Mental</h1>
            <p style="color: #6B7280; margin-top: 5px;">Rekam jejak emosi dan progres harianmu.</p>
        </div>

        <?php if($message): ?>
            <div style="background: #D1FAE5; color: #065F46; padding: 15px; border-radius: 12px; margin-bottom: 20px; text-align: center; font-weight: bold; border: 1px solid #A7F3D0;">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('jurnal')"><i class="fas fa-pen-fancy"></i> Jurnal</button>
            <button class="tab-btn" onclick="switchTab('program')"><i class="fas fa-tasks"></i> Program 30 Hari</button>
            <button class="tab-btn" onclick="switchTab('statistik')"><i class="fas fa-chart-line"></i> Statistik</button>
            <button class="tab-btn" onclick="switchTab('pengaturan')"><i class="fas fa-cog"></i> Pengaturan</button>
        </div>

        <div id="jurnal" class="tab-content active">
            <div class="card">
                <h3 style="color: #014C63; margin-bottom: 5px;">Catatan Hari Ini</h3>
                <p style="color: #999; font-size: 0.9rem;">Bagaimana perasaanmu saat ini?</p>
                
                <form method="POST">
                    <input type="hidden" name="action" value="save_mood">
                    
                    <input type="hidden" name="mood_level" id="moodInput" required>
                    <div class="mood-options">
                        <div class="mood-opt c-1" onclick="selectMood(1, this)"><i class="fas fa-frown"></i><span>Buruk</span></div>
                        <div class="mood-opt c-2" onclick="selectMood(2, this)"><i class="fas fa-meh-rolling-eyes"></i><span>Kurang</span></div>
                        <div class="mood-opt c-3" onclick="selectMood(3, this)"><i class="fas fa-meh"></i><span>Biasa</span></div>
                        <div class="mood-opt c-4" onclick="selectMood(4, this)"><i class="fas fa-smile"></i><span>Baik</span></div>
                        <div class="mood-opt c-5" onclick="selectMood(5, this)"><i class="fas fa-laugh-beam"></i><span>Hebat</span></div>
                    </div>

                    <h4 style="font-size: 1rem; margin-bottom: 15px;">Target Harian (Mindfulness)</h4>
                    <div class="task-item">
                        <input type="checkbox" name="tasks[]" value="Meditasi 5 Menit" id="task1">
                        <label for="task1">🧘 Meditasi / Ibadah Khusyuk (5 Menit)</label>
                    </div>
                    <div class="task-item">
                        <input type="checkbox" name="tasks[]" value="Minum Air Putih" id="task2">
                        <label for="task2">💧 Minum 8 Gelas Air Putih</label>
                    </div>
                    <div class="task-item">
                        <input type="checkbox" name="tasks[]" value="Tidur Cukup" id="task3">
                        <label for="task3">😴 Tidur Sebelum Jam 23.00</label>
                    </div>

                    <h4 style="font-size: 1rem; margin-bottom: 10px; margin-top: 20px;">Refleksi Singkat</h4>
                    <textarea name="note" style="width: 100%; padding: 15px; border: 1px solid #E5E7EB; border-radius: 12px; font-family: inherit; resize: none;" rows="3" placeholder="Apa hal baik yang terjadi hari ini?"></textarea>

                    <button type="submit" style="width: 100%; background: #014C63; color: white; padding: 15px; border: none; border-radius: 12px; margin-top: 25px; font-weight: bold; cursor: pointer; transition: 0.3s; font-size: 1rem;">
                        <i class="fas fa-save"></i> Simpan Jurnal
                    </button>
                </form>
            </div>

            <div class="card">
                <h3 style="margin-bottom: 15px;">Riwayat Terakhir</h3>
                <?php 
                $recent = array_reverse($history);
                if(empty($recent)): echo "<p style='color:#999; text-align:center;'>Belum ada data jurnal.</p>"; endif;
                
                $limit=0;
                foreach($recent as $r): 
                    if($limit++ >= 3) break;
                    $emojiList = [1=>'😫', 2=>'😔', 3=>'😐', 4=>'🙂', 5=>'😁'];
                    $colorList = [1=>'#EF4444', 2=>'#F59E0B', 3=>'#6B7280', 4=>'#10B981', 5=>'#3B82F6'];
                ?>
                <div class="history-item">
                    <div class="history-left">
                        <div class="emoji-display"><?php echo $emojiList[$r['mood_level']]; ?></div>
                        <div>
                            <div style="font-weight: bold; color: <?php echo $colorList[$r['mood_level']]; ?>">
                                Level <?php echo $r['mood_level']; ?>
                            </div>
                            <div style="font-size: 0.9rem; color: #4B5563;">
                                <?php echo htmlspecialchars(mb_strimwidth($r['note'], 0, 50, "...")); ?>
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 0.8rem; color: #9CA3AF; text-align: right;">
                        <?php echo date('d M', strtotime($r['created_at'])); ?><br>
                        <?php echo date('H:i', strtotime($r['created_at'])); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="program" class="tab-content">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin:0;">Peta Perjalanan 30 Hari</h3>
                        <p style="margin:5px 0 0; color:#666; font-size:0.9rem;">Konsistensi adalah kunci kesehatan mental.</p>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 2rem; font-weight: bold; color: #014C63;"><?php echo $currentDay; ?></span>
                        <span style="color: #999;">/30</span>
                    </div>
                </div>

                <div class="program-grid">
                    <div class="phase-divider">Fase 1: Kesadaran Diri</div>
                    <?php for($i=1; $i<=10; $i++): 
                        $state = ($i < $currentDay) ? 'done' : (($i == $currentDay) ? 'current' : '');
                        $content = ($i < $currentDay) ? '<i class="fas fa-check"></i>' : $i;
                    ?>
                        <div class="day-box <?php echo $state; ?>"><?php echo $content; ?></div>
                    <?php endfor; ?>

                    <div class="phase-divider">Fase 2: Regulasi Emosi</div>
                    <?php for($i=11; $i<=20; $i++): 
                        $state = ($i < $currentDay) ? 'done' : (($i == $currentDay) ? 'current' : '');
                        $content = ($i < $currentDay) ? '<i class="fas fa-check"></i>' : $i;
                    ?>
                        <div class="day-box <?php echo $state; ?>"><?php echo $content; ?></div>
                    <?php endfor; ?>

                    <div class="phase-divider">Fase 3: Optimisme & Kebiasaan</div>
                    <?php for($i=21; $i<=30; $i++): 
                        $state = ($i < $currentDay) ? 'done' : (($i == $currentDay) ? 'current' : '');
                        $content = ($i < $currentDay) ? '<i class="fas fa-check"></i>' : $i;
                    ?>
                        <div class="day-box <?php echo $state; ?>"><?php echo $content; ?></div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <div id="statistik" class="tab-content">
            <div class="stats-summary">
                <div class="stat-box">
                    <h2><?php echo $totalEntries; ?></h2>
                    <p>Total Catatan</p>
                </div>
                <div class="stat-box">
                    <h2><?php echo $averageMood; ?> <span style="font-size:1rem; color:#999;">/5</span></h2>
                    <p>Rata-rata Mood</p>
                </div>
            </div>

            <div class="card">
                <h3>Tren Emosi</h3>
                <?php if(empty($history)): ?>
                    <div style="text-align: center; padding: 40px;">
                        <i class="fas fa-chart-area" style="font-size: 3rem; color: #E5E7EB; margin-bottom: 10px;"></i>
                        <p style="color: #9CA3AF;">Belum cukup data untuk menampilkan grafik.</p>
                    </div>
                <?php else: ?>
                    <canvas id="statsChart" height="200"></canvas>
                <?php endif; ?>
            </div>
        </div>

        <div id="pengaturan" class="tab-content">
            <div class="card">
                <h3><i class="fas fa-sliders-h"></i> Pengaturan Data</h3>
                <p style="color: #666; font-size: 0.9rem;">Kelola data privasi dan laporan Anda.</p>
                
                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                <div style="margin-bottom: 25px;">
                    <h4 style="margin-bottom: 10px;">Ekspor Laporan</h4>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px;">Unduh riwayat kesehatan mental Anda untuk arsip pribadi atau ditunjukkan ke profesional.</p>
                    
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <button onclick="downloadPDF()" style="background: #DC2626; color: white; padding: 12px 20px; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-file-pdf"></i> Unduh PDF
                        </button>
                        
                        <a href="?export=json" style="background: #059669; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; font-weight: bold; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-code"></i> Unduh JSON
                        </a>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">

                <div>
                    <h4 style="color: #EF4444; margin-bottom: 10px;">Zona Bahaya</h4>
                    <p style="font-size: 0.9rem; color: #666; margin-bottom: 15px;">Menghapus semua data mood secara permanen dan memulai ulang program dari Hari 1.</p>
                    <form method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN? Data yang dihapus tidak dapat dikembalikan.');">
                        <input type="hidden" name="action" value="reset_data">
                        <button type="submit" style="background: white; color: #EF4444; border: 1px solid #EF4444; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s;">
                            <i class="fas fa-trash-alt"></i> Hapus Semua Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </main>

    <?php include '../partials/footer.php'; ?>

    <div id="pdfTemplate" style="display: none;">
        <div style="padding: 30px; font-family: sans-serif; color: #333; width: 100%;">
            
            <div style="border-bottom: 3px solid #014C63; padding-bottom: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="color: #014C63; margin: 0; font-size: 26px;">Laporan Kesehatan Mental</h1>
                    <p style="margin: 5px 0 0; font-size: 14px; color: #666;">Generated by SagaHealth App</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-weight: bold; font-size: 16px;"><?php echo htmlspecialchars($user_name); ?></p>
                    <p style="margin: 5px 0 0; font-size: 12px; color: #666;">Tanggal Cetak: <?php echo date("d F Y"); ?></p>
                </div>
            </div>

            <div style="background: #F0F9FF; padding: 20px; border-radius: 10px; margin-bottom: 30px; border: 1px solid #BDE0FE;">
                <h3 style="margin-top: 0; margin-bottom: 15px; color: #014C63; font-size: 18px;">Ringkasan Program</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px; font-size: 14px;"><strong>Total Catatan:</strong> <?php echo count($history); ?> Entry</td>
                        <td style="padding: 5px; font-size: 14px;"><strong>Rata-rata Mood:</strong> <?php echo $averageMood; ?> / 5.0</td>
                        <td style="padding: 5px; font-size: 14px;"><strong>Status:</strong> <?php echo ($averageMood >= 3) ? '<span style="color:green">Stabil/Baik</span>' : '<span style="color:red">Perlu Perhatian</span>'; ?></td>
                    </tr>
                </table>
            </div>

            <h3 style="color: #014C63; font-size: 18px; border-bottom: 1px solid #ccc; padding-bottom: 8px; margin-bottom: 15px;">Riwayat Detail</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background-color: #014C63; color: white;">
                        <th style="padding: 10px; text-align: left; width: 20%;">Waktu</th>
                        <th style="padding: 10px; text-align: center; width: 15%;">Level</th>
                        <th style="padding: 10px; text-align: left; width: 65%;">Catatan & Tugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $pdfData = array_reverse($history); 
                    if(empty($pdfData)): echo '<tr><td colspan="3" style="padding:15px; text-align:center;">Data Kosong</td></tr>'; endif;
                    
                    $colors = [1=>'#EF4444', 2=>'#F59E0B', 3=>'#6B7280', 4=>'#10B981', 5=>'#3B82F6'];
                    $labels = [1=>'Buruk', 2=>'Kurang', 3=>'Biasa', 4=>'Baik', 5=>'Hebat'];

                    foreach($pdfData as $row): 
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; vertical-align: top;">
                            <?php echo date('d/m/Y', strtotime($row['created_at'])); ?><br>
                            <span style="color:#999"><?php echo date('H:i', strtotime($row['created_at'])); ?></span>
                        </td>
                        <td style="padding: 10px; text-align: center; vertical-align: top; font-weight: bold; color: <?php echo $colors[$row['mood_level']]; ?>;">
                            <?php echo $labels[$row['mood_level']]; ?><br>(<?php echo $row['mood_level']; ?>)
                        </td>
                        <td style="padding: 10px; vertical-align: top; line-height: 1.4;">
                            <?php echo htmlspecialchars($row['note'] ?: '-'); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="margin-top: 40px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 15px;">
                Dokumen ini digenerate secara otomatis oleh sistem SagaHealth.
            </div>
        </div>
    </div>

    <script>
        // 1. Tab Switching
        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            
            const btnIndex = {'jurnal':0, 'program':1, 'statistik':2, 'pengaturan':3};
            document.querySelectorAll('.tab-btn')[btnIndex[tabId]].classList.add('active');
        }

        // 2. Mood Select
        function selectMood(level, el) {
            document.getElementById('moodInput').value = level;
            document.querySelectorAll('.mood-opt').forEach(opt => opt.classList.remove('selected'));
            el.classList.add('selected');
        }

        // 3. PDF Export
        function downloadPDF() {
            const element = document.getElementById('pdfTemplate');
            element.style.display = 'block'; // Show temp
            
            const opt = {
                margin: 0.5,
                filename: 'Laporan-SagaHealth-<?php echo date("Y-m-d"); ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                element.style.display = 'none'; // Hide again
            });
        }

        // 4. Chart Render
        <?php if(!empty($history)): ?>
        const ctx = document.getElementById('statsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($dates); ?>,
                datasets: [{
                    label: 'Tingkat Mood',
                    data: <?php echo json_encode($levels); ?>,
                    borderColor: '#014C63',
                    backgroundColor: 'rgba(1, 76, 99, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#028C8B',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        min: 1, max: 5,
                        ticks: { stepSize: 1, callback: v => ['','Buruk','Kurang','Biasa','Baik','Hebat'][v] }
                    }
                },
                plugins: { legend: {display: false} }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>