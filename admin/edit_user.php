<?php
session_start();
require '../db/config.php';

// Verifikasi: Pastikan pengguna sudah login dan merupakan admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: ../login.php");
    exit();
}

$user_id = null;
$user_data = null;
$message = '';

// Logika untuk menampilkan formulir (saat halaman pertama kali diakses)
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.id WHERE users.id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    } else {
        $message = "Pengguna tidak ditemukan.";
    }
    $stmt->close();
}

// Logika untuk memproses formulir (saat dikirim)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];

    // Update data pengguna
    if (!empty($password)) {
        // Update password jika ada input baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role_id = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $name, $email, $role_id, $hashed_password, $user_id);
    } else {
        // Jangan update password jika kosong
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role_id = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $email, $role_id, $user_id);
    }

    if ($stmt->execute()) {
        $message = "Data pengguna berhasil diperbarui.";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    
    // Setelah update, ambil kembali data terbaru
    $stmt = $conn->prepare("SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.id WHERE users.id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna | Ahli Poles</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2 class="sidebar-logo">Admin Panel</h2>
            <nav class="sidebar-nav">
                <a href="dashboard.php#dashboard" class="nav-item">Dashboard</a>
                <a href="dashboard.php#bookings" class="nav-item">Kelola Booking</a>
                <a href="dashboard.php#users" class="nav-item active">Kelola Akun</a>
                <a href="../logout.php" class="nav-item logout">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header class="main-header">
                <h1>Edit Pengguna</h1>
            </header>

            <div class="edit-container">
                <?php if ($message): ?>
                    <p class="message-<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></p>
                <?php endif; ?>

                <?php if ($user_data): ?>
                    <h2>Edit Data Pengguna: <?php echo htmlspecialchars($user_data['name']); ?></h2>
                    <form action="edit_user.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $user_data['id']; ?>">
                        
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role_id">Peran</label>
                            <select id="role_id" name="role_id">
                                <option value="1" <?php if ($user_data['role_id'] == 1) echo 'selected'; ?>>User</option>
                                <option value="2" <?php if ($user_data['role_id'] == 2) echo 'selected'; ?>>Admin</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="password">Password (kosongkan jika tidak ingin diubah)</label>
                            <input type="password" id="password" name="password">
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <a href="dashboard.php#users" style="display: block; width: 100%; padding: 15px; text-align: center; text-decoration: none;" class="btn-submit">Kembali</a>
                            <button type="submit" class="btn-submit">Simpan Perubahan</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p>Pengguna tidak ditemukan. <a href="dashboard.php">Kembali ke dasbor</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
