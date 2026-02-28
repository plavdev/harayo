<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT items.*, users.name as poster_name FROM items JOIN users ON items.user_id = users.id WHERE items.id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    echo "<div style='text-align:center; padding: 4rem 0;'><h2>Item not found.</h2><a href='/search.php' class='btn btn-primary' style='margin-top:1rem;display:inline-block;'>Back to Search</a></div>";
    require_once 'includes/footer.php';
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_claim'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $proof = trim($_POST['proof_description']);
    if (empty($proof)) {
        $error = "Please provide proof of ownership.";
    }
    else {
        $stmt = $pdo->prepare("INSERT INTO claims (item_id, user_id, proof_description) VALUES (?, ?, ?)");
        try {
            if ($stmt->execute([$id, $_SESSION['user_id'], $proof])) {
                $success = "Claim submitted successfully. You will be notified once reviewed.";
            }
            else {
                $error = "Failed to submit claim.";
            }
        }
        catch (PDOException $e) {
            $error = "You have already claimed this or an error occurred.";
        }
    }
}
?>

<div style="max-width: 800px; margin: 3rem auto;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
            <div>
                <span style="background: var(--<?php echo $item['type'] === 'lost' ? 'danger' : 'success'; ?>); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; margin-bottom: 1rem; display: inline-block;">
                    <?php echo htmlspecialchars($item['type']); ?>
                </span>
                <h1 style="color: var(--text-main);"><?php echo htmlspecialchars($item['title']); ?></h1>
            </div>
            <span style="padding: 0.5rem 1rem; border-radius: 0.5rem; background: #f1f5f9; font-size: 0.9rem; font-weight: 500;">
                Status: <?php echo ucfirst(htmlspecialchars($item['status'])); ?>
            </span>
        </div>

        <?php if ($item['image_url']): ?>
            <div style="margin-bottom: 2rem; border-radius: 0.5rem; overflow: hidden; max-height: 400px; background: #eee; text-align:center;">
                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" style="max-width: 100%; max-height: 400px; object-fit: contain;">
            </div>
        <?php
endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <h3 style="margin-bottom: 0.5rem; color: var(--text-main);">Details</h3>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
                <p><strong>Date <?php echo ucfirst($item['type']); ?>:</strong> <?php echo htmlspecialchars($item['date']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                <p><strong>Posted By:</strong> <?php echo htmlspecialchars($item['poster_name']); ?></p>
                <p><strong>Posted On:</strong> <?php echo date('M d, Y', strtotime($item['created_at'])); ?></p>
            </div>
            <div>
                <h3 style="margin-bottom: 0.5rem; color: var(--text-main);">Description</h3>
                <p style="color: var(--text-muted); line-height: 1.6;"><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
            </div>
        </div>

        <?php if ($item['status'] !== 'claimed'): ?>
            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 2rem 0;">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $item['user_id'] && $item['type'] === 'found'): ?>
                <div style="background: rgba(99, 102, 241, 0.05); padding: 2rem; border-radius: 1rem; border: 1px solid rgba(99, 102, 241, 0.2);">
                    <h3 style="margin-bottom: 1rem; color: var(--primary);">Is this yours? Claim it!</h3>
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
        else: ?>
                        <form method="POST" action="">
                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Proof of Ownership</label>
                                <textarea name="proof_description" rows="3" required placeholder="Describe specific details only the owner would know (e.g., contents, screen damage, serial number)..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';"></textarea>
                            </div>
                            <button type="submit" name="submit_claim" class="btn btn-primary">Submit Claim</button>
                        </form>
                    <?php
        endif; ?>
                </div>
            <?php
    elseif (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $item['user_id']): ?>
                <p style="text-align: center; color: var(--text-muted); font-weight:500;">You posted this item.</p>
            <?php
    elseif (!isset($_SESSION['user_id'])): ?>
                <div style="text-align: center; padding: 2rem; background: rgba(0,0,0,0.02); border-radius:0.5rem;">
                    <p style="margin-bottom: 1rem; font-weight: 500;">Please sign in to interact with this post.</p>
                    <a href="login.php" class="btn btn-primary">Sign In</a>
                </div>
            <?php
    endif; ?>
        <?php
endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
