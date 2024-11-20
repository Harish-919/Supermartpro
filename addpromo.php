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
echo "<h1><span>Add Promo:</span></h1>";
echo "<div id='data'>To view the list of promos <a style='text-decoration:none' href='viewlist.php?list=promo'>Click Here</a><br /><br />";

if (isset($_GET['success'])) {
    $discount = $conn->real_escape_string($_POST['discount']);
    $rawDate = $_POST['valid'];
    $dateTime = DateTime::createFromFormat('Y-m-d', $rawDate);

    if ($dateTime && $dateTime->format('Y-m-d') === $rawDate) {
        // The date is valid and formatted correctly
        $date = $rawDate;

        $query = $conn->prepare("INSERT INTO promotion (discount, valid_upto, promo_code, count) VALUES (?, ?, NULL, 0)");
        $query->bind_param("ss", $discount, $date);

        if ($query->execute()) {
            echo "Addition of promo data successful";
        } else {
            echo "Addition not successful: " . $conn->error;
        }

        $query->close();
    } else {
        // The date is not in the correct format
        echo "Invalid date format. Please enter the date in the 'Y-m-d' format.";
    }
} else {
    echo "<form method='post' action='addpromo.php?success=1'><table>
          <tr><td style='padding:5px'>Discount:</td><td><input type='text' placeholder='%' name='discount' /></td></tr>
          <tr><td style='padding:5px'>Valid Upto:</td><td><input type='text' name='valid' /></td></tr>
          <tr><td style='padding:5px' colspan='2'><input type='submit' value='submit' /></td></tr>
          </table></form>";
}

echo "</div>";
echo "</div>";
echo "</div>";

include_once("include/footer.php");

$conn->close();
?>
