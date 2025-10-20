<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
require '../db/config.php';

$user_id = $_SESSION['user_id'];
$bookings_sql = "SELECT id, service, booking_date, created_at FROM bookings WHERE user_id = ? ORDER BY created_at DESC";

$stmt = $conn->prepare($bookings_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna | Ahli Poles</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <nav class="container nav-container">
            <a href="../index.html" class="logo">Ahli Poles</a>
            
            <button class="menu-toggle" id="menu-toggle" aria-label="Buka menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            
            <div class="nav-links" id="nav-links">
                <a href="../logout.php" class="btn btn-contact">Logout</a>
            </div>
        </nav>
    </header>
    <div class="dashboard-content">
        <h1>Selamat Datang, <?php echo $_SESSION['user_name']; ?>!</h1>
        <p>Kelola pesanan Anda di sini.</p>

        <!-- Tombol Booking dipindahkan ke dalam section -->
        <div style="margin-top: 20px;">
            <a href="booking.php" class="btn btn-primary">Booking Sekarang</a>
            <a href="pertanyaan_sus.php" class="btn btn-primary">Umpan Balik (Kuesioner SUS)</a>

        </div>

        <div class="user-bookings">
            <h2>Riwayat Pesanan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Tanggal Booking</th>
                        <th>Tanggal Pesan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($bookings_result->num_rows > 0) {
                        while ($row = $bookings_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['service']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
                            echo "<td>" . date('d-m-Y', strtotime($row['created_at'])) . "</td>";
                            echo "<td><a href='status.php?id=" . $row['id'] . "'>Lihat Status</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Anda belum memiliki riwayat pesanan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>