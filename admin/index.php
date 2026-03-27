<?php
declare(strict_types=1);
require_once __DIR__ . '/common.php';

ensureAdminTables($conn);

$error = '';
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if (adminLogin($conn, $u, $p)) {
        header('Location: /admin');
        exit;
    }
    $error = 'Invalid admin credentials.';
}

if (!isAdminLoggedIn()) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stylique Admin Login</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="/stylique/admin/assets/admin.css">
    </head>
    <body class="admin-auth-bg">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card admin-card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="admin-brand mb-1">Stylique<span class="dot">.</span></h3>
                            <p class="text-muted mb-0">Admin Panel</p>
                        </div>

                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="post">
                            <input type="hidden" name="action" value="login">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button class="btn btn-admin w-100" type="submit"><i class="fas fa-right-to-bracket me-2"></i>Sign In</button>
                        </form>
                    </div>
                </div>
                <p class="text-center text-muted mt-3 small">Default: admin / admin123</p>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

$page = $_GET['page'] ?? 'products';
$allowedPages = ['products', 'stock', 'users', 'orders', 'contacts', 'subscribers', 'banners', 'settings'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'products';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save_product') {
            $id = intval($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $category = trim($_POST['category'] ?? 'special');
            $validCategories = ['special', 'men', 'women', 'collection'];
            if (!in_array($category, $validCategories, true)) {
                $category = 'special';
            }
            $price = (float) ($_POST['price'] ?? 0);
            $discount = (float) ($_POST['discount'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $image = '';

            if ($id > 0) {
                $stmt = $conn->prepare('SELECT image FROM products WHERE id = ?');
                $stmt->execute([$id]);
                $existing = $stmt->fetch();
                $image = (string) ($existing['image'] ?? '');
            }

            if (!empty($_FILES['image_file']['name'] ?? '')) {
                $up = cloudinaryUploadFile($_FILES['image_file'], 'product');
                if ($up && !empty($up['secure_url'])) {
                    $image = (string) $up['secure_url'];
                }
            }

            if ($image === '') {
                throw new RuntimeException('Please upload product image. Image URL is auto-generated from Cloudinary.');
            }

            if ($id > 0) {
                $stmt = $conn->prepare('UPDATE products SET name=?, description=?, category=?, price=?, discount=?, stock=?, image=? WHERE id=?');
                $stmt->execute([$name, $description, $category, $price, $discount, $stock, $image, $id]);
                $flash = 'Product updated.';
            } else {
                $stmt = $conn->prepare('INSERT INTO products (name, description, category, price, discount, stock, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $description, $category, $price, $discount, $stock, $image]);
                $flash = 'Product created.';
            }
            $page = 'products';
        }

        if ($action === 'delete_product') {
            try {
                // Soft delete: archive the product instead of removing it
                $stmt = $conn->prepare('UPDATE products SET is_active = FALSE WHERE id = ?');
                $stmt->execute([intval($_POST['id'] ?? 0)]);
                $flash = 'Product archived.';
            } catch (PDOException $e) {
                $error = 'Failed to archive product. ' . $e->getMessage();
            }
            $page = 'products';
        }

        if ($action === 'save_stock') {
            $id = intval($_POST['id'] ?? 0);
            $stock = max(0, (int) ($_POST['stock'] ?? 0));
            $stmt = $conn->prepare('UPDATE products SET stock = ? WHERE id = ?');
            $stmt->execute([$stock, $id]);
            $flash = 'Stock updated.';
            $page = 'stock';
        }

        if ($action === 'save_banner') {
            $id = intval($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $subtitle = trim($_POST['subtitle'] ?? '');
            $buttonText = trim($_POST['button_text'] ?? '');
            $buttonLink = trim($_POST['button_link'] ?? '#');
            $imageUrl = '';
            $sortOrder = (int) ($_POST['sort_order'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;

            if ($id > 0) {
                $stmt = $conn->prepare('SELECT image_url FROM home_banners WHERE id = ?');
                $stmt->execute([$id]);
                $existing = $stmt->fetch();
                $imageUrl = (string) ($existing['image_url'] ?? '');
            }

            if (!empty($_FILES['banner_file']['name'] ?? '')) {
                $up = cloudinaryUploadFile($_FILES['banner_file'], 'banner');
                if ($up && !empty($up['secure_url'])) {
                    $imageUrl = (string) $up['secure_url'];
                }
            }

            if ($imageUrl === '') {
                throw new RuntimeException('Please upload banner image. Image URL is auto-generated from Cloudinary.');
            }

            if ($id > 0) {
                $stmt = $conn->prepare('UPDATE home_banners SET title=?, subtitle=?, button_text=?, button_link=?, image_url=?, sort_order=?, is_active=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$title, $subtitle, $buttonText, $buttonLink, $imageUrl, $sortOrder, $isActive, $id]);
                $flash = 'Banner updated.';
            } else {
                $stmt = $conn->prepare('INSERT INTO home_banners (title, subtitle, button_text, button_link, image_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$title, $subtitle, $buttonText, $buttonLink, $imageUrl, $sortOrder, $isActive]);
                $flash = 'Banner created.';
            }
            $page = 'banners';
        }

        if ($action === 'delete_banner') {
            $stmt = $conn->prepare('DELETE FROM home_banners WHERE id = ?');
            $stmt->execute([intval($_POST['id'] ?? 0)]);
            $flash = 'Banner deleted.';
            $page = 'banners';
        }

        if ($action === 'update_order_status') {
            $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $stmt->execute([trim($_POST['status'] ?? 'pending'), intval($_POST['id'] ?? 0)]);
            $flash = 'Order status updated.';
            $page = 'orders';
        }

        if ($action === 'delete_order') {
            $stmt = $conn->prepare('DELETE FROM orders WHERE id = ?');
            $stmt->execute([intval($_POST['id'] ?? 0)]);
            $flash = 'Order deleted.';
            $page = 'orders';
        }

        if ($action === 'update_contact_status') {
            $stmt = $conn->prepare('UPDATE contact_submissions SET status = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([trim($_POST['status'] ?? 'read'), trim($_POST['id'] ?? '')]);
            $flash = 'Contact status updated.';
            $page = 'contacts';
        }

        if ($action === 'delete_contact') {
            $stmt = $conn->prepare('DELETE FROM contact_submissions WHERE id = ?');
            $stmt->execute([trim($_POST['id'] ?? '')]);
            $flash = 'Contact deleted.';
            $page = 'contacts';
        }

        if ($action === 'delete_subscriber') {
            $stmt = $conn->prepare('DELETE FROM subscribers WHERE id = ?');
            $stmt->execute([intval($_POST['id'] ?? 0)]);
            $flash = 'Subscriber deleted.';
            $page = 'subscribers';
        }

        if ($action === 'update_user') {
            $id = intval($_POST['id'] ?? 0);
            $stmt = $conn->prepare('UPDATE login_data SET name=?, email=?, phone=?, address=? WHERE id=?');
            $stmt->execute([
                trim($_POST['name'] ?? ''),
                trim($_POST['email'] ?? ''),
                trim($_POST['phone'] ?? ''),
                trim($_POST['address'] ?? ''),
                $id,
            ]);
            $flash = 'User updated.';
            $page = 'users';
        }

        if ($action === 'delete_user') {
            $stmt = $conn->prepare('DELETE FROM login_data WHERE id = ?');
            $stmt->execute([intval($_POST['id'] ?? 0)]);
            $flash = 'User deleted.';
            $page = 'users';
        }

        if ($action === 'change_admin_credentials') {
            $currentId = (int) (currentAdmin()['id'] ?? 0);
            $newUser = trim($_POST['username'] ?? 'admin');
            $newPass = trim($_POST['password'] ?? '');
            if ($currentId > 0 && $newPass !== '') {
                $stmt = $conn->prepare('UPDATE admin_users SET username=?, password_hash=?, updated_at=NOW() WHERE id=?');
                $stmt->execute([$newUser, password_hash($newPass, PASSWORD_DEFAULT), $currentId]);
                $stmt = $conn->prepare('SELECT * FROM admin_users WHERE id = ?');
                $stmt->execute([$currentId]);
                $_SESSION['admin_user_row'] = $stmt->fetch();
                $flash = 'Admin credentials updated.';
            }
            $page = 'settings';
        }

        if ($action === 'upload_only') {
            if (!empty($_FILES['media_file']['name'] ?? '')) {
                $up = cloudinaryUploadFile($_FILES['media_file'], 'manual');
                if ($up && !empty($up['secure_url'])) {
                    $flash = 'Uploaded URL: ' . $up['secure_url'];
                } else {
                    $error = 'Upload failed.';
                }
            }
            $page = 'settings';
        }
    } catch (Throwable $e) {
        $error = 'Action failed: ' . $e->getMessage();
    }
}

$stats = [
    'products' => (int) dbScalar($conn, 'SELECT COUNT(*) FROM products'),
    'orders' => (int) dbScalar($conn, 'SELECT COUNT(*) FROM orders'),
    'users' => (int) dbScalar($conn, 'SELECT COUNT(*) FROM login_data'),
    'contacts' => (int) dbScalar($conn, 'SELECT COUNT(*) FROM contact_submissions'),
    'revenue' => (float) dbScalar($conn, "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status <> 'cancelled'"),
];

$products = dbRows($conn, 'SELECT * FROM products WHERE is_active = TRUE ORDER BY id DESC LIMIT 200');
$users = dbRows($conn, 'SELECT * FROM login_data ORDER BY id DESC LIMIT 200');
$orders = dbRows($conn, 'SELECT * FROM orders ORDER BY created_at DESC LIMIT 200');
$contacts = dbRows($conn, 'SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT 200');
$subs = dbRows($conn, 'SELECT * FROM subscribers ORDER BY created_at DESC LIMIT 200');
$banners = dbRows($conn, 'SELECT * FROM home_banners ORDER BY sort_order ASC, id ASC');
$orderItemsAll = dbRows($conn, 'SELECT oi.order_id, oi.product_name, oi.quantity, oi.final_price, oi.item_total FROM order_items oi ORDER BY oi.order_id, oi.id');
$orderItemsMap = [];
foreach ($orderItemsAll as $oi) { $orderItemsMap[(int)$oi['order_id']][] = $oi; }

function pageActive(string $page, string $want): string { return $page === $want ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylique Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/stylique/admin/assets/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="admin-dashboard-bg">
<nav class="navbar navbar-expand-lg bg-light shadow-sm sticky-top">
    <div class="container-fluid px-4">
        <span class="navbar-brand admin-brand mb-0">Stylique<span class="dot">.</span> Admin</span>
        <div class="d-flex gap-2">
            <a href="/admin/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
        </div>
    </div>
</nav>

<main class="container-fluid p-4">
    <?php if ($flash !== ''): ?><div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-2"><div class="admin-stat-card"><p class="small text-muted mb-1">Products</p><h5><?php echo $stats['products']; ?></h5></div></div>
        <div class="col-6 col-lg-2"><div class="admin-stat-card"><p class="small text-muted mb-1">Orders</p><h5><?php echo $stats['orders']; ?></h5></div></div>
        <div class="col-6 col-lg-2"><div class="admin-stat-card"><p class="small text-muted mb-1">Users</p><h5><?php echo $stats['users']; ?></h5></div></div>
        <div class="col-6 col-lg-3"><div class="admin-stat-card"><p class="small text-muted mb-1">Revenue</p><h5>Rs <?php echo number_format($stats['revenue'], 2); ?></h5></div></div>
        <div class="col-6 col-lg-3"><div class="admin-stat-card"><p class="small text-muted mb-1">Contacts</p><h5><?php echo $stats['contacts']; ?></h5></div></div>
    </div>

    <ul class="nav nav-pills admin-nav mb-4">
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'products'); ?>" href="/admin?page=products">Products</a></li>
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'stock'); ?>" href="/admin?page=stock">Stock</a></li>
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'users'); ?>" href="/admin?page=users">Users</a></li>
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'orders'); ?>" href="/admin?page=orders">Orders</a></li>
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'contacts'); ?>" href="/admin?page=contacts">Contacts</a></li>
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'subscribers'); ?>" href="/admin?page=subscribers">Subscribers</a></li>
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'banners'); ?>" href="/admin?page=banners">Banners</a></li>
        <li class="nav-item"><a class="nav-link <?php echo pageActive($page,'settings'); ?>" href="/admin?page=settings">Settings</a></li>
    </ul>

    <?php if ($page === 'products'): ?>
        <div class="card admin-card border-0 shadow-sm mb-3"><div class="card-body">
            <h6 class="mb-3">Add / Edit Product</h6>
            <form method="post" enctype="multipart/form-data" class="row g-2">
                <input type="hidden" name="action" value="save_product">
                <input type="hidden" name="id" id="productId" value="0">
                <div class="col-12 small text-muted" id="productFormMode"></div>
                <div class="col-md-2"><input name="name" class="form-control" placeholder="Name" required></div>
                <div class="col-md-2">
                    <select name="category" class="form-select" required>
                        <option value="special" selected>special</option>
                        <option value="men">men</option>
                        <option value="women">women</option>
                        <option value="collection">collection</option>
                    </select>
                </div>
                <div class="col-md-1"><input name="price" type="number" step="0.01" class="form-control" placeholder="price"></div>
                <div class="col-md-1"><input name="discount" type="number" step="0.01" class="form-control" placeholder="disc"></div>
                <div class="col-md-1"><input name="stock" type="number" class="form-control" placeholder="stock"></div>
                <div class="col-md-2"><input type="file" name="image_file" class="form-control" id="productImageFile" accept="image/*"></div>
                <div class="col-12"><textarea name="description" class="form-control" id="productDescription" placeholder="description"></textarea></div>
                <div class="col-12"><button class="btn btn-admin" id="productSaveBtn">Save Product</button> <button type="button" class="btn btn-outline-secondary" id="productResetBtn" style="display:none;">Cancel Edit</button></div>
            </form>
        </div></div>
        <div class="card admin-card border-0 shadow-sm"><div class="card-body table-responsive">
            <table class="table table-sm align-middle"><thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Image</th><th>Delete</th></tr></thead><tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo (int)$p['id']; ?></td><td><?php echo htmlspecialchars($p['name']); ?></td><td><?php echo htmlspecialchars($p['category']); ?></td><td><?php echo htmlspecialchars((string)$p['price']); ?></td>
                    <td><?php if (!empty($p['image'])): ?><a href="<?php echo htmlspecialchars($p['image']); ?>" target="_blank">view</a><?php endif; ?></td>
                    <td><button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editProduct(<?php echo htmlspecialchars(json_encode($p)); ?>)">Edit</button><form method="post" onsubmit="return confirm('Delete product?')" style="display:inline;"><input type="hidden" name="action" value="delete_product"><input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    <?php endif; ?>

    <?php if ($page === 'users'): ?>
        <div class="card admin-card border-0 shadow-sm"><div class="card-body table-responsive">
            <table class="table table-sm align-middle"><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Save</th><th>Delete</th></tr></thead><tbody>
            <?php foreach ($users as $u): ?>
                <tr><form method="post">
                    <input type="hidden" name="action" value="update_user"><input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>">
                    <td><?php echo (int)$u['id']; ?></td>
                    <td><input name="name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($u['name']); ?>"></td>
                    <td><input name="email" class="form-control form-control-sm" value="<?php echo htmlspecialchars($u['email']); ?>"></td>
                    <td><input name="phone" class="form-control form-control-sm" value="<?php echo htmlspecialchars((string)($u['phone'] ?? '')); ?>"></td>
                    <td><input name="address" class="form-control form-control-sm" value="<?php echo htmlspecialchars((string)($u['address'] ?? '')); ?>"></td>
                    <td><button class="btn btn-sm btn-outline-primary">Save</button></td>
                </form>
                    <td><form method="post" onsubmit="return confirm('Delete user?')"><input type="hidden" name="action" value="delete_user"><input type="hidden" name="id" value="<?php echo (int)$u['id']; ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    <?php endif; ?>

    <?php if ($page === 'orders'): ?>
        <div class="card admin-card border-0 shadow-sm"><div class="card-body table-responsive">
            <table class="table table-sm align-middle"><thead><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Date</th><th>View</th><th>Save</th><th>Delete</th></tr></thead><tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?php echo (int)$o['id']; ?></td><td><?php echo htmlspecialchars($o['full_name']); ?></td><td>Rs <?php echo htmlspecialchars((string)$o['total_amount']); ?></td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="action" value="update_order_status"><input type="hidden" name="id" value="<?php echo (int)$o['id']; ?>">
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach (['pending','shipped','delivered','cancelled'] as $st): ?>
                                    <option value="<?php echo $st; ?>" <?php echo ($o['status']===$st?'selected':''); ?>><?php echo ucfirst($st); ?></option>
                                <?php endforeach; ?>
                            </select>
                    </td>
                    <td><?php echo htmlspecialchars((string)$o['created_at']); ?></td>
                    <td><button type="button" class="btn btn-sm btn-outline-info" onclick="viewOrder(<?php echo htmlspecialchars(json_encode(['order'=>$o,'items'=>$orderItemsMap[(int)$o['id']]??[]])); ?>)"><i class="fas fa-eye"></i></button></td>
                    <td><button class="btn btn-sm btn-outline-primary">Save</button></form></td>
                    <td><form method="post" onsubmit="return confirm('Delete order?')"><input type="hidden" name="action" value="delete_order"><input type="hidden" name="id" value="<?php echo (int)$o['id']; ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    <?php endif; ?>

    <?php if ($page === 'contacts'): ?>
        <div class="card admin-card border-0 shadow-sm"><div class="card-body table-responsive">
            <table class="table table-sm align-middle"><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>View</th><th>Save</th><th>Delete</th></tr></thead><tbody>
            <?php foreach ($contacts as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['name']); ?></td><td><?php echo htmlspecialchars($c['email']); ?></td><td><?php echo htmlspecialchars($c['subject']); ?></td>
                    <td><form method="post" class="d-flex gap-2"><input type="hidden" name="action" value="update_contact_status"><input type="hidden" name="id" value="<?php echo htmlspecialchars($c['id']); ?>">
                        <select name="status" class="form-select form-select-sm"><?php foreach (['new','read','replied'] as $st): ?><option value="<?php echo $st; ?>" <?php echo ($c['status']===$st?'selected':''); ?>><?php echo ucfirst($st); ?></option><?php endforeach; ?></select>
                    </td>
                    <td><button type="button" class="btn btn-sm btn-outline-info" onclick="viewContact(<?php echo htmlspecialchars(json_encode($c)); ?>)"><i class="fas fa-eye"></i></button></td>
                    <td><button class="btn btn-sm btn-outline-primary">Save</button></form></td>
                    <td><form method="post" onsubmit="return confirm('Delete contact?')"><input type="hidden" name="action" value="delete_contact"><input type="hidden" name="id" value="<?php echo htmlspecialchars($c['id']); ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    <?php endif; ?>

    <?php if ($page === 'subscribers'): ?>
        <div class="card admin-card border-0 shadow-sm"><div class="card-body table-responsive">
            <table class="table table-sm align-middle"><thead><tr><th>ID</th><th>Email</th><th>Date</th><th>Delete</th></tr></thead><tbody>
            <?php foreach ($subs as $s): ?>
                <tr>
                    <td><?php echo (int)$s['id']; ?></td><td><?php echo htmlspecialchars($s['email']); ?></td><td><?php echo htmlspecialchars((string)$s['created_at']); ?></td>
                    <td><form method="post" onsubmit="return confirm('Delete subscriber?')"><input type="hidden" name="action" value="delete_subscriber"><input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    <?php endif; ?>

    <?php if ($page === 'stock'): ?>
        <div class="card admin-card border-0 shadow-sm"><div class="card-body table-responsive">
            <h6 class="mb-3">Stock Management</h6>
            <?php if (empty($products)): ?>
                <p class="text-muted">No products found. Add products first.</p>
            <?php else: ?>
            <table class="table table-sm align-middle"><thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Current Stock</th><th>Set Stock</th><th>Save</th></tr></thead><tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <form method="post">
                        <input type="hidden" name="action" value="save_stock">
                        <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                        <td><?php echo (int)$p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo htmlspecialchars($p['category']); ?></td>
                        <td><span class="badge <?php echo (int)$p['stock'] <= 5 ? 'bg-danger' : ((int)$p['stock'] <= 20 ? 'bg-warning text-dark' : 'bg-success'); ?>"><?php echo (int)$p['stock']; ?></span></td>
                        <td><input type="number" name="stock" class="form-control form-control-sm" value="<?php echo (int)$p['stock']; ?>" min="0" style="width:90px;"></td>
                        <td><button class="btn btn-sm btn-outline-primary">Update</button></td>
                    </form>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
            <?php endif; ?>
        </div></div>
    <?php endif; ?>

    <?php if ($page === 'banners'): ?>
        <div class="card admin-card border-0 shadow-sm mb-3"><div class="card-body">
            <h6 class="mb-3">Add / Edit Home Banner</h6>
            <form method="post" enctype="multipart/form-data" class="row g-2">
                <input type="hidden" name="action" value="save_banner">
                <input type="hidden" name="id" id="bannerId" value="0">
                <div class="col-12 small text-muted" id="bannerFormMode"></div>
                <div class="col-md-2"><input name="title" class="form-control" placeholder="Title" required></div>
                <div class="col-md-2"><input name="subtitle" class="form-control" placeholder="Subtitle"></div>
                <div class="col-md-1"><input name="button_text" class="form-control" placeholder="Button"></div>
                <div class="col-md-2"><input name="button_link" class="form-control" placeholder="Link" value="#"></div>
                <div class="col-md-3"><input type="file" name="banner_file" class="form-control" id="bannerImageFile" accept="image/*"></div>
                <div class="col-md-1"><input type="number" name="sort_order" class="form-control" placeholder="Sort" value="0"></div>
                <div class="col-md-2 form-check ms-2 mt-2"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked><label class="form-check-label" for="is_active">Active</label></div>
                <div class="col-12"><button class="btn btn-admin" id="bannerSaveBtn">Save Banner</button> <button type="button" class="btn btn-outline-secondary" id="bannerResetBtn" style="display:none;">Cancel Edit</button></div>
            </form>
        </div></div>
        <div class="card admin-card border-0 shadow-sm"><div class="card-body table-responsive">
            <table class="table table-sm align-middle"><thead><tr><th>ID</th><th>Title</th><th>Image</th><th>Sort</th><th>Active</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($banners as $b): ?>
                <tr>
                    <td><?php echo (int)$b['id']; ?></td>
                    <td><?php echo htmlspecialchars($b['title']); ?></td>
                    <td><?php if (!empty($b['image_url'])): ?><a href="<?php echo htmlspecialchars($b['image_url']); ?>" target="_blank">view</a><?php endif; ?></td>
                    <td><?php echo (int)$b['sort_order']; ?></td>
                    <td><?php echo ((int)$b['is_active']===1?'Yes':'No'); ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editBanner(<?php echo htmlspecialchars(json_encode($b)); ?>)">Edit</button>
                        <form method="post" onsubmit="return confirm('Delete banner?')" style="display:inline;">
                            <input type="hidden" name="action" value="delete_banner">
                            <input type="hidden" name="id" value="<?php echo (int)$b['id']; ?>">
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody></table>
        </div></div>
    <?php endif; ?>

    <?php if ($page === 'settings'): ?>
        <div class="card admin-card border-0 shadow-sm" style="max-width:420px;"><div class="card-body">
            <h6>Admin Credentials (stored in DB)</h6>
            <form method="post" class="row g-2">
                <input type="hidden" name="action" value="change_admin_credentials">
                <div class="col-12"><input name="username" class="form-control" placeholder="Username" value="<?php echo htmlspecialchars((string)(currentAdmin()['username'] ?? 'admin')); ?>" required></div>
                <div class="col-12"><input type="password" name="password" class="form-control" placeholder="New Password" required></div>
                <div class="col-12"><button class="btn btn-admin">Update Credentials</button></div>
            </form>
        </div></div>
    <?php endif; ?>

<!-- Order Details Modal -->
<div class="modal fade" id="orderModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-shopping-bag me-2"></i>Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderModalBody"></div>
        </div>
    </div>
</div>

<!-- Contact Details Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-envelope me-2"></i>Contact Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contactModalBody"></div>
        </div>
    </div>
</div>

<script>
function editProduct(product) {
    document.getElementById('productId').value = product.id;
    document.querySelector('input[name="name"]').value = product.name || '';
    document.querySelector('select[name="category"]').value = product.category || 'special';
    document.querySelector('input[name="price"]').value = product.price || '';
    document.querySelector('input[name="discount"]').value = product.discount || '';
    document.querySelector('input[name="stock"]').value = product.stock || '';
    document.getElementById('productDescription').value = product.description || '';
    document.getElementById('productImageFile').removeAttribute('required');
    document.getElementById('productFormMode').textContent = 'Editing Product ID: ' + product.id;
    document.getElementById('productSaveBtn').textContent = 'Update Product';
    document.getElementById('productResetBtn').style.display = 'inline-block';
    document.querySelector('input[name="name"]').focus();
}

function resetProductForm() {
    document.getElementById('productId').value = '0';
    document.querySelector('input[name="name"]').value = '';
    document.querySelector('select[name="category"]').value = 'special';
    document.querySelector('input[name="price"]').value = '';
    document.querySelector('input[name="discount"]').value = '';
    document.querySelector('input[name="stock"]').value = '';
    document.getElementById('productDescription').value = '';
    document.getElementById('productImageFile').setAttribute('required', 'required');
    document.getElementById('productImageFile').value = '';
    document.getElementById('productFormMode').textContent = '';
    document.getElementById('productSaveBtn').textContent = 'Save Product';
    document.getElementById('productResetBtn').style.display = 'none';
}

function editBanner(banner) {
    document.getElementById('bannerId').value = banner.id;
    document.querySelector('input[name="title"]').value = banner.title || '';
    document.querySelector('input[name="subtitle"]').value = banner.subtitle || '';
    document.querySelector('input[name="button_text"]').value = banner.button_text || '';
    document.querySelector('input[name="button_link"]').value = banner.button_link || '#';
    document.querySelector('input[name="sort_order"]').value = banner.sort_order || '0';
    document.getElementById('is_active').checked = banner.is_active == 1 ? true : false;
    document.getElementById('bannerImageFile').removeAttribute('required');
    document.getElementById('bannerFormMode').textContent = 'Editing Banner ID: ' + banner.id;
    document.getElementById('bannerSaveBtn').textContent = 'Update Banner';
    document.getElementById('bannerResetBtn').style.display = 'inline-block';
    document.querySelector('input[name="title"]').focus();
}

function resetBannerForm() {
    document.getElementById('bannerId').value = '0';
    document.querySelector('input[name="title"]').value = '';
    document.querySelector('input[name="subtitle"]').value = '';
    document.querySelector('input[name="button_text"]').value = '';
    document.querySelector('input[name="button_link"]').value = '#';
    document.querySelector('input[name="sort_order"]').value = '0';
    document.getElementById('is_active').checked = true;
    document.getElementById('bannerImageFile').setAttribute('required', 'required');
    document.getElementById('bannerImageFile').value = '';
    document.getElementById('bannerFormMode').textContent = '';
    document.getElementById('bannerSaveBtn').textContent = 'Save Banner';
    document.getElementById('bannerResetBtn').style.display = 'none';
}

document.getElementById('productResetBtn').addEventListener('click', resetProductForm);
document.getElementById('bannerResetBtn').addEventListener('click', resetBannerForm);

function escHtml(s) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(s != null ? String(s) : ''));
    return d.innerHTML;
}

