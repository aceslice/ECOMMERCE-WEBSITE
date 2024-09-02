<?php
include_once ("config.php");
// Start or resume a session
session_start();

// Check if a reference is provided in the URL query parameters
if (isset($_GET["reference"])) {
    // Retrieve the reference from the URL query parameters
    $reference = $_GET["reference"];
}
$user_id = $_SESSION["user_id"];
// Initialize a cURL session
$curl = curl_init();

// Set multiple options for a cURL transfer
curl_setopt_array(
    $curl,
    array(
        // Specify the URL to fetch. This can also be set when initializing a session with curl_init().
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
        // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it directly.
        CURLOPT_RETURNTRANSFER => true,
        // The contents of the "Accept-Encoding: " header. This enables decoding of the response. "" means all supported encodings.
        CURLOPT_ENCODING => "",
        // The maximum amount of HTTP redirections to follow. Use this to stop after 10 redirects.
        CURLOPT_MAXREDIRS => 10,
        // The maximum number of seconds to allow cURL functions to execute.
        CURLOPT_TIMEOUT => 30,
        // Specifies the HTTP protocol version to use.
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // Custom request method to use instead of "GET" or "HEAD" when doing a HTTP request. This is useful for doing "GET" requests.
        CURLOPT_CUSTOMREQUEST => "GET",
        // An array of HTTP header fields to set, in the format array('Content-type: text/plain', 'Content-length: 100')
        CURLOPT_HTTPHEADER => array(
            // Authorization header with the API key
            "Authorization: Bearer sk_test_24a07dc3e2d254eb33a5183534fb5543b66aa860",
            // Cache-Control header to indicate that the response should not be cached
            "Cache-Control: no-cache",
        ),
    )
);

// Execute the cURL session
// $response = json_decode(curl_exec($curl), true);
$response = json_decode(curl_exec($curl), true);
$res = curl_exec($curl);


// Retrieve the error string of the current cURL session
$err = curl_error($curl);

// Close a cURL session
curl_close($curl);

// Check if there was an error during the cURL session
if ($err) {
    // Output the cURL error
    echo "cURL Error #:" . $err;
} else {
    // Output the response from the cURL session

    if (isset($response['data']['status']) && $response['data']['status'] === "success") {
        // print_r($response);
        print_r($_SESSION);
        $name = $_SESSION['user_name'];
        $number = $_SESSION['telephone'];
        $email = $_SESSION['user_email'];
        $method = $response['data']['channel'];
        $address = $_SESSION['address'];
        $placed_on = date('l, F j, Y', strtotime($response['data']['paid_at']));
        echo $method;
        echo $placed_on;

        $cart_total = 0;
        $cart_products[] = '';

        $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($cart_query) > 0) {
            while ($cart_item = mysqli_fetch_assoc($cart_query)) {
                $cart_products[] = $cart_item['name'] . ' (' . $cart_item['quantity'] . ') ';
                $sub_total = ($cart_item['price'] * $cart_item['quantity']);
                $cart_total += $sub_total;
            }
        }

        $total_products = implode(', ', $cart_products);

        $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND method = '$method' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');

        if ($cart_total == 0) {
            $message[] = 'your cart is empty';
        } else {
            if (mysqli_num_rows($order_query) > 0) {
                $message[] = 'order already placed!';
            } else {
                mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, placed_on, payment_status) VALUES('$user_id', '$name', '$number', '$email', '$method', '$address', '$total_products', '$cart_total', '$placed_on', 'completed')") or die('query failed');
                $message[] = 'order placed successfully!';
                mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                header("Location: orders.php");
            }
        }

    } else {
        echo "Failed to verify payment.";
    }
}