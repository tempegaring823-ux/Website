<?php
session_start();
require '../db/config.php';


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 2) {
    header("Location: ../login.php");
    exit();
}

$message = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];
    $password = $_POST['password'];


    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    

    if ($check_result->num_rows > 0) {
       
        $message = "Error: Email ini sudah terdaftar. Silakan gunakan email lain.";
    } elseif (empty($name) || empty($email) || empty($password)) {
        
        $message = "Error: Nama, email, dan password harus diisi.";
    } else {
      
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $email, $hashed_password, $role_id);
        
        if ($stmt->execute()) {
            $message = "Akun pengguna berhasil ditambahkan.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna | Ahli Poles</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <h2 class="sidebar-logo">Admin Panel</h2>
            <nav class="sidebar-nav">
                <a href="dashboard.php#dashboard" class="nav-item">Dashboard</a>
                <a href="dashboard.php#bookings" class="nav-item">Kelola Booking</a>
                <a href="dashboard.php#users" class="nav-item active">Kelola Akun</a>
                <a href="../logout.php" class="nav-item logout">Logout</a>
            </nav>
        </div>
        <div class="main-content">
            <header class="main-header">
                <h1>Tambah Pengguna</h1>
            </header>
            <div class="edit-container">
                <?php if ($message): ?>
                    <p class="message-<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></p>
                <?php endif; ?>
                <h2>Formulir Tambah Akun</h2>
                <form action="add_user.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role_id">Peran</label>
                        <select id="role_id" name="role_id">
                            <option value="1">User</option>
                            <option value="2">Admin</option>
                            <option value="3">Teknisi</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div style="display: flex; gap: 10px;">
                    <a href="dashboard.php#users" style="display: block; width: 100%; padding: 15px; text-align: center; text-decoration: none;" class="btn-submit">Kembali</a>
                    <button type="submit" class="btn-submit">Tambah Akun</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</body>
</html>