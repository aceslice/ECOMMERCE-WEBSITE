<?php

// Include the configuration file which likely contains the database connection
include 'config.php';

// Check if the registration form is submitted
if (isset($_POST['submit'])) {

   // Sanitize and escape user inputs to prevent SQL injection
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password'])); // Encrypt password using MD5
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword'])); // Encrypt confirm password using MD5
   $telephone = mysqli_real_escape_string($conn, $_POST['tel']);
   $address = mysqli_real_escape_string($conn, $_POST['address']);

   // Check if the user already exists in the database
   $select_users = mysqli_prepare($conn, "SELECT * FROM `users` WHERE email = ? AND password = ?");
   mysqli_stmt_bind_param($select_users, "ss", $email, $pass);
   mysqli_stmt_execute($select_users);
   $result = mysqli_stmt_get_result($select_users) or die('query failed');

   if (mysqli_num_rows($result) > 0) {
      $message[] = 'user already exist!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'confirm password not matched!';
      } else {
         // Use prepared statement to insert user data
         $insert_stmt = mysqli_prepare($conn, "INSERT INTO `users`(name, email, password, user_type, telephone, address) VALUES(?, ?, ?, 'user', ?, ?)");
         mysqli_stmt_bind_param($insert_stmt, "sssss", $name, $email, $cpass, $telephone, $address);
         $exec_result = mysqli_stmt_execute($insert_stmt);

         if ($exec_result) {
            $message[] = 'registered successfully!';
            header('location:login.php');
         } else {
            die('query failed');
         }
      }
   }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Create new account</title>

   <!-- Font Awesome CDN link for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php
   // Display messages if there are any
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
      }
   }
   ?>

   <div class="form-container">

      <form action="" method="post">
         <h3>Create new account</h3>
         <input type="text" name="name" placeholder="Your full name ex. Kwame Nkrumah" required class="box">
         <input type="email" name="email" placeholder="Your email" required class="box">
         <input type="password" name="password" placeholder="Stong password" required class="box">
         <input type="password" name="cpassword" placeholder="Confirm password" required class="box">
         <input type="tel" name="tel" required class="box" placeholder="Phone number ex. +233000000000">
         <input type="text" name="address" required class="box" placeholder="Your address">

         <input type="submit" name="submit" value="register now" class="btn">
         <p>Already have an account? <a href="login.php">Login now</a></p>
      </form>

   </div>

</body>

</html>