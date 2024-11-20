<?php
require_once("include/header.php");

// Establish database connection
$conn = new mysqli("localhost", "root", "", "supermarket");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['success'])) {
    if ($_GET['success'] == 1) {
        echo "<h1><span>Addition of product data successful</span></h1>";
    } else {
        echo "<h1><span>Addition not successful</span></h1>";
    }
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check for empty fields
        if (empty($_POST['product_name']) || empty($_POST['product_type']) || empty($_POST['supplier']) || empty($_POST['quantity']) || empty($_POST['mprice']) || empty($_POST['cprice'])) {
            echo "<script>alert('Please fill in all the required fields');</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO product (cost_price, supplier_id, product_name, quantity, product_type, market_price) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("dissid", $_POST['cprice'], $_POST['supplier'], $_POST['product_name'], $_POST['quantity'], $_POST['product_type'], $_POST['mprice']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                header("Location: addproduct.php?success=1");
                exit();
            } else {
                echo "Addition not successful: " . $conn->error;
            }

            $stmt->close();
        }
    }

    echo "<div id='body'>";
    include_once("include/left_content.php");
    echo "<div class='rcontent'>";
    echo "<h1><span>Add Product</span></h1>";
    echo "<div id='data'>To view the list of products <a style='text-decoration:none' href='viewlist.php?list=product'>Click Here</a><br /><br />";

    echo "<form method='post' action='addproduct.php'>
              <table>
                <tr><td style='padding:5px'>Product Name: </td><td><input name='product_name' type='text' /></td></tr>
                <tr><td style='padding:5px'>Product type: </td>
                <td><select name='product_type'>";

    $dept_set = $conn->query("SELECT dept_id, dept_name FROM department");
    while ($row = $dept_set->fetch_assoc()) {
        echo "<option value='{$row['dept_id']}'>{$row['dept_name']}</option>";
    }

    echo "</select>
                </td></tr>
                <tr><td style='padding:5px'>Supplier ID: </td>
                <td><select name='supplier'>";

    $supplier_set = $conn->query("SELECT sid, sname FROM supplier");
    while ($row = $supplier_set->fetch_assoc()) {
        echo "<option value='{$row['sid']}'>{$row['sname']}</option>";
    }

    echo "</select></td></tr>
                <tr><td style='padding:5px'>Quantity: </td><td><input name='quantity' type='text' /></td></tr>
                <tr><td style='padding:5px'>Market Price: </td><td><input name='mprice' type='text' /></td></tr>
                <tr><td style='padding:5px'>Cost Price: </td><td><input name='cprice' type='text' /></td></tr>
                <tr><td style='padding:5px' colspan='2'><input type='submit' value='submit' /></td></tr>
              </table>
            </form>";

    echo "</div>";
    echo "</div>";
    echo "</div>";

    include_once("include/footer.php");

    // Close the connection
    $conn->close();
}
?>