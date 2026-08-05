<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = isset($_GET['msg']) ? $_GET['msg'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once '../Backend/config/database.php';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && $password == $user['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - SMP Al Islam Krian</title>
    <link rel="icon" type="image/png" sizes="64x64" href="../assets/logo/logo-smp-al-islam.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="login-card">
        <div class="logo">
            <img src="../assets/logo/logo-smp-al-islam.png" alt="Logo">
            <h4>SMP Al Islam <span>Krian</span></h4>
            <p>Panel Administrator</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success py-2"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-person"></i> Username
                    <span class="required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-icon"><i class="bi bi-person"></i></span>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-control"
                        placeholder="Masukkan username Anda"
                        required
                        autofocus
                        autocomplete="off"
                        readonly
                        onfocus="this.removeAttribute('readonly')" />
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-lock"></i> Password
                    <span class="required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-icon"><i class="bi bi-lock"></i></span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control"
                        placeholder="Masukkan password Anda"
                        required
                        autocomplete="new-password" />
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember" value="1" autocomplete="off" />
                    Ingat saya
                </label>
                <a href="#" class="forgot-link">Lupa password?</a>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </button>
        </form>

        <!-- ===== TOMBOL KEMBALI KE BERANDA ===== -->
        <div class="login-footer">
            <a href="../index.html" class="btn-back-home">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html>