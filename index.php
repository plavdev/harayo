<?php
require_once 'includes/db.php';
require_once 'includes/header.php';
?>

<div class="hero">
    <h1>Find What's Lost, Return What's Found</h1>
    <p>HARAYO is your central hub for recovering lost items and returning found belongings across the campus.</p>
    <div style="margin-top: 2rem;">
        <a href="search.php" class="btn btn-primary" style="margin-right: 1rem;">Search Items</a>
        <a href="post_item.php" class="btn btn-outline">Report an Item</a>
    </div>
</div>

<div style="margin-top: 4rem;">
    <h2 style="text-align: center; margin-bottom: 2rem;">Recently Listed Items</h2>
    <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <?php
try {
    $stmt = $pdo->query("SELECT * FROM items WHERE status IN ('pending', 'approved') ORDER BY created_at DESC LIMIT 3");
    if ($stmt->rowCount() > 0) {
        while ($item = $stmt->fetch()) {
            $typeClass = $item['type'] === 'lost' ? 'danger' : 'success';
            echo '<div class="card">';
            echo '<span style="background: var(--' . $typeClass . '); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase;">' . htmlspecialchars($item['type']) . '</span>';
            echo '<h3 style="margin: 1rem 0 0.5rem;">' . htmlspecialchars($item['title']) . '</h3>';
            echo '<p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.9rem;">' . htmlspecialchars(substr($item['description'], 0, 100)) . '...</p>';
            echo '<p style="font-size: 0.8rem; color: var(--text-muted);"><strong>Category:</strong> ' . htmlspecialchars($item['category']) . '</p>';
            echo '<p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;"><strong>Location:</strong> ' . htmlspecialchars($item['location']) . '</p>';
            echo '<a href="item_detail.php?id=' . $item['id'] . '" class="btn btn-outline" style="display: block; text-align: center;">View Details</a>';
            echo '</div>';
        }
    }
    else {
        echo '<p style="text-align: center; grid-column: 1 / -1; color: var(--text-muted);">No items found recently.</p>';
    }
}
catch (PDOException $e) {
    echo "<p>Could not load recent items.</p>";
}
?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
