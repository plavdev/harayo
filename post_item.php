<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $category = $_POST['category'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $date = $_POST['date'];
    $location = trim($_POST['location']);

    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_dir = 'assets/images/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                $image_url = '/' . $upload_dir . $new_filename;
            }
        }
        else {
            $error = "Invalid image format.";
        }
    }

    if (empty($error)) {
        if (empty($title) || empty($description) || empty($date)) {
            $error = "Title, description, and date are required.";
        }
        else {
            $stmt = $pdo->prepare("INSERT INTO items (user_id, type, category, title, description, date, location, image_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            if ($stmt->execute([$_SESSION['user_id'], $type, $category, $title, $description, $date, $location, $image_url])) {
                $success = "Item reported successfully! It is pending admin approval.";
            }
            else {
                $error = "Failed to report item.";
            }
        }
    }
}
?>

<div style="max-width: 600px; margin: 4rem auto;">
    <div class="card">
        <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">Report an Item</h2>
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
        <form method="POST" action="" enctype="multipart/form-data">
            <div style="display: flex; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Type</label>
                    <select name="type" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none;">
                        <option value="lost">Lost</option>
                        <option value="found">Found</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Category</label>
                    <select name="category" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none;">
                        <option value="Electronics">Electronics</option>
                        <option value="Books">Books</option>
                        <option value="ID Cards">ID Cards</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title</label>
                <input type="text" name="title" required placeholder="e.g. Blue Backpack, iPhone 12" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Description</label>
                <textarea name="description" rows="4" required placeholder="Provide distinguishing details..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none; transition: border-color 0.3s; resize: vertical;" onfocus="this.style.borderColor='var(--primary)';" onblur="this.style.borderColor='var(--border-color)';"></textarea>
            </div>
<div style="display: flex; gap: 1rem; margin-bottom: 1.25rem;">
    <div style="flex: 1;">
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Date (Lost/Found)</label>
        <input 
            type="date" 
            name="date" 
            required 
            max="<?php echo date('Y-m-d'); ?>" 
            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none;" 
            onfocus="this.style.borderColor='var(--primary)';" 
            onblur="this.style.borderColor='var(--border-color)';"
        >
    </div>
    <div style="flex: 1;"> 
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Location</label>
        <input 
            type="text" 
            name="location" 
            placeholder="e.g. Library, Cafe" 
            style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; font-size: 1rem; outline: none;" 
            onfocus="this.style.borderColor='var(--primary)';" 
            onblur="this.style.borderColor='var(--border-color)';"
        >
    </div>
</div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Upload Image (Optional)</label>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px dashed var(--primary); border-radius: 0.5rem; background: rgba(99, 102, 241, 0.05);">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Item</button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
