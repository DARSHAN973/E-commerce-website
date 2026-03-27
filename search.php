<?php
session_start();
include 'includes/db.php';

$activePage = 'search';
$searchQuery = trim($_GET['q'] ?? '');
$searchResults = [];
$searchMessage = '';

if ($searchQuery !== '') {
    try {
        $stmt = $conn->prepare("
            SELECT * FROM products 
            WHERE LOWER(name) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?)
            ORDER BY name ASC
        ");
        $searchTerm = '%' . $searchQuery . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        $searchResults = $stmt->fetchAll();
        
        if (empty($searchResults)) {
            $searchMessage = "No products found matching '$searchQuery'";
        }
    } catch (Exception $e) {
        $searchMessage = "Search error: " . $e->getMessage();
    }
} else {
    $searchMessage = "Please enter a search term";
}

include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Stylique</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assests/css/home-page.css">
</head>
<body>
    <!-- Include Auth Modal -->
    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <?php include 'includes/auth-modal.php'; ?>
    <?php endif; ?>

    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="fw-bold">
                    <i class="fas fa-search text-primary me-2"></i>Search Results
                </h2>
                <?php if ($searchQuery !== ''): ?>
                    <p class="text-muted">Showing results for: <strong>"<?php echo htmlspecialchars($searchQuery); ?>"</strong></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($searchResults)): ?>
            <div class="row">
                <?php foreach ($searchResults as $product): ?>
                    <div class="col-md-3 col-sm-6 col-12 mb-4">
                        <div class="card h-100 border-0 shadow-sm hover-card">
                            <div class="card-img-top position-relative overflow-hidden" style="height: 250px;">
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                     class="w-100 h-100 object-fit-cover">
                                <?php if ($product['discount'] > 0): ?>
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                        <?php echo round(($product['discount'] / $product['price']) * 100); ?>% OFF
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?php echo htmlspecialchars(substr($product['description'] ?? '', 0, 60)); ?>...
                                </p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <div>
                                        <h6 class="fw-bold text-primary mb-0">
                                            Rs <?php echo number_format($product['price'] - $product['discount'], 2); ?>
                                        </h6>
                                        <?php if ($product['discount'] > 0): ?>
                                            <small class="text-muted"><del>Rs <?php echo number_format($product['price'], 2); ?></del></small>
                                        <?php endif; ?>
                                    </div>
                                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info alert-lg text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="fs-5 mt-3"><?php echo htmlspecialchars($searchMessage); ?></p>
                <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add style for hover effect -->
    <style>
        .hover-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }
        .card-img-top {
            background-color: #f5f5f5;
        }
    </style>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
