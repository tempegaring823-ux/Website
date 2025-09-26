<?php
require 'db/config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $message = "Error: Password dan konfirmasi password tidak cocok.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role_id = 1;

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            $message = "Error: " . $conn->error;
        } else {
            $stmt->bind_param("sssi", $name, $email, $hashed_password, $role_id);
            if ($stmt->execute()) {
                session_start();
                $_SESSION['status'] = 'Akun berhasil dibuat. Silakan login.';
                header("Location: login.php");
                exit();
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Ahli Poles</title>
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
        <h2>Daftar Akun Baru</h2>
        <?php if ($message): ?>
            <p class="message-error"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Konfirmasi Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <button type="submit" class="btn-submit">Daftar Sekarang</button>
        </form>
        <p style="margin-top: 20px;">Sudah punya akun? <a href="login.php" style="color: #0A2342; font-weight: bold;">Masuk di sini</a></p>
    </div>
    <script src="js/script.js"></script>
</body>
</html>