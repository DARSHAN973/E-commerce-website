<?php
include 'includes/db.php';

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $sql = "SELECT * FROM products WHERE id = $id";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $product = mysqli_fetch_assoc($result);
  } else {
    echo "Product not found!";
    exit;
  }
} else {
  echo "No product ID provided.";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?php echo $product['name']; ?> - Stylique</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap Bundle JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Font Awesome CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="assests/css/home-page.css">
  <style>
  .nav-link i {
      margin-bottom: 0 !important;
  }
</style>
</head>
<body>
  <?php include 'includes/navbar.php'; ?>
  <!-- Breadcrumb -->
    <div class="container mt-4">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb bg-light p-3 rounded">
      <li class="breadcrumb-item">
        <a href="/stylique/index.php" class="text-decoration-none text-dark">Home</a>
      </li>

      <?php
      $category = strtolower($product['category']); // for easier comparison
      if ($category !== 'special') {
        // show only if not 'special'
        echo '<li class="breadcrumb-item">
                <a href="/stylique/' . $category . '.php" class="text-decoration-none text-dark">'
                  . ucfirst($category) .
                '</a>
              </li>';
      }
      ?>

      <li class="breadcrumb-item active text-muted" aria-current="page">
        <?php echo $product['name']; ?>
      </li>
    </ol>
  </nav>
</div>




  <div class="container py-5">
    <div class="row">
      <!-- Product Image -->
      <div class="col-md-6 text-center">
        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid rounded-3" style="max-height: 500px;">
      </div>

      <!-- Product Info -->
      <div class="col-md-6">
        <h2 class="fw-bold"><?php echo $product['name']; ?></h2>
        <p class="lead"><?php echo $product['description']; ?></p>

        <h4>
          ₹<?php echo $product['price'] - $product['discount']; ?>
          <?php if ($product['discount'] > 0) { ?>
            <del class="text-muted ms-2">₹<?php echo $product['price']; ?></del>
            <span class="badge bg-danger ms-2">
              <?php echo round(($product['discount'] / $product['price']) * 100); ?>% OFF
            </span>
          <?php } ?>
        </h4>

        <p class="mt-3">In Stock: <strong><?php echo $product['stock']; ?></strong></p>

        <form method="post" action="#">
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
          </div>
          <button type="submit" class="btn btn-dark px-4">Add to Cart</button>
        </form>
      </div>
    </div>
  </div>

<div class="container py-3">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <!-- Highlights -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-semibold mb-3"><i class="fas fa-star text-warning me-2"></i>Highlights</h5>
          <ul class="list-unstyled mb-0">
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Premium Quality Material</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Stylish Modern Fit</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Easy to Wash</li>
            <li><i class="fas fa-check-circle text-success me-2"></i> Lightweight & Comfortable</li>
          </ul>
        </div>
      </div>

      <!-- Shipping & Returns -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <h5 class="fw-semibold mb-3"><i class="fas fa-truck text-primary me-2"></i>Shipping & Returns</h5>
          <p class="text-muted mb-2"><i class="fas fa-shipping-fast text-primary me-2"></i>Free shipping on orders over ₹999</p>
          <p class="text-muted mb-0"><i class="fas fa-undo text-warning me-2"></i>Easy 7-day return policy</p>
        </div>
      </div>
    </div>
  </div>
</div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

