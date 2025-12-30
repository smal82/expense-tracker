<?php
// backend/index.php - Login
session_start();
require_once '../config/database.php';

$error = '';

// Se già loggato, redirect al dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

// Gestione login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username && $password) {
        $stmt = $conn->prepare("SELECT * FROM expense_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username o password non corretti';
        }
    } else {
        $error = 'Compila tutti i campi';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>🔐 Area Admin</h1>
                <p class="text-muted">Accedi per gestire le spese</p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Accedi</button>
            </form>
            
            <div class="text-center mt-3">
                <a href="../index.php" class="text-muted" style="text-decoration: none; font-size: 14px;">← Torna al frontend</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($error): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Errore di accesso',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonColor: '#ef4444'
        });
    </script>
    <?php endif; ?>
</body>
</html>