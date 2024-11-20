<?php
require_once("include/header.php");

$conn = new mysqli("localhost", "root", "", "supermarket");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<div id='body'>";
include_once("include/left_content.php");
echo "<div class='rcontent'>";
echo "<h1><span>Supplier Details:</span></h1>";
echo "<div id='data'>To view the list of suppliers <a style='text-decoration:none' href='viewlist.php?list=supplier'>Click Here</a><br /><br />";

if (isset($_GET['success'])) {
    $dealer = $conn->real_escape_string($_POST['dealer']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);

    // Check if any of the fields is empty
    if (empty($name) || empty($address) || empty($dealer) || empty($phone) || empty($email)) {
        echo "<script>alert('Please fill in all the fields.');</script>";
    } else {
        // Check if the phone number already exists
        $checkQuery = $conn->query("SELECT * FROM supplier WHERE sphone='$phone'");
        
        if ($checkQuery->num_rows > 0) {
            echo "<script>alert('Addition not successful: Phone number already exists.');</script>";
        } else {
            // Insert the new supplier data
            $result = $conn->query("INSERT INTO supplier (sname, saddress, sdealer, sphone, semail) VALUES ('$name', '$address', '$dealer', '$phone', '$email')");

            if (!$result) {
                echo "Addition not successful: " . $conn->error;
            } else {
                echo "Addition of supplier data successful";
            }
        }
    }
} else {
    echo "<form method='post' action='addsupplier.php?success=1' onsubmit='return validateForm()'>
              <table>
                <tr><td style='padding:5px'>Name: </td><td><input name='name' type='text' /></td></tr>
                <tr><td style='padding:5px'>Address: </td><td><input name='address' type='text' /></td></tr>
                <tr><td style='padding:5px'>Dealer: </td><td><input name='dealer' type='text' /></td></tr>
                <tr><td style='padding:5px'>Phone: </td><td><input name='phone' placeholder='+91..' type='text' /></td></tr>
                <tr><td style='padding:5px'>Email: </td><td><input name='email' placeholder='name@email.com' type='text' /></td></tr>
                <tr><td style='padding:5px' colspan='2'><input type='submit' value='submit' /></td></tr>
              </table>
            </form>";

    // JavaScript function for form validation
    echo "<script>
            function validateForm() {
                var name = document.forms[0]['name'].value;
                var address = document.forms[0]['address'].value;
                var dealer = document.forms[0]['dealer'].value;
                var phone = document.forms[0]['phone'].value;
                var email = document.forms[0]['email'].value;

                if (name === '' || address === '' || dealer === '' || phone === '' || email === '') {
                    alert('Please fill in all the fields.');
                    return false;
                }

                return true;
            }
          </script>";
}

echo "</div>";
echo "</div>";
echo "</div>";

include_once("include/footer.php");

$conn->close();
?>
