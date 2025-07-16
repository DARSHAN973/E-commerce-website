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
  <link rel="stylesheet" href="assests/css/home-page.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
  <?php include 'includes/navbar.php'; ?>

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
</body>
</html>
