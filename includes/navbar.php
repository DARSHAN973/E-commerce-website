<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once 'includes/db.php'; 

// Get cart count for logged in users
$cart_count = 0;
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    $user_id = $_SESSION['user_id'];
  $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $cart_data = $stmt->fetch();
    $cart_count = $cart_data['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&display=swap');
body {
      font-family: 'Montserrat', sans-serif;
    }
    /*navbar style*/
    .navbar-brand {
      font-family: 'Playfair Display', serif;
      font-size: 28px;
      font-weight: 600;
      color: #111 !important;
    }
    .nav-link {
      font-size: 17px;
      font-weight: 500;
      color: #555 !important;
    }
    .nav-link.active {
    color: #000 !important;
    font-weight: 600;
    }
    .nav-link.active {
    border-bottom: 2px solid #000;
    }
    .nav-link:hover {
      color: #000 !important;
    }
    .nav-icons .nav-link {
      padding: 0 10px;
      font-size: 18px;
    }
    .navbar.sticky-top {
    z-index: 1050; 
    }
    /*logo style*/
    .premium-logo {
    font-family: 'Playfair Display', serif !important;
    font-size: 32px !important;
    font-weight: 600 !important;
    letter-spacing: 1.5px !important;
    color: #1a1a1a !important;
    text-decoration: none !important;
    position: relative;
    transition: all 0.3s ease;
    margin-right: 75px;
    }

    /* Optional classy colored dot */
    .premium-logo .dot {
    color: #c2185b;
    font-size: 36px;
    vertical-align: top;
    margin-left: 2px;
    }

    /* Optional underline bar below logo */
    .premium-logo::after {
    content: '';
    display: block;
    width: 40%;
    height: 2px;
    background-color: #c2185b;
    margin: 6px auto 0;
    border-radius: 2px;
    transition: all 0.3s ease;
    }

    /* Hover effect */
    .premium-logo:hover {
    color: #c2185b !important;
    }
    .premium-logo:hover::after {
    width: 60%;
    }
    /* search bar and searcg icon */
    .input-group .form-control {
    box-shadow: none;
    border-color: #c2185b;
    margin-right: 10px;
    }
    .input-group .btn {
    color: #fff;
    background: #c2185b;
    border-color: #c2185b;
    }
    .input-group .form-control:focus {
    border-color: #c2185b;
    box-shadow: 0 0 0 0.2rem rgba(194,24,91,.25);
    }
    /*mobile search bar*/
    #mobileSearchBox input,
    #mobileSearchBox button {
    padding: 0.5rem 1rem;
    }

    /*icon space*/
    .nav-icons .nav-link {
      padding-left: 20px;
      padding-right: 20px;
      font-size: 18px;
    }
    .navbar .nav-link i {
    font-size: 28px !important;
    position: relative !important;
    top: 2px !important;
    }

    .navbar .input-group-sm .form-control {
      height: 35px;
    }

    .navbar .input-group-sm .btn {
      height: 35px;
    }

    /* User menu styles */
    .user-menu {
        background: linear-gradient(135deg, #c2185b, #e91e63);
        border: none;
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .user-menu:hover {
        background: linear-gradient(135deg, #a91650, #c2185b);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(194, 24, 91, 0.3);
    }
    .user-menu:focus {
        box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.25);
    }

    /* Cart badge */
    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #c2185b;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    /* Dropdown menu styling */
    .dropdown {
        position: relative;
    }
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 10px;
        margin-top: 10px;
        z-index: 1060 !important;
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        display: none;
        min-width: 200px;
        background: white;
    }
    .dropdown-menu.show {
        display: block !important;
    }
    .dropdown-item {
        padding: 10px 20px;
        transition: all 0.3s ease;
        display: block;
        text-decoration: none;
        color: #333;
    }
    .dropdown-item:hover {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        color: #c2185b;
        text-decoration: none;
    }

    /* ── Loading Screen ── */
    #stylique-loader {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: #ffffff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 99999;
      transition: opacity 0.45s ease, visibility 0.45s ease;
    }
    #stylique-loader.fade-out {
      opacity: 0;
      visibility: hidden;
    }
    #stylique-loader .loader-brand {
      font-family: 'Playfair Display', serif;
      font-size: 2.4rem;
      font-weight: 700;
      color: #c2185b;
      letter-spacing: 2px;
      margin-bottom: 1.25rem;
    }
    #stylique-loader .loader-brand .dot {
      color: #c2185b;
    }
    #stylique-loader .loader-spinner {
      width: 48px;
      height: 48px;
      border: 4px solid #f8d7e5;
      border-top: 4px solid #c2185b;
      border-radius: 50%;
      animation: stylique-spin 0.8s linear infinite;
    }
    @keyframes stylique-spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
<!-- Loading Screen -->
<div id="stylique-loader">
  <div class="loader-brand">Stylique<span class="dot">.</span></div>
  <div class="loader-spinner"></div>
