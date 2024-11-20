<?php
require_once("include/connection.php");

echo "<script type='text/javascript' src='js/script.js'></script>
<style type='text/css'>
#list {
    width: 100%;
}
#list a {
    color: #006b68;
}
#list a:hover {
    color: #006b68;
    text-decoration: underline;
}
#list th, td {
    padding: 2px;
    text-align: center;
}

#list tr:nth-child(even) {
    background-color: #CCC;
    opacity: 0.5;
}
#list tr:nth-child(odd) {
}
#list tr:nth-child(1) {
    background-color: #006b68;
    opacity: 0.5;
    color: #fff;
}
</style>";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM transaction WHERE id='{$id}'");
}

if (isset($_GET['pid']) && isset($_GET['q'])) {
    $pid = $_GET['pid'];
    $quan = (float)$_GET['q']; // Explicitly cast $quan to float
    $plist = mysqli_query($conn, "SELECT product_name, cost_price FROM product WHERE product_id='{$pid}'");

    if (!$plist) {
        die("Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($plist)) {
        while ($row = mysqli_fetch_array($plist)) {
            $pname = $row['product_name'];
            $price = (float) $row['cost_price']; // Ensure $price is a numeric type
            $price *= $quan;
        }

        mysqli_query($conn, "INSERT INTO transaction VALUES('{$pname}', $pid, $quan, $price, NULL)");
    }
}

$translist = mysqli_query($conn, "SELECT * FROM transaction");
$transmax = mysqli_query($conn, "SELECT SUM(price) FROM transaction");
$transmax = mysqli_fetch_array($transmax);

if (mysqli_num_rows($translist)) {
    echo "<table id='list' style='width:100%'>
          <tr><th>Product Name</th><th>Quantity</th><th>Price</th><th>Options</th></tr>";

    while ($row = mysqli_fetch_array($translist)) {
        echo "<tr><td>{$row['p_name']}</td><td>{$row['quantity']}</td><td>{$row['price']}</td>
              <td><a href='javascript:delData({$row['id']})'>Delete</a></td>
              </tr>";
    }

    echo "</table><table style='width:100%'><tr><td style='float:right'>Total Rs. {$transmax['SUM(price)']}</td></tr></table>";
} else {
    echo "No items added yet.";
}
?>
