<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    }
    else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: " . ($user['role'] === 'admin' ? '/admin/' : '/harayo/'));
            exit();
        }
        else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<div style="max-width: 400px; margin: 4rem auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">Welcome Back</h2>
        <?php if ($error): ?>
            <div class="alert" style="background: var(--danger); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php
endif; ?>
        <form method="POST" action="">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1.5rem;">Sign In</button>
            <p style="text-align: center; font-size: 0.9rem;">
                Don't have an account? <a href="register.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Sign up</a>
            </p>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
