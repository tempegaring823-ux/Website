<?php
session_start();
require '../db/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: ../login.php");
    exit();
}


$current_anchor = 'dashboard';
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '#') !== false) {
    $current_anchor = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '#') + 1);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];
    $new_status = '';

    if ($action == 'approve') {
        $new_status = 'approved';
    } elseif ($action == 'decline') {
        $new_status = 'declined';
    }

    if ($new_status != '') {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $booking_id);
        $stmt->execute();
        $stmt->close();
        
        header("Location: dashboard.php#bookings");
        exit();
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $user_id_to_delete = $_POST['user_id'];
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id_to_delete);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php#users");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}



$users_sql = "SELECT users.id, users.name, users.email, roles.role_name, users.created_at FROM users JOIN roles ON users.role_id = roles.id";
$users_result = $conn->query($users_sql);

$bookings_sql = "SELECT bookings.*, users.name as user_name FROM bookings JOIN users ON bookings.user_id = users.id ORDER BY created_at DESC";
$bookings_result = $conn->query($bookings_sql);


$total_bookings_sql = "SELECT COUNT(*) as total FROM bookings";
$total_bookings_result = $conn->query($total_bookings_sql);
$total_bookings = $total_bookings_result->fetch_assoc()['total'];


$today_bookings_sql = "SELECT COUNT(*) as today FROM bookings WHERE DATE(created_at) = CURDATE()";
$today_bookings_result = $conn->query($today_bookings_sql);
$today_bookings = $today_bookings_result->fetch_assoc()['today'];

$total_users_sql = "SELECT COUNT(*) as total_users FROM users";
$total_users_result = $conn->query($total_users_sql);
$total_users = $total_users_result->fetch_assoc()['total_users'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Ahli Poles</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

    <div class="dashboard-container">
        <div class="sidebar">
            <h2 class="sidebar-logo">Admin Panel</h2>
            <nav class="sidebar-nav">
                <a href="#dashboard" class="nav-item <?php echo ($current_anchor == 'dashboard' ? 'active' : ''); ?>">Dashboard</a>
                <a href="#bookings" class="nav-item <?php echo ($current_anchor == 'bookings' ? 'active' : ''); ?>">Kelola Booking</a>
                <a href="#users" class="nav-item <?php echo ($current_anchor == 'users' ? 'active' : ''); ?>">Kelola Akun</a>
                <a href="hasil_sus.php" class="nav-item">Hasil SUS</a>
                <a href="../logout.php" class="nav-item logout">Logout</a>
            </nav>
        </div>

        <div class="main-content">
            <header class="main-header">
                <h1>Dashboard</h1>
            </header>

            <section id="dashboard" class="content-section <?php echo ($current_anchor == 'dashboard' ? 'active' : ''); ?>">
                <div class="summary-cards">
                    <div class="card">
                        <h3>Total Booking</h3>
                        <p class="card-number"><?php echo $total_bookings; ?></p>
                    </div>
                    <div class="card">
                        <h3>Booking Hari Ini</h3>
                        <p class="card-number"><?php echo $today_bookings; ?></p>
                    </div>
                    <div class="card">
                        <h3>Total Pengguna</h3>
                        <p class="card-number"><?php echo $total_users; ?></p>
                    </div>
                </div>
            </section>
            
            <section id="bookings" class="content-section <?php echo ($current_anchor == 'bookings' ? 'active' : ''); ?>">
                <h2>Daftar Booking Terbaru</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID Akun</th>
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
                                echo "<td data-label='ID Akun'>#" . $row['user_id'] . "</td>";
                                echo "<td data-label='Nama Pengguna'>" . htmlspecialchars($row['user_name']) . "</td>";
                                echo "<td data-label='Nomor Telepon'>" . htmlspecialchars($row['phone']) . "</td>";
                                echo "<td data-label='Layanan'>" . htmlspecialchars($row['service']) . "</td>";
                                echo "<td data-label='Tanggal Booking'>" . htmlspecialchars($row['booking_date']) . "</td>";
                                echo "<td data-label='Status' class='status-" . htmlspecialchars($row['status']) . "'>" . ucwords(htmlspecialchars($row['status'])) . "</td>";
                                echo "<td data-label='Aksi'>";
                                
                                if ($row['status'] == 'pending') {
                                    echo "<form action='dashboard.php' method='POST' class='action-form'>";
                                    echo "<input type='hidden' name='booking_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='approve'>";
                                    echo "<button type='submit' class='approve'>Terima</button>";
                                    echo "</form>";
                                                                
                                    echo "<form action='dashboard.php' method='POST' class='action-form'>";
                                    echo "<input type='hidden' name='booking_id' value='" . $row['id'] . "'>";
                                    echo "<input type='hidden' name='action' value='decline'>";
                                    echo "<button type='submit' class='decline'>Tolak</button>";
                                    echo "</form>";
                                                                
                                } else {
                                    echo "<span>Sudah diproses</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>Tidak ada data booking.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>

            <section id="users" class="content-section <?php echo ($current_anchor == 'users' ? 'active' : ''); ?>">
                <h2>Daftar Akun Pengguna</h2>
                <div style="margin-bottom: 20px;">
                    <a href="add_user.php" class='btn-action' style="background-color: #0A2342; color:white; padding: 10px 15px;">+ Tambah Akun</a>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Peran</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($users_result->num_rows > 0) {
                            while($row = $users_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='ID'>#" . $row['id'] . "</td>";
                                echo "<td data-label='Nama'>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td data-label='Email'>" . htmlspecialchars($row['email']) . "</td>";
                                echo "<td data-label='Peran'>" . htmlspecialchars($row['role_name']) . "</td>";
                                echo "<td data-label='Tanggal Daftar'>" . htmlspecialchars($row['created_at']) . "</td>";
                                echo "<td data-label='Aksi'>";

                                echo "<a href='edit_user.php?id=" . $row['id'] . "' class='btn-action edit'>Edit</a>";

                                echo "<form action='dashboard.php#users' method='POST' style='display:inline;' onsubmit='return confirm(\"Apakah Anda yakin ingin menghapus akun ini?\");'>";
                                echo "<input type='hidden' name='user_id' value='" . $row['id'] . "'>";
                                echo "<input type='hidden' name='delete_user' value='1'>";
                                echo "<button type='submit' class='btn-action delete'>Hapus</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Tidak ada data pengguna.</td></tr>";
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