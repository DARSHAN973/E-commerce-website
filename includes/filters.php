<!-- Filter Top Bar -->
<!-- Filter Bar -->
<div class="filter-bar container mb-5">
  <div class="glass-filter d-flex justify-content-center flex-wrap gap-3 py-3 px-4 rounded-4 shadow-sm">
    <a href="?price=" class="filter-btn <?= (!isset($_GET['price']) || $_GET['price'] == '') ? 'active' : '' ?>">All</a>
    <a href="?price=under-1000" class="filter-btn <?= ($_GET['price'] ?? '') == 'under-1000' ? 'active' : '' ?>">Under ₹1000</a>
    <a href="?price=1000-1999" class="filter-btn <?= ($_GET['price'] ?? '') == '1000-1999' ? 'active' : '' ?>">₹1000 - ₹1999</a>
    <a href="?price=2000+" class="filter-btn <?= ($_GET['price'] ?? '') == '2000+' ? 'active' : '' ?>">₹2000 & Above</a>
  </div>
</div>


<style>
.glass-filter {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(200, 200, 200, 0.3);
  border-radius: 1.5rem;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
}

.filter-btn {
  text-decoration: none;
  color: #222;
  font-weight: 500;
  padding: 0.5rem 1.2rem;
  border-radius: 50px;
  border: 1.5px solid #ccc;
  transition: all 0.3s ease;
  background: white;
}

.filter-btn:hover {
  background: #d7263d;
  color: white;
  border-color: #d7263d;
}

.filter-btn.active {
  background: #d7263d;
  color: white;
  border-color: #d7263d;
  box-shadow: 0 0 10px rgba(215, 38, 61, 0.4);
}

</style>