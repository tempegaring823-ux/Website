<?php
session_start();
require '../db/config.php';

// Verifikasi: Pastikan pengguna sudah login dan merupakan admin (role_id 2)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: ../login.php");
    exit();
}

$message = '';
$results_data = [];

// Query untuk mengambil semua hasil SUS dari tabel feedback_sus
$sql = "SELECT sr.*, u.name as user_name, u.email as user_email 
        FROM feedback_sus sr 
        JOIN users u ON sr.user_id = u.id 
        ORDER BY sr.created_at DESC";
$result = $conn->query($sql);

$total_submissions = $result->num_rows;
$total_sus_score_sum = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ambil skor SUS yang sudah tersimpan
        $total_sus_score_sum += $row['sus_score']; 
        $results_data[] = $row;
    }
}

// Hitung Skor Rata-rata Global
$average_sus_score = ($total_submissions > 0) ? number_format($total_sus_score_sum / $total_submissions, 2) : 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuesioner SUS | Admin Ahli Poles</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

    <div class="dashboard-container">
        <div class="sidebar">
            <h2 class="sidebar-logo">Admin Panel</h2>
            <nav class="sidebar-nav">
                <a href="dashboard.php#dashboard" class="nav-item">Dashboard</a>
                <a href="dashboard.php#bookings" class="nav-item">Kelola Booking</a>
                <a href="dashboard.php#users" class="nav-item">Kelola Akun</a>
                <a href="hasil_sus.php" class="nav-item active">Hasil SUS</a>
                <a href="../logout.php" class="nav-item logout">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <header class="main-header">
                <h1>Hasil Kuesioner System Usability Scale (SUS)</h1>
            </header>
            
            <div style="margin-bottom: 25px;">
                <a href="dashboard.php" class="btn-action" style="background-color: #f39c12; color:white; padding: 10px 15px; text-decoration: none;">&larr; Kembali ke Dashboard</a>
            </div>
            
            <section id="sus_summary" class="content-section active">
                <div class="summary-cards" style="margin-bottom: 50px;">
                    <div class="card" style="background-color: #f7f7f7;">
                        <h3>Total Responden</h3>
                        <p class="card-number"><?php echo $total_submissions; ?></p>
                    </div>
                    <div class="card" style="border-left: 5px solid #0A2342;">
                        <h3>Skor SUS Rata-rata</h3>
                        <p class="card-number" style="color: #FF7C00;"><?php echo $average_sus_score; ?></p>
                    </div>
                </div>

                <h2>Detail Setiap Respon</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pengguna</th>
                            <th>Tanggal Isi</th>
                            <th>Skor SUS</th>
                            <th>Jawaban (Q1-Q10)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_submissions > 0): ?>
                            <?php foreach ($results_data as $row): ?>
                                <tr>
                                    <td data-label='ID'>#<?php echo $row['id']; ?></td>
                                    <td data-label='Pengguna'><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td data-label='Tanggal Isi'><?php echo date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                                    <td data-label='Skor SUS' style="font-weight: bold; color: #0A2342;"><?php echo $row['sus_score']; ?></td>
                                    <td data-label='Jawaban (Q1-Q10)'>
                                        <?php 
                                            // Tampilkan jawaban berurutan
                                            $answers = [];
                                            for ($i = 1; $i <= 10; $i++) {
                                                $answers[] = $row['q' . $i];
                                            }
                                            echo implode(', ', $answers);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan='5'>Belum ada hasil kuesioner SUS.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</body>
</html>