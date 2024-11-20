<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("include/header.php");

?>

<div id="body">
    <?php include_once("include/left_content.php"); ?>
    <div class="rcontent">
        <h1><span>Sell Status:</span></h1>
        <div id="contentbox">
            <?php
            $time = date("Y-m-d");
            $discount = 0;
            $pid = [];

            //pids
            $pidlist = mysqli_query($conn, "SELECT pid FROM transaction");

            while ($row = mysqli_fetch_array($pidlist)) {
                $pid[] = $row['pid'];
            }

            $pids = implode(",", $pid);

            //total amount
            $data = mysqli_query($conn, "SELECT SUM(price) FROM transaction");
            $data = mysqli_fetch_array($data);
            $totamo = $data['SUM(price)'];

            $promo = isset($_POST['discount']) ? $_POST['discount'] : 0;

            if ($promo != 0) {
                $promolist = mysqli_query($conn, "SELECT discount, valid_upto FROM promotion WHERE promo_code='{$promo}'");

                if (mysqli_num_rows($promolist) > 0) {
                    $promolist = mysqli_fetch_array($promolist);
                    $valid_upto = date("Y-m-d", strtotime($promolist['valid_upto']));

                    if ($valid_upto >= $time) {
                        mysqli_query($conn, "UPDATE promotion SET count=count+1 WHERE promo_code='{$promo}'");
                        $discount = ($totamo * $promolist['discount']) / 100;
                        $totamo = $totamo - $discount;
                    }
                }
            }

            // profit, profit-discount error
            $profit = 0;


$flag = 1;

$data = mysqli_query($conn, "SELECT pid, quantity FROM transaction");

while ($row = mysqli_fetch_array($data)) {
    $temp = mysqli_query($conn, "SELECT cost_price, market_price, quantity, product_name FROM product WHERE product_id='{$row['pid']}'");
    $temp = mysqli_fetch_array($temp);

    if ($row['quantity'] > $temp['quantity'] || $row['quantity'] <= 0) {
        echo "Error with quantity values for product '{$temp['product_name']}'. ";
        echo "Transaction quantity: {$row['quantity']}, Available quantity: {$temp['quantity']}<br />";
        $flag = 0;
    }

    $profit += $row['quantity'] * ($temp['cost_price'] - $temp['market_price']);
}


            $profit -= $discount;

            if ($flag) {
                $cid = isset($_POST['cid']) ? $_POST['cid'] : 0;

                if ($cid != 0) {
                    $clist = mysqli_query($conn, "SELECT first_name, last_name, cmoney_spent FROM customer WHERE cid='{$cid}'");
                    $clist = mysqli_fetch_array($clist);

                    echo "Hello " . $clist['first_name'] . " " . $clist['last_name'] . ", your previous balance is Rs. " . $clist['cmoney_spent'] . "<br />";
                    mysqli_query($conn, "UPDATE customer SET cmoney_spent=cmoney_spent+'{$totamo}' WHERE cid='{$cid}'");
                    echo "New balance: Rs. " . ($clist['cmoney_spent'] + $totamo) . "<br />";
                }

                $result = mysqli_query($conn, "INSERT INTO buy VALUES (NULL,'{$time}','{$pids}', $totamo, $profit, $cid)");

                if ($result) {
                    echo "<div id='data'><br />Items Sold:<br />";

                    // Display items sold
                    $data = mysqli_query($conn, "SELECT p.product_name, t.quantity, p.cost_price, p.market_price FROM transaction t JOIN product p ON t.pid = p.product_id");

                    while ($row = mysqli_fetch_array($data)) {
                        $itemTotal = $row['quantity'] * ($row['cost_price'] - $row['market_price']);
                        echo "{$row['quantity']} x {$row['product_name']} - Rs. {$itemTotal}<br />";
                    }

                    echo "<br />Total Bill Value: Rs. {$totamo}</div>";

                    // lessen the quantity
                    $data = mysqli_query($conn, "SELECT pid, quantity FROM transaction");

                    while ($row = mysqli_fetch_array($data)) {
                        mysqli_query($conn, "UPDATE product SET quantity = quantity-'{$row['quantity']}' WHERE product_id='{$row['pid']}'");
                    }

                    mysqli_query($conn, "TRUNCATE TABLE transaction");
                } else {
                    echo "Error in transaction. Please <a href='transaction.php'>retry</a>";
                }
            } else {
                echo "Error with quantity values. Please check again... <a href='transaction.php'>Go Back</a>";
            }
            ?>
        </div>
    </div>
</div>
<!-- body ends -->
<?php
require_once("include/footer.php");
?>
