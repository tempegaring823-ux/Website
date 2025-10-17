<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
require '../db/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $phone = $_POST['phone'];
    $service = $_POST['service'];
    
    $booking_datetime = $_POST['date'] . ' ' . $_POST['time'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, phone, service, booking_date, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $phone, $service, $booking_datetime, $notes);

    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Layanan | Ahli Poles</title>
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
                <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                <a href="../logout.php" class="btn btn-contact">Logout</a>
            </div>
        </nav>
    </header>
    <section class="booking-section">
        
        <div class="container booking-form-container">
            
            <div style="margin-bottom: 25px;">
                <a href="dashboard.php" class="btn btn-secondary">&larr; Kembali</a>
            </div>
            <h2 style="text-align: center;">Formulir Booking</h2>
            <?php if ($message): ?>
                <p style="color: #e74c3c; text-align: center;"><?php echo $message; ?></p>
            <?php endif; ?>
            <form action="booking.php" method="POST">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <p style="margin-top: 5px; font-weight: bold;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                </div>
                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="service">Pilih Layanan</label>
                    <select id="service" name="service" required>
                        <option value="">-- Pilih Layanan --</option>
                        <option value="poles-lampu">Poles Lampu</option>
                        <option value="salon-eksterior">Salon Eksterior</option>
                        <option value="salon-interior">Salon Interior</option>
                        <option value="paket-lengkap">Paket Lengkap (Poles + Salon)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date">Tanggal & Jam Booking</label>
                    <div class="datetime-picker-container">
                        <input type="date" id="date" name="date" required>
                        
                        <select id="time" name="time" required>
                            <option value="">-- Pilih Jam --</option>
                            <option value="09:00:00">09:00</option>
                            <option value="12:00:00">12:00</option>
                            <option value="15:00:00">15:00</option>
                        </select>
                        </div>
                </div>
                <div class="form-group">
                    <label for="notes">Catatan Tambahan (Opsional)</label>
                    <textarea id="notes" name="notes"></textarea>
                </div>
                <button type="submit" class="btn-submit">Booking Sekarang</button>
            </form>
        </div>
    </section>
    <script src="../js/script.js"></script>
</body>
</html>