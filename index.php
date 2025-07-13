<?php
$activePage = 'home';
include 'includes/db.php';
include 'includes/navbar.php';
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
    <!-- Slide 1 -->
    <div class="carousel-item active">
      <img src="images/summer collation.jpg" class="d-block w-100 image-fluid" alt="Men’s Summer Collection">
      <div class="carousel-caption">
        <h2 class="text-shadow">The Summer Code</h2>
        <p class="text-shadow">Light layers. Clean cuts. Men’s essentials redefined.</p>
        <a href="#" class="btn btn-dark">Shop Now</a>
      </div>
    </div>
    <!-- Slide 2 -->
    <div class="carousel-item">
      <img src="images/bold femme.jpg" class="d-block w-100 image-fluid" alt="Women's Collection">
      <div class="carousel-caption">
        <h2 class="text-shadow">Bold Femme</h2>
        <p class="text-shadow">Confidence in every step. Your boldest self, styled.</p>
        <a href="#" class="btn btn-light">Explore</a>
      </div>
    </div>
    <!-- Slide 3 -->
    <div class="carousel-item">
      <img src="images/collection.jpg" class="d-block w-100 image-fluid " alt="Seasonal Collection">
      <div class="carousel-caption">
        <h2 class="text-shadow">The Season Edit</h2>
        <p class="text-shadow">A curated collection for every style and every you.</p>
        <a href="#" class="btn btn-light">Discover Now</a>
      </div>
    </div>

  </div>

  <!-- Carousel Controls -->
  <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>
<!--offer section-->
<!--<section class="offer-section py-5">
  <div class="container">
    <section class="container py-5">
      <div class="row g-4 text-center">
        <div class="col-md-4">
          <div class="offer-box">
            <i class="fas fa-tags"></i>
            <h5>Flat 30% Off</h5>
            <p>On all new summer arrivals</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="offer-box">
            <i class="fas fa-shipping-fast"></i>
            <h5>Free Shipping</h5>
            <p>On all orders above ₹999</p>
          </div>
        </div>

        <div class="col-md-4">
          <div class="offer-box">
            <i class="fas fa-star"></i>
            <h5>Premium Quality</h5>
            <p>Styling that lasts, comfort that stays</p>
          </div>
        </div>
      </div>
  </section>
  </div>
</section>-->

<!--featured styles-->
<section class="container py-5">
  <h3 class="mb-4 text-center fancy-heading">✨ Stylique Selects</h3>
  <div class="row g-4">
    
    <!-- Card 1 -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="images/product1.jpg" class="card-img-top" alt="Product 1">
        <div class="card-body text-center">
          <h5 class="card-title">Denim Layer Jacket</h5>
          <p class="card-text">₹1,999 <del class="text-muted">₹2,499</del></p>
          <a href="#" class="btn btn-dark btn-sm">Shop Now</a>
        </div>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="images/product2.jpg" class="card-img-top" alt="Product 2">
        <div class="card-body text-center">
          <h5 class="card-title">Casual Oversized Tee</h5>
          <p class="card-text">₹799</p>
          <a href="#" class="btn btn-dark btn-sm">Shop Now</a>
        </div>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="images/product3.jpg" class="card-img-top" alt="Product 3">
        <div class="card-body text-center">
          <h5 class="card-title">Women’s Blazer Suit</h5>
          <p class="card-text">₹2,999</p>
          <a href="#" class="btn btn-dark btn-sm">Shop Now</a>
        </div>
      </div>
    </div>

    <!-- Card 4 -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="images/product4.jpg" class="card-img-top" alt="Product 4">
        <div class="card-body text-center">
          <h5 class="card-title">Black Street Hoodie</h5>
          <p class="card-text">₹1,599</p>
          <a href="#" class="btn btn-dark btn-sm">Shop Now</a>
        </div>
      </div>
    </div>

    <!-- Card 5 -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="images/product5.jpg" class="card-img-top" alt="Product 5">
        <div class="card-body text-center">
          <h5 class="card-title">Relax Fit White Shirt</h5>
          <p class="card-text">₹1,299</p>
          <a href="#" class="btn btn-dark btn-sm">Shop Now</a>
        </div>
      </div>
    </div>

    <!-- Card 6 -->
    <div class="col-md-4">
      <div class="card shadow-sm h-100">
        <img src="images/product6.jpg" class="card-img-top" alt="Product 6">
        <div class="card-body text-center">
          <h5 class="card-title">Mid-Rise Women Jeans</h5>
          <p class="card-text">₹1,799</p>
          <a href="#" class="btn btn-dark btn-sm">Shop Now</a>
        </div>
      </div>
    </div>

  </div>
</section>



</body>
</html>
<?php include 'includes/footer.php'; ?>