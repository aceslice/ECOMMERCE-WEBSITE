<?php

// Include the configuration file which likely contains the database connection
include 'config.php';

// Start the PHP session to persist user data across pages
session_start();

// Retrieve user_id from the session data
$user_id = $_SESSION['user_id'];

// If user_id is not set, redirect to login page
if (!isset($user_id)) {
   header('location:login.php');
   exit; // Stop further execution after redirection
}

// Check if the form for adding to cart is submitted
if (isset($_POST['add_to_cart'])) {

   // Retrieve product details from the form submission
   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   // Check if the product is already in the cart for the current user
   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if (mysqli_num_rows($check_cart_numbers) > 0) {
      // If the product is already in the cart, add a message to the array
      $message[] = 'already added to cart!';
   } else {
      // If the product is not in the cart, insert it into the 'cart' table
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
      // Add a success message to the array
      $message[] = 'product added to cart!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>search page</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>search page</h3>
   <p> <a href="home.php">home</a> / search </p>
</div>

<section class="search-form">
   <!-- Search form for products -->
   <form action="" method="post">
      <input type="text" name="search" placeholder="search products..." class="box">
      <input type="submit" name="submit" value="search" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">

   <div class="box-container">
   <?php
      // Check if search form is submitted
      if (isset($_POST['submit'])) {
         $search_item = $_POST['search'];
         // Query to search products based on the entered text
         $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('query failed');
         if (mysqli_num_rows($select_products) > 0) {
            // Display each product that matches the search criteria
            while ($fetch_product = mysqli_fetch_assoc($select_products)) {
   ?>
   <form action="" method="post" class="box">
      <!-- Display product details -->
      <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image">
      <div class="name"><?php echo $fetch_product['name']; ?></div>
      <div class="price">GHC <?php echo $fetch_product['price']; ?></div>
      <input type="number" class="qty" name="product_quantity" min="1" value="1">
      <!-- Hidden fields to store product details -->
      <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
      <!-- Button to add product to cart -->
      <input type="submit" class="btn" value="add to cart" name="add_to_cart">
   </form>
   <?php
            }
         } else {
            // If no products found matching the search, display a message
            echo '<p class="empty">no result found!</p>';
         }
      } else {
         // Display message to prompt user to search for products
         echo '<p class="empty">search something!</p>';
      }
   ?>
   </div>
  
</section>

<?php include 'footer.php'; ?>

<!-- Custom JavaScript file link -->
<script src="js/script.js"></script>

</body>
</html>
