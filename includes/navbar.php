<!--navbar-->
    <nav class="navbar navbar-expand-lg bg-light shadow-sm py-3 sticky-top">
    <div class="container">
      <a class="navbar-brand premium-logo" href="#">Stylique<span class="dot">.</span></a>
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
        <form class="d-none d-lg-flex" role="search">
        <div class="input-group input-group-sm">
          <input class="form-control border-end-0 rounded-pill" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-secondary border-start-0 rounded-pill ms-n3" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>
      <div id="mobileSearchBox" class="d-lg-none" style="display:none;">
        <form class="py-2 px-3" role="search">
          <div class="input-group input-group-sm">
            <input class="form-control border-end-0 rounded-pill" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-secondary border-start-0 rounded-pill ms-n3" type="submit">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
      <!-- Mobile Search Toggle Button -->
      <a class="nav-link d-lg-none" href="#" id="searchToggleBtn">
        <i class="fas fa-search"></i>
      </a>
        <a class="nav-link" href="#"><i class="fas fa-user "></i></a>
        <a class="nav-link position-relative" href="#">
          <i class="fas fa-shopping-cart"></i>
        </a>
      </div>
    </div>
  </nav>
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
    .nav-link i {
    font-size: 26px;
    margin-bottom: 12px;
    }

    .navbar .input-group-sm .form-control {
      height: 35px;
    }

    .navbar .input-group-sm .btn {
      height: 35px;
    }

  </style>