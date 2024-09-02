<?php

// Include the configuration file which likely contains the database connection details
include 'config.php';

// Start the PHP session to persist user data across pages
session_start();

// Get the user ID from the session if it exists; otherwise, redirect to login page
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
   header('location:login.php');
   exit; // Ensure script stops executing after redirecting
}

// If the form for adding to cart is submitted
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
   <title>shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>our shop</h3>
      <p> <a href="home.php">home</a> / shop </p>
   </div>

   <section class="products">

      <h1 class="title">latest products</h1>

      <div class="box-container">

         <?php
         // Query to select all products from 'products' table
         $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
         // Check if there are products available
         if (mysqli_num_rows($select_products) > 0) {
            // Loop through each product fetched from the database
            while ($fetch_products = mysqli_fetch_assoc($select_products)) {
               ?>
               <form action="" method="post" class="box">
                  <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                  <div class="name"><?php echo $fetch_products['name']; ?></div>
                  <div class="price">GHC <?php echo $fetch_products['price']; ?></div>
                  <!-- Input field to enter quantity and hidden fields for product details -->
                  <input type="number" min="1" name="product_quantity" value="1" class="qty">
                  <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
                  <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
                  <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
                  <!-- Button to add product to cart -->
                  <input type="submit" value="add to cart" name="add_to_cart" class="btn">
               </form>
               <?php
            }
         } else {
            // If no products found in the database, display a message
            echo '<p class="empty">no products added yet!</p>';
         }
         ?>
      </div>

   </section><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>








   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>