</div>
<!--navbar-->
    <nav class="navbar navbar-expand-lg bg-light shadow-sm py-3 sticky-top">
    <div class="container">
      <a class="navbar-brand premium-logo" href="index.php">Stylique<span class="dot">.</span></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Links -->
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link <?php if ($activePage == 'home') echo 'active'; ?>" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?php if ($activePage == 'men') echo 'active'; ?>" href="men.php">Men</a></li>
          <li class="nav-item"><a class="nav-link <?php if ($activePage == 'women') echo 'active'; ?>" href="women.php">Women</a></li>
          <li class="nav-item"><a class="nav-link <?php if ($activePage == 'collection') echo 'active'; ?>" href="collection.php">Collection</a></li>
          <li class="nav-item"><a class="nav-link <?php if ($activePage == 'contact') echo 'active'; ?>" href="contact.php">Contact</a></li>
        </ul>
      </div>
      
      <!-- Icons row, always visible and responsive -->
      <div class="d-flex flex-row justify-content-end align-items-center gap-3 py-2">
        <!-- Search Bar -->
        <form class="d-none d-lg-flex" role="search" method="GET" action="search.php">
        <div class="input-group input-group-sm">
          <input class="form-control border-end-0 rounded-pill" type="search" placeholder="Search products..." name="q" aria-label="Search" required>
          <button class="btn btn-primary border-start-0 rounded-pill ms-n3" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>
      
      <!-- Mobile Search Box -->
      <div id="mobileSearchBox" class="d-lg-none" style="display:none;">
        <form class="py-2 px-3" role="search" method="GET" action="search.php">
          <div class="input-group input-group-sm">
            <input class="form-control border-end-0 rounded-pill" type="search" placeholder="Search products..." name="q" aria-label="Search" required>
            <button class="btn btn-primary border-start-0 rounded-pill ms-n3" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
      
      <!-- Mobile Search Toggle Button -->
      <a class="nav-link d-lg-none" href="#" id="searchToggleBtn">
        <i class="fas fa-search"></i>
      </a>

      <!-- User Authentication Section -->
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
        <!-- Logged In User -->
        <div class="dropdown">
          <button class="btn user-menu" type="button" id="userDropdown" onclick="toggleDropdown()">
            <i class="fas fa-user me-2"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?>
            <i class="fas fa-chevron-down ms-2"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" id="userDropdownMenu" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
            <li><a class="dropdown-item" href="cart.php"><i class="fas fa-shopping-cart me-2"></i>My Cart (<?php echo $cart_count; ?>)</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#" onclick="logoutUser()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <!-- Not Logged In -->
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#authModal">
          <i class="fas fa-user"></i>
        </a>
      <?php endif; ?>

      <!-- Shopping Cart -->
      <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
        <a class="nav-link position-relative" href="cart.php">
          <i class="fas fa-shopping-cart"></i>
          <?php if ($cart_count > 0): ?>
            <span class="cart-badge"><?php echo $cart_count; ?></span>
          <?php endif; ?>
        </a>
      <?php endif; ?>
      </div>
    </div>
  </nav>

  <!-- Include Auth Modal for non-logged in users -->
  <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
    <?php include 'includes/auth-modal.php'; ?>
  <?php endif; ?>

  <!-- Mobile Search Toggle Script -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Mobile search functionality
    const searchToggleBtn = document.getElementById('searchToggleBtn');
    const mobileSearchBox = document.getElementById('mobileSearchBox');
    
    if (searchToggleBtn && mobileSearchBox) {
      searchToggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (mobileSearchBox.style.display === 'none' || mobileSearchBox.style.display === '') {
          mobileSearchBox.style.display = 'block';
        } else {
          mobileSearchBox.style.display = 'none';
        }
      });
    }

    // Initialize Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
      return new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Debug: Check if dropdown elements exist
    console.log('Dropdown elements found:', dropdownElementList.length);
    
    // Additional dropdown debugging
    const userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
      console.log('User dropdown found:', userDropdown);
      userDropdown.addEventListener('click', function() {
        console.log('Dropdown clicked');
      });
    }
  });

  // Custom dropdown toggle function
  function toggleDropdown() {
    const dropdownMenu = document.getElementById('userDropdownMenu');
    if (dropdownMenu) {
      dropdownMenu.classList.toggle('show');
      console.log('Dropdown toggled:', dropdownMenu.classList.contains('show'));
    }
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.dropdown');
    const dropdownMenu = document.getElementById('userDropdownMenu');
    
    if (dropdown && dropdownMenu && !dropdown.contains(event.target)) {
      dropdownMenu.classList.remove('show');
    }
  });

  // Logout function
  function logoutUser() {
    if (confirm('Are you sure you want to logout?')) {
      const formData = new FormData();
      formData.append('action', 'logout');
      
      fetch('includes/auth.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = 'index.php';
        }
      })
      .catch(error => {
        console.error('Logout error:', error);
        window.location.href = 'index.php';
      });
    }
  }

  // Loading screen
  window.addEventListener('load', function() {
    var loader = document.getElementById('stylique-loader');
    if (loader) {
      loader.classList.add('fade-out');
      setTimeout(function() { loader.style.display = 'none'; }, 450);
    }
  });
  </script>
  
</body>
</html>