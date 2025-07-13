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
        <a class="nav-link" href="#"><i class="fas fa-user"></i></a>
        <a class="nav-link position-relative" href="#">
          <i class="fas fa-shopping-cart"></i>
        </a>
      </div>
    </div>
  </nav>