function viewOrder(data) {
    const o = data.order;
    const items = data.items;
    let itemsHtml = '';
    if (items && items.length > 0) {
        itemsHtml = '<h6 class="mt-3">Items Ordered</h6><table class="table table-sm"><thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead><tbody>';
        items.forEach(function(item) {
            itemsHtml += '<tr><td>' + escHtml(item.product_name) + '</td><td>' + escHtml(String(item.quantity)) + '</td><td>Rs ' + parseFloat(item.final_price || 0).toFixed(2) + '</td><td>Rs ' + parseFloat(item.item_total || 0).toFixed(2) + '</td></tr>';
        });
        itemsHtml += '</tbody></table>';
    } else {
        itemsHtml = '<p class="text-muted mt-3">No items found for this order.</p>';
    }
    document.getElementById('orderModalBody').innerHTML =
        '<div class="row g-2">' +
        '<div class="col-md-6"><strong>Order #:</strong> ' + escHtml(String(o.id)) + '</div>' +
        '<div class="col-md-6"><strong>Status:</strong> <span class="badge bg-secondary">' + escHtml(o.status) + '</span></div>' +
        '<div class="col-md-6"><strong>Customer:</strong> ' + escHtml(o.full_name) + '</div>' +
        '<div class="col-md-6"><strong>Email:</strong> ' + escHtml(o.email) + '</div>' +
        '<div class="col-md-6"><strong>Phone:</strong> ' + escHtml(o.phone) + '</div>' +
        '<div class="col-md-6"><strong>Payment:</strong> ' + escHtml(o.payment_method) + '</div>' +
        '<div class="col-12"><strong>Address:</strong> ' + escHtml(o.shipping_address) + '</div>' +
        '<div class="col-md-6"><strong>Total:</strong> Rs ' + parseFloat(o.total_amount || 0).toFixed(2) + '</div>' +
        '<div class="col-md-6"><strong>Date:</strong> ' + escHtml(o.created_at) + '</div>' +
        '</div>' + itemsHtml;
    new bootstrap.Modal(document.getElementById('orderModal')).show();
}

function viewContact(c) {
    document.getElementById('contactModalBody').innerHTML =
        '<div class="row g-2">' +
        '<div class="col-md-6"><strong>Name:</strong> ' + escHtml(c.name) + '</div>' +
        '<div class="col-md-6"><strong>Email:</strong> ' + escHtml(c.email) + '</div>' +
        '<div class="col-md-6"><strong>Phone:</strong> ' + escHtml(c.phone || 'N/A') + '</div>' +
        '<div class="col-md-6"><strong>Subject:</strong> ' + escHtml(c.subject) + '</div>' +
        '<div class="col-md-6"><strong>Status:</strong> <span class="badge bg-secondary">' + escHtml(c.status) + '</span></div>' +
        '<div class="col-md-6"><strong>Date:</strong> ' + escHtml(c.created_at) + '</div>' +
        '<div class="col-12 mt-2"><strong>Message:</strong><div class="mt-1 p-2 bg-light rounded">' + escHtml(c.message) + '</div></div>' +
        '</div>';
    new bootstrap.Modal(document.getElementById('contactModal')).show();
}
</script>

</main>
</body>
</html>
