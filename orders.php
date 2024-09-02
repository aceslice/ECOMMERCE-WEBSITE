<?php

include 'config.php'; // Including configuration file for database connection

session_start(); // Starting the session to access session variables

$user_id = $_SESSION['user_id']; // Retrieving user ID from session

// Redirect to login page if user ID is not set (not logged in)
if(!isset($user_id)){
   header('location:login.php');
   exit; // Exit to stop further execution
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'header.php'; ?> <!-- Including header.php for common header -->

<div class="heading">
   <h3>Your Orders</h3>
   <p> <a href="home.php">Home</a> / Orders </p>
</div>

<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

   <div class="box-container">

      <?php
         // Query to fetch orders for the logged-in user
         $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('Query failed');
         // Checking if there are orders
         if(mysqli_num_rows($order_query) > 0){
            // Loop through each fetched order
            while($fetch_orders = mysqli_fetch_assoc($order_query)){
      ?>
      <div class="box">
         <!-- Displaying order details -->
         <p> Placed On : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
         <p> Name : <span><?php echo $fetch_orders['name']; ?></span> </p>
         <p> Number : <span><?php echo $fetch_orders['number']; ?></span> </p>
         <p> Email : <span><?php echo $fetch_orders['email']; ?></span> </p>
         <p> Address : <span><?php echo $fetch_orders['address']; ?></span> </p>
         <p> Payment Method : <span><?php echo $fetch_orders['method']; ?></span> </p>
         <p> Total Products : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
         <p> Total Price : <span>GHC <?php echo $fetch_orders['total_price']; ?></span> </p>
         <p> Payment Status : <span style="color:<?php if($fetch_orders['payment_status'] == 'pending'){ echo 'red'; }else{ echo 'green'; } ?>;"><?php echo $fetch_orders['payment_status']; ?></span> </p>
      </div>
      <?php
         }
      }else{
         // Display message if no orders found
         echo '<p class="empty">No orders placed yet!</p>';
      }
      ?>
   </div>

</section>

<?php include 'footer.php'; ?> <!-- Including footer.php for common footer -->

<!-- Custom JavaScript file link -->
<script src="js/script.js"></script>

</body>
</html>
