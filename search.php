<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$search = $_GET['q'] ?? '';
$category = $_GET['category'] ?? '';
$type = $_GET['type'] ?? '';

$sql = "SELECT * FROM items WHERE status IN ('pending', 'approved')";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if (!empty($type)) {
    $sql .= " AND type = ?";
    $params[] = $type;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>

<div style="margin: 3rem 0;">
    <h2 style="text-align: center; margin-bottom: 2rem; color: var(--text-main);">Search Items</h2>
    
    <div class="card" style="margin-bottom: 3rem; padding: 1.5rem;">
        <form method="GET" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 200px;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Keywords</label>
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search title or description..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; outline: none;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Type</label>
                <select name="type" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; outline: none;">
                    <option value="">All Types</option>
                    <option value="lost" <?php if ($type === 'lost')
    echo 'selected'; ?>>Lost</option>
                    <option value="found" <?php if ($type === 'found')
    echo 'selected'; ?>>Found</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem;">Category</label>
                <select name="category" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; outline: none;">
                    <option value="">All Categories</option>
                    <option value="Electronics" <?php if ($category === 'Electronics')
    echo 'selected'; ?>>Electronics</option>
                    <option value="Books" <?php if ($category === 'Books')
    echo 'selected'; ?>>Books</option>
                    <option value="ID Cards" <?php if ($category === 'ID Cards')
    echo 'selected'; ?>>ID Cards</option>
                    <option value="Clothing" <?php if ($category === 'Clothing')
    echo 'selected'; ?>>Clothing</option>
                    <option value="Other" <?php if ($category === 'Other')
    echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Filter</button>
        </form>
    </div>

    <div class="grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item):
        $typeClass = $item['type'] === 'lost' ? 'danger' : 'success';
?>
                <div class="card">
                    <span style="background: var(--<?php echo $typeClass; ?>); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase;">
                        <?php echo htmlspecialchars($item['type']); ?>
                    </span>
                    <h3 style="margin: 1rem 0 0.5rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <?php if ($item['image_url']): ?>
                        <div style="height: 150px; overflow: hidden; border-radius: 0.5rem; margin-bottom: 1rem; background: #eee;">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php
        endif; ?>
                    <p style="color: var(--text-muted); margin-bottom: 1rem; font-size: 0.9rem;">
                        <?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?>...
                    </p>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;"><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;"><strong>Date:</strong> <?php echo htmlspecialchars($item['date']); ?></p>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;"><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                    <a href="item_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-outline" style="display: block; text-align: center;">View Details</a>
                </div>
            <?php
    endforeach; ?>
        <?php
else: ?>
            <p style="text-align: center; grid-column: 1 / -1; color: var(--text-muted); font-size: 1.2rem;">No items match your criteria.</p>
        <?php
endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
