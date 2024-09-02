<?php
session_start();
include "config.php";

// Check if user session exists
if (!isset($_SESSION["user_id"]) || !isset($_SESSION["user_email"])) {
  exit("User is not logged in.");
}

$user_id = $_SESSION["user_id"];
$user_email = $_SESSION["user_email"];

// Connect to the database and select cart items for the given user
$query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');


// Fetch all rows from the query result
$rows = mysqli_fetch_all($query, MYSQLI_ASSOC);

// Initialize total amount
$total = 0;

if ($rows) {
  // Iterate over each row to calculate the total amount
  foreach ($rows as $row) {
    $total += $row['price'];
  }
} else {
  echo "No items in the cart.";
  exit; // Stop script execution if the cart is empty
}

$url = "https://api.paystack.co/transaction/initialize";
$host = "http://localhost/ECOMMERCE%20WEBSITE";
$fields = [
  'email' => $user_email,
  'amount' => $total * 100,
  'callback_url' => $host."/verify.php",
  'metadata' => ["cancel_action" => $host."/home.php"]
];

$fields_string = http_build_query($fields);

// Open connection
$ch = curl_init();

// Set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt(
  $ch,
  CURLOPT_HTTPHEADER,
  array(
    "Authorization: Bearer sk_test_24a07dc3e2d254eb33a5183534fb5543b66aa860",
    "Cache-Control: no-cache",
  )
);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute post
$result = json_decode(curl_exec($ch), true);

if ($result === false) {
  die('CURL Error: ' . curl_error($ch));
}
print_r($result);
if (isset($result['data']['authorization_url'])) {
  $payment_url = $result['data']['authorization_url'];
  header("Location: $payment_url");
} else {
  echo "Failed to initialize payment.";
}

// Close CURL session
curl_close($ch);
