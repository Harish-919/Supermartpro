<?php
require_once("include/header.php");

// Assuming you've created a MySQLi connection object
$conn = new mysqli("localhost", "root", "", "supermarket");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<div id='body'>";
include_once("include/left_content.php");
echo "<div class='rcontent'>";
echo "<h1><span>Customer Details:</span></h1>";
echo "<div id='data'>To view the list of customers <a style='text-decoration:none' href='viewlist.php?list=customer'>Click Here</a><br /><br />";

if (isset($_GET['success'])) {
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $cjoindate = date("Y-m-d");
    $cmoneyspent = 0; // Assuming default value
    $caddress = $conn->real_escape_string($_POST['caddress']);
    $cmoney_spent_reset = 0; // Assuming default value
    $cphone = $conn->real_escape_string($_POST['cphone']);

	$query = $conn->prepare("INSERT INTO customer (first_name, last_name, cjoin_date, cmoney_spent, caddress, cmoney_spent_reset, cphone) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$query->bind_param("sssdiss", $fname, $lname, $cjoindate, $cmoneyspent, $caddress, $cmoney_spent_reset, $cphone);
	
    if ($query->execute()) {
        echo "Addition of customer data successful";
    } else {
        echo "Addition not successful: " . $conn->error;
    }

    $query->close();
} else {
    $time = date("Y-m-d");
    echo "<form method='post' action='addcustomer.php?success=1'>
            <table>
                <tr><td style='padding:5px'>First Name: </td><td><input name='fname' type='text' /></td></tr>
                <tr><td style='padding:5px'>Last Name: </td><td><input name='lname' type='text' /></td></tr>
                <tr><td style='padding:5px'>Address: </td><td><input name='caddress' type='text' /></td></tr>
                <input name='cjoindate' type='hidden' value='{$time}' />
                <input name='cmoneyspent' type='hidden' value='0'/>
                <input type='hidden' name='cmoney_spent_reset' value='0' />
                <tr><td style='padding:5px'>Phone no.</td><td><input type='text' placeholder='+91..' name='cphone' /></td></tr>
                <tr><td colspan='2'><input type='submit' value='submit' /></td></tr>
            </table>
        </form>";
}

echo "</div>";
echo "</div>";
echo "</div>";

include_once("include/footer.php");

$conn->close();
?>
