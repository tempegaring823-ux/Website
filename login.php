<?php
session_start();
require 'db/config.php';
$message = '';
if (isset($_SESSION['status'])) {
    $message = $_SESSION['status'];
    unset($_SESSION['status']);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, name, password, role_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_role'] = $row['role_id'];
            if ($row['role_id'] == 2) {
                header("Location: admin/dashboard.php");
            } elseif ($row['role_id'] == 3) { // Tambahan untuk Teknisi
                header("Location: technician/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit();
        } else {
            $message = "Email atau password salah.";
        }
    } else {
        $message = "Email atau password salah.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Ahli Poles</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <nav class="container nav-container">
            <a href="index.html" class="logo">Ahli Poles</a>
            
            <button class="menu-toggle" id="menu-toggle" aria-label="Buka menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            
            <div class="nav-links" id="nav-links">
                <a href="index.html">Beranda</a>
                <a href="services.html">Layanan</a>
                <a href="gallery.html">Portofolio</a>
                <a href="login.php" class="btn btn-contact">Login</a>
            </div>
        </nav>
    </header>
    <div class="auth-form-container">
        <h2>Masuk ke Akun Anda</h2>
        <?php if ($message): ?>
            <p class="message-<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
            <p style="margin-top: 20px;">Belum punya akun? <a href="register.php" style="color: #0A2342; font-weight: bold;">Daftar sekarang</a></p>
        </form>
    </div>
    <script src="js/script.js"></script>
</body>
</html>