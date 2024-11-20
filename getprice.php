<?php
require_once("include/connection.php");

if (isset($_GET['pid'])) {
    $conn = new mysqli("localhost", "root", "", "supermarket");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $pid = $conn->real_escape_string($_GET['pid']);
    $result = $conn->query("SELECT product_name, cost_price, quantity FROM product WHERE product_id='$pid'");

    while ($row = $result->fetch_assoc()) {
        echo "{$row['product_name']},{$row['cost_price']},{$row['quantity']}";
    }

    $conn->close();
} elseif (isset($_GET['pname'])) {
    $conn = new mysqli("localhost", "root", "", "your_database_name");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $pname = $conn->real_escape_string($_GET['pname']);
    $result = $conn->query("SELECT product_id, cost_price, quantity FROM product WHERE product_name='$pname'");

    while ($row = $result->fetch_assoc()) {
        echo "{$row['product_id']},{$row['cost_price']},{$row['quantity']}";
    }

    $conn->close();
}
?>
