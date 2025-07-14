<?php
$heading = $heading ?? ucfirst($category) ;
$limit = $limit ?? 12;

if (!isset($category)) {
  echo "<p class='text-danger text-center'>No category specified!</p>";
  return;
}

include_once 'includes/db.php';

$sql = "SELECT * FROM products WHERE category = '$category' LIMIT $limit";
$result = mysqli_query($conn, $sql);

?>

<section class="container py-5">
  <h3 class="mb-4 text-center fancy-heading">✨ <?php echo ucfirst($heading); ?></h3>
  <div class="row g-4">
    <?php while ($row = mysqli_fetch_assoc($result)) {
      $finalPrice = $row['price'] - $row['discount'];
    ?>
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card shadow-sm h-100">
        <div class="product-img-wrapper">
          <img src="<?php echo $row['image']; ?>" class="product-img" alt="<?php echo $row['name']; ?>">
        </div>
        <div class="card-body text-center">
          <h5 class="card-title"><?php echo $row['name']; ?></h5>

          <p class="card-text">
            ₹<?php echo $finalPrice; ?>
            <?php if ($row['discount'] > 0): ?>
              <del class="text-muted ms-2">₹<?php echo $row['price']; ?></del>
              <span class="badge bg-danger ms-1">
                <?php echo round(($row['discount'] / $row['price']) * 100); ?>% OFF
              </span>
            <?php endif; ?>
          </p>

          <a href="product.php?id=<?php echo $row['id']; ?>" class="btn btn-dark btn-sm">Shop Now</a>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</section>
<style>
    /*featured section style*/
    /*heading style*/
    h3.fancy-heading {
    font-weight: 800;
    font-size: 2rem;
    background: linear-gradient(90deg, #3b3636, #ff0055);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: 1px;
    }

    /*.card img {
    /*height: 300px;
    object-fit: cover;
    }*/

    .card h5 {
      font-weight: 600;
    }

    .card .btn {
      border-radius: 20px;
    }
    .card .btn:hover {
      background-color: #c2185b;
      color: #fff;
    }
    .product-img-wrapper {
    height: 300px; /* Adjust based on design */
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f9f9f900;
    border-radius: 0.5rem;
    padding: 8px;
    }

   .product-img {
    max-height: 100%;
    max-width: 100%;
    object-fit: contain; /* Use 'cover' if you want crop */
    transition: transform 0.3s ease-in-out;
    }

  .product-img:hover {
    transform: scale(1.05);
    }
</style>