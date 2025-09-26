<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
require '../db/config.php';

$booking_data = null;
$message = '';

if (isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $booking_data = $result->fetch_assoc();
    } else {
        $message = "Pesanan tidak ditemukan atau Anda tidak memiliki akses.";
    }
    $stmt->close();
} else {
    $message = "ID pesanan tidak valid.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pesanan | Ahli Poles</title>
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
                <a href="dashboard.php" class="btn btn-contact">Dashboard</a>
            </div>
        </nav>
    </header>

    <section style="padding: 60px 0;">
        <div class="container status-container">
            
            <div style="margin-bottom: 25px;">
                <a href="dashboard.php" class="btn btn-secondary">&larr; Kembali</a>
            </div>
            <?php if ($booking_data): ?>
                <h2>Status Pesanan #<?php echo htmlspecialchars($booking_data['id']); ?></h2>
                <div class="status-info">
                    <p><span>Layanan:</span> <?php echo htmlspecialchars($booking_data['service']); ?></p>
                    <p><span>Tanggal Booking:</span> <?php echo htmlspecialchars($booking_data['booking_date']); ?></p>
                    <p><span>Catatan:</span> <?php echo nl2br(htmlspecialchars($booking_data['notes'])); ?></p>
                    <p><span>Status:</span> <span class="status-text status-<?php echo htmlspecialchars($booking_data['status']); ?>"><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($booking_data['status']))); ?></span></p>
                </div>
            <?php else: ?>
                <h2>Informasi Pesanan</h2>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>
        </div>
    </section>
    <script src="../js/script.js"></script>
</body>
</html>