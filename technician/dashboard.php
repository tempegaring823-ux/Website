<?php
session_start();
require '../db/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 3) {
    header("Location: ../login.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];
    $new_status = '';

    if ($action == 'in_progress') {
        $new_status = 'in_progress';
    } elseif ($action == 'complete') {
        $new_status = 'completed';
    }

    if ($new_status != '') {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $booking_id);
        $stmt->execute();
        $stmt->close();
        
        header("Location: dashboard.php");
        exit();
    }
}

$bookings_sql = "SELECT bookings.*, users.name as user_name FROM bookings JOIN users ON bookings.user_id = users.id WHERE bookings.status IN ('approved', 'in_progress') ORDER BY created_at DESC";
$bookings_result = $conn->query($bookings_sql);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teknisi | Ahli Poles</title>
    <link rel="stylesheet" href="../css/admin.css"> <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

    <div class="dashboard-container">
        <div class="sidebar">
            <h2 class="sidebar-logo">Teknisi Panel</h2>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">Kelola Pesanan</a>
                <a href="../logout.php" class="nav-item logout">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <header class="main-header">
                <h1>Daftar Pesanan yang Perlu Dikerjakan</h1>
            </header>
            
            <section id="bookings" class="content-section active">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Nama Pengguna</th>
                            <th>Nomor Telepon</th>
                            <th>Layanan</th>
                            <th>Tanggal Booking</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($bookings_result->num_rows > 0) {
                            while($row = $bookings_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='ID Pesanan'>#" . $row['id'] . "</td>";
                                echo "<td data-label='Nama Pengguna'>" . htmlspecialchars($row['user_name']) . "</td>";
                                echo "<td data-label='Nomor Telepon'>" . htmlspecialchars($row['phone']) . "</td>";
                                echo "<td data-label='Layanan'>" . htmlspecialchars($row['service']) . "</td>";
                                echo "<td data-label='Tanggal Booking'>" . htmlspecialchars($row['booking_date']) . "</td>";
                                echo "<td data-label='Status' class='status-" . htmlspecialchars($row['status']) . "'>" . ucwords(str_replace('_', ' ', htmlspecialchars($row['status']))) . "</td>";
                                echo "<td data-label='Aksi'>";
                                
                                if ($row['status'] == 'approved') {
                                    echo "<form action='dashboard.php' method='POST' class='action-form'>";
                                    echo "<input type='hidden' name='booking_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='in_progress'>";
                                    echo "<button type='submit' class='in_progress'>Mulai Kerjakan</button>";
                                    echo "</form>";
                                
                                } elseif ($row['status'] == 'in_progress') {
                                    echo "<form action='dashboard.php' method='POST' class='action-form'>";
                                    echo "<input type='hidden' name='booking_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='complete'>";
                                    echo "<button type='submit' class='complete'>Selesai</button>";
                                    echo "</form>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>Tidak ada pesanan yang perlu dikerjakan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
<script src="../js/script.js"></script>
</body>
</html>