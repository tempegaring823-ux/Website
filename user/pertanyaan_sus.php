<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
require '../db/config.php';

$message = '';
$user_id = $_SESSION['user_id'];

// Cek apakah pengguna sudah mengisi kuesioner (menggunakan nama tabel: feedback_sus)
$check_sql = "SELECT id FROM feedback_sus WHERE user_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$check_stmt->close();

if ($check_result->num_rows > 0) {
    $message = "Anda sudah pernah mengisi kuesioner SUS. Terima kasih!";
    $disable_form = true;
} else {
    $disable_form = false;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && !$disable_form) {
    
    $score_sum = 0;
    $q = [];
    
    // Ambil jawaban (Q1 sampai Q10) dan hitung skor
    for ($i = 1; $i <= 10; $i++) {
        $q[$i] = (int)$_POST['q' . $i];
        
        // Logika Perhitungan SUS:
        if ($i % 2 !== 0) {
            // Ganjil (Positif): Score = Jawaban - 1
            $score_sum += ($q[$i] - 1);
        } else {
            // Genap (Negatif): Score = 5 - Jawaban
            $score_sum += (5 - $q[$i]);
        }
    }
    
    // Skor Akhir SUS = Total Sum * 2.5
    $final_sus_score = number_format($score_sum * 2.5, 2);

    // Insert data ke database
    $stmt = $conn->prepare("INSERT INTO feedback_sus (user_id, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, sus_score) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiiiiiiis", $user_id, $q[1], $q[2], $q[3], $q[4], $q[5], $q[6], $q[7], $q[8], $q[9], $q[10], $final_sus_score);

    if ($stmt->execute()) {
        $message = "Terima kasih! Kuesioner berhasil dikirimkan. Skor Anda: " . $final_sus_score;
        $disable_form = true;
    } else {
        $message = "Error: Gagal mengirim kuesioner. " . $stmt->error;
    }
    $stmt->close();
}

$sus_questions = [
    1 => "Saya pikir saya akan sering menggunakan sistem ini.",
    2 => "Saya menemukan sistem ini rumit.",
    3 => "Saya pikir sistem ini mudah digunakan.",
    4 => "Saya memerlukan dukungan teknis dari orang lain untuk menggunakan sistem ini.",
    5 => "Saya menemukan berbagai fungsi dalam sistem ini terintegrasi dengan baik.",
    6 => "Saya pikir sistem ini tidak konsisten.",
    7 => "Saya membayangkan bahwa kebanyakan orang akan belajar menggunakan sistem ini dengan sangat cepat.",
    8 => "Saya menemukan sistem ini sangat tidak canggung untuk digunakan.",
    9 => "Saya merasa sangat percaya diri menggunakan sistem ini.",
    10 => "Saya perlu belajar banyak hal sebelum saya bisa memulai dengan sistem ini.",
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner SUS | Ahli Poles</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .sus-form-container { max-width: 800px; margin: 40px auto; padding: 40px; background-color: #FFFFFF; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.07); }
        .question-group { margin-bottom: 25px; padding: 15px; border: 1px solid #eee; border-radius: 8px; }
        .question-group label { display: block; font-weight: 500; margin-bottom: 15px; color: #0A2342; font-size: 1.1em; }
        .radio-options { display: flex; justify-content: space-between; margin-top: 10px; }
        .radio-options div { text-align: center; }
        .radio-options input[type="radio"] { margin: 5px; }
        .radio-options span { font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <header class="main-header">
        <nav class="container nav-container">
            <a href="../index.html" class="logo">Ahli Poles</a>
            <div class="nav-links" id="nav-links">
                <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                <a href="../logout.php" class="btn btn-contact">Logout</a>
            </div>
        </nav>
    </header>
    <section class="booking-section">
        <div class="container sus-form-container">
            
            <div style="margin-bottom: 25px;">
                <a href="dashboard.php" class="btn btn-secondary">&larr; Kembali</a>
            </div>

            <h2>Kuesioner System Usability Scale (SUS)</h2>
            <p style="text-align: center; margin-bottom: 30px;">Berikan penilaian dari 1 (Sangat Tidak Setuju) sampai 5 (Sangat Setuju).</p>
            <?php if ($message): ?>
                <p class="message-<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <form action="pertanyaan_sus.php" method="POST">
                <?php foreach ($sus_questions as $q_num => $question): ?>
                    <div class="question-group">
                        <label><?php echo $q_num . ". " . $question; ?></label>
                        <div class="radio-options">
                            <span>Sangat Tidak Setuju</span>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div>
                                    <input type="radio" id="q<?php echo $q_num; ?>_<?php echo $i; ?>" name="q<?php echo $q_num; ?>" value="<?php echo $i; ?>" required <?php echo $disable_form ? 'disabled' : ''; ?>>
                                    <label for="q<?php echo $q_num; ?>_<?php echo $i; ?>"><?php echo $i; ?></label>
                                </div>
                            <?php endfor; ?>
                            <span>Sangat Setuju</span>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <button type="submit" class="btn-submit" <?php echo $disable_form ? 'disabled' : ''; ?>>Kirim Kuesioner</button>
            </form>
        </div>
    </section>
</body>
</html>