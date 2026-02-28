<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$my_items = $stmt->fetchAll();

$stmt2 = $pdo->prepare("SELECT claims.*, items.title as item_title, items.type as item_type FROM claims JOIN items ON claims.item_id = items.id WHERE claims.user_id = ? ORDER BY claims.created_at DESC");
$stmt2->execute([$_SESSION['user_id']]);
$my_claims = $stmt2->fetchAll();
?>

<div style="max-width: 1000px; margin: 3rem auto;">
    <h2 style="margin-bottom: 2rem; color: var(--text-main);">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
    
    <div style="margin-bottom: 3rem;">
        <h3 style="margin-bottom: 1rem; color: var(--primary);">My Reported Items</h3>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if (count($my_items) > 0): ?>
                <?php foreach ($my_items as $item): ?>
                    <div class="card" style="padding: 1.5rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                            <span style="background: var(--<?php echo $item['type'] === 'lost' ? 'danger' : 'success'; ?>); color: white; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">
                                <?php echo htmlspecialchars($item['type']); ?>
                            </span>
                            <span style="font-size: 0.8rem; font-weight: 500; color: var(--text-muted);">
                                Status: <?php echo ucfirst(htmlspecialchars($item['status'])); ?>
                            </span>
                        </div>
                        <h4 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($item['title']); ?></h4>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">Posted on <?php echo date('M d, Y', strtotime($item['created_at'])); ?></p>
                        <a href="item_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 1rem;">View Details</a>
                    </div>
                <?php
    endforeach; ?>
            <?php
else: ?>
                <p style="color: var(--text-muted);">You haven't reported any items yet.</p>
            <?php
endif; ?>
        </div>
    </div>

    <div>
        <h3 style="margin-bottom: 1rem; color: var(--primary);">My Claims</h3>
        <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php if (count($my_claims) > 0): ?>
                <?php foreach ($my_claims as $claim): ?>
                    <div class="card" style="padding: 1.5rem;">
                        <h4 style="margin-bottom: 0.5rem;">Claim for: <?php echo htmlspecialchars($claim['item_title']); ?></h4>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;"><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($claim['status'])); ?></p>
                        <p style="font-size: 0.8rem; color: var(--text-muted);"><strong>Proof Provided:</strong><br><?php echo htmlspecialchars(substr($claim['proof_description'], 0, 50)); ?>...</p>
                    </div>
                <?php
    endforeach; ?>
            <?php
else: ?>
                <p style="color: var(--text-muted);">You haven't made any claims yet.</p>
            <?php
endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
