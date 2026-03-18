<?php
$activePage = 'home';
include 'includes/navbar.php';
include_once 'includes/db.php'; 

$banners = [];
try {
  $stmt = $conn->query("SELECT title, subtitle, button_text, button_link, image_url FROM home_banners WHERE is_active = TRUE ORDER BY sort_order ASC, id ASC");
  $banners = $stmt->fetchAll();
} catch (Throwable $e) {
  $banners = [];
}

if (count($banners) === 0) {
  $banners = [
    [
      'title' => 'The Summer Code',
      'subtitle' => 'Light layers. Clean cuts. Men\'s essentials redefined.',
      'button_text' => 'Shop Now',
      'button_link' => 'men.php',
      'image_url' => 'images/summer collation.jpg',
    ],
    [
      'title' => 'Bold Femme',
      'subtitle' => 'Confidence in every step. Your boldest self, styled.',
      'button_text' => 'Explore',
      'button_link' => 'women.php',
      'image_url' => 'images/bold femme.jpg',
    ],
    [
      'title' => 'The Season Edit',
      'subtitle' => 'A curated collection for every style and every you.',
      'button_text' => 'Discover Now',
      'button_link' => 'collection.php',
      'image_url' => 'images/collection.jpg',
    ],
  ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylique - Fashion Redefined</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assests/css/home-page.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!--javascript-->
    <script src="assests/javascript/home-page.js"></script>

</head>
<body>
  <!--slider-->
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php foreach ($banners as $i => $banner): ?>
      <div class="carousel-item <?php echo $i === 0 ? 'active' : ''; ?>">
        <img src="<?php echo htmlspecialchars($banner['image_url']); ?>" class="d-block w-100 image-fluid" alt="<?php echo htmlspecialchars($banner['title']); ?>">
        <div class="carousel-caption">
          <h2 class="text-shadow"><?php echo htmlspecialchars($banner['title']); ?></h2>
          <p class="text-shadow"><?php echo htmlspecialchars($banner['subtitle']); ?></p>
          <a href="<?php echo htmlspecialchars($banner['button_link'] ?: '#'); ?>" class="btn btn-light"><?php echo htmlspecialchars($banner['button_text'] ?: 'Explore'); ?></a>
        </div>
      </div>
    <?php endforeach; ?>

  </div>

  <!-- Carousel Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<?php 
  $category = 'special';
  $limit = 12;
  $heading = "Stylique Featured Styles";
  include 'includes/product-grid.php';
?>
<!-- Offers Section -->
<section class="container py-5">
  <h3 class="mb-4 text-center fancy-heading">
  <span style="font-size: 1.8rem;">🔥</span> <span class="text-danger">Today's Hot Offers</span>
</h3>


  <div class="row g-4">
    
    <!-- Offer 1 -->
    <div class="col-md-6 col-lg-4">
      <div class="offer-card p-4 text-center text-white" style="background: linear-gradient(135deg, #000000, #434343); border-radius: 1rem;">
        <h5 class="mb-2">⚡ Flat 40% OFF</h5>
        <p>On all Men’s Jackets</p>
        <a href="#" class="btn btn-light btn-sm mt-2">Grab Now</a>
      </div>
    </div>

    <!-- Offer 2 -->
    <div class="col-md-6 col-lg-4">
      <div class="offer-card p-4 text-center text-white" style="background: linear-gradient(135deg, #ff6a00, #ee0979); border-radius: 1rem;">
        <h5 class="mb-2">🎉 Buy 1 Get 1 Free</h5>
        <p>Women's Tops & Tees</p>
        <a href="#" class="btn btn-light btn-sm mt-2">Shop Offer</a>
      </div>
    </div>

    <!-- Offer 3 -->
    <div class="col-md-12 col-lg-4">
      <div class="offer-card p-4 text-center text-white" style="background: linear-gradient(135deg, #3a1c71, #d76d77, #ffaf7b); border-radius: 1rem;">
        <h5 class="mb-2">👟 Extra 25% OFF</h5>
        <p>Stylique Sneakers & Footwear</p>
        <a href="#" class="btn btn-light btn-sm mt-2">Explore</a>
      </div>
      
    </div>
  </div>
</section>
<!--Brands We Work With-->
  <section class="container py-5 text-center">
  <h5 class="mb-4 text-uppercase text-muted">Trusted by top fashion brands</h5>
  <div class="d-flex justify-content-center flex-wrap gap-4">
    <img src="images/adidas.png" alt="Brand 1" height="40">
    <img src="images/h&m.png" alt="Brand 2" height="40">
    <img src="images/nike.png" alt="Brand 3" height="40">
    <img src="images/zara.png" alt="Brand 4" height="40">
  </div>
</section>
<!--email subscription-->
<section class="container py-5 text-center">
  <h4 class="mb-3">🛍️ Stay in Style with Stylique</h4>
  <p class="mb-4 text-muted">Subscribe to get exclusive offers & updates</p>
  <form class="row justify-content-center" id="subscribeForm" action="includes/subscribe.php" method="POST">
    <div class="col-md-6 col-lg-4">
      <input type="email" name="email" id="subscriberEmail" class="form-control rounded-pill" placeholder="Enter your email">
    </div>
    <div class="col-md-2 mt-2 mt-md-0">
      <button type="submit" class="btn btn-dark rounded-pill px-4">Subscribe</button>
    </div>
  </form>
</section>
<!--why shop with us-->
<section class="container py-5 text-center">
  <div class="row g-4">
    <div class="col-md-4">
      <i class="fas fa-truck fa-2x mb-2 text-dark"></i>
      <h6>Free Shipping</h6>
      <p class="small text-muted">On orders over ₹999</p>
    </div>
    <div class="col-md-4">
      <i class="fas fa-sync fa-2x mb-2 text-dark"></i>
      <h6>Easy Returns</h6>
      <p class="small text-muted">Within 7 days</p>
    </div>
    <div class="col-md-4">
      <i class="fas fa-lock fa-2x mb-2 text-dark"></i>
      <h6>Secure Payment</h6>
      <p class="small text-muted">100% secure & encrypted</p>
    </div>
  </div>
</section>
<!--pop up modal for email subscription-->
  <div class="modal fade" id="subscribeModal" tabindex="-1" aria-labelledby="subscribeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <h5 class="modal-title mb-3" id="subscribeModalLabel">🎉 Congratulations!</h5>
        <p class="mb-3">You're now subscribed to Stylique updates.</p>
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
</div>


</body>
</html>
<?php include 'includes/footer.php'; ?>