<?php
include 'includes/db.php';
$activePage = 'men';
include 'includes/navbar.php';

$where = "category = 'men'";
if (isset($_GET['price'])) {
  $price = $_GET['price'];
  if ($price == 'under-1000') {
    $where .= " AND price < 1000";
  } elseif ($price == '1000-1999') {
    $where .= " AND price BETWEEN 1000 AND 1999";
  } elseif ($price == '2000+') {
    $where .= " AND price >= 2000";
  }
}

/*$sql = "SELECT * FROM products WHERE category = 'mens' LIMIT 12";
$result = mysqli_query($conn, $sql);*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mens wear</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!--javascript-->
    
</head>
<body>
    <?php
    include 'includes/filters.php';
    $heading = 'Mens Wear';
    $limit = 12;
    $category = 'mens';
    include 'includes/product-grid.php';


    ?>
</body>
</html>


