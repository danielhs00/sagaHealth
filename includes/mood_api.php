<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/koneksi.php';

header('Content-Type: application/json');

function respond($status, $message, $data = []) {
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $data));
    exit;
}

// Pastikan tabel tersedia
$conn->query(
    "CREATE TABLE IF NOT EXISTS mood_entries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL DEFAULT 0,
        entry_date DATE NOT NULL,
        score TINYINT NOT NULL DEFAULT 0,
        note TEXT,
        day_number INT DEFAULT 0,
        tasks_done JSON DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_user_date (user_id, entry_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

function fetchEntries($conn, $userId)
{
    $stmt = $conn->prepare("SELECT entry_date, score, note, day_number, tasks_done FROM mood_entries WHERE user_id = ? ORDER BY entry_date DESC");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $tasks = json_decode($row['tasks_done'] ?? '[]', true);
        $rows[] = [
            'date' => $row['entry_date'],
            'score' => intval($row['score']),
            'note' => $row['note'] ?? '',
            'day' => intval($row['day_number']),
            'tasksDone' => is_array($tasks) ? $tasks : [],
        ];
    }
    $stmt->close();
    return $rows;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    respond('success', 'Data mood berhasil diambil.', ['entries' => fetchEntries($conn, $userId)]);
}

$input = json_decode(file_get_contents('php://input'), true);
$date = $input['date'] ?? '';
$score = intval($input['score'] ?? 0);
$note = trim($input['note'] ?? '');
$day = intval($input['day'] ?? 0);
$tasksDone = $input['tasksDone'] ?? [];

if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    respond('error', 'Tanggal tidak valid.');
}

$tasksJson = json_encode(array_values($tasksDone));

$stmt = $conn->prepare(
    "INSERT INTO mood_entries (user_id, entry_date, score, note, day_number, tasks_done)
     VALUES (?,?,?,?,?,?)
     ON DUPLICATE KEY UPDATE score=VALUES(score), note=VALUES(note), day_number=VALUES(day_number), tasks_done=VALUES(tasks_done), updated_at=CURRENT_TIMESTAMP"
);
$stmt->bind_param('isisis', $userId, $date, $score, $note, $day, $tasksJson);

if ($stmt->execute()) {
    $stmt->close();
    respond('success', 'Data mood tersimpan.', ['entries' => fetchEntries($conn, $userId)]);
}

$error = $stmt->error;
$stmt->close();
respond('error', 'Gagal menyimpan data: ' . $error);