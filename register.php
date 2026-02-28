<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all fields.";
    }
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email is already registered.";
        }
        else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = "Registration successful! You can now sign in.";
            }
            else {
                $error = "An error occurred. Please try again later.";
            }
        }
    }
}
?>

<div style="max-width: 450px; margin: 4rem auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">Create an Account</h2>
        <?php if ($error): ?>
            <div class="alert" style="background: var(--danger); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php
endif; ?>
        <?php if ($success): ?>
            <div class="alert" style="background: var(--success); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php
endif; ?>
        <form method="POST" action="">
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirm Password</label>
                <input type="password" name="confirm_password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1.5rem;">Sign Up</button>
            <p style="text-align: center; font-size: 0.9rem;">
                Already have an account? <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Sign in</a>
            </p>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
