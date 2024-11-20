<?php
require_once("include/header.php");

// Establish database connection
$conn = new mysqli("localhost", "root", "", "supermarket");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check the query result
function checkQueryResult($result, $successMessage) {
    global $conn;

    if (!$result) {
        echo "Editing not successful: " . $conn->error;
    } else {
        echo "<h1><span>$successMessage</span></h1>";
    }
}

?>

<div id="body">
    <?php include_once("include/left_content.php"); ?>
    <div class="rcontent">

        <?php
        // Check if editing is successful
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo "<h1><span>Editing successful</span></h1>";
        } else {
            // Check if section and id are set
            if (isset($_GET['name']) && isset($_GET['id'])) {
                // product, supplier, customer, or department
                $section = strtolower($_GET['name']);

                // Execute corresponding update query based on the section
                if ($section == "product" && isset($_POST['product_name'])) {
                    $stmt = $conn->prepare("UPDATE product SET product_name=?, cost_price=?, supplier_id=?, quantity=?, product_type=?, market_price=? WHERE product_id=?");
                    $stmt->bind_param("sddissi", $_POST['product_name'], $_POST['cprice'], $_POST['supplier'], $_POST['quantity'], $_POST['product_type'], $_POST['mprice'], $_POST['product_id']);
                    $stmt->execute();
                    checkQueryResult($stmt, "Editing of product data successful");
                } elseif ($section == "supplier" && isset($_POST['name'])) {
                    $stmt = $conn->prepare("UPDATE supplier SET sname=?, saddress=?, sdealer=?, sphone=?, semail=? WHERE sid=?");
                    $stmt->bind_param("ssssi", $_POST['name'], $_POST['address'], $_POST['dealer'], $_POST['phone'], $_POST['email'], $_POST['sid']);
                    $stmt->execute();
                    checkQueryResult($stmt, "Editing of supplier data successful");
                } elseif ($section == "customer" && isset($_POST['fname'])) {
                    $stmt = $conn->prepare("UPDATE customer SET first_name=?, last_name=?, caddress=?, cphone=? WHERE cid=?");
                    $stmt->bind_param("ssssi", $_POST['fname'], $_POST['lname'], $_POST['caddress'], $_POST['cphone'], $_POST['cid']);
                    $stmt->execute();
                    checkQueryResult($stmt, "Editing of customer data successful");
                } elseif ($section == "department" && isset($_POST['dept_name'])) {
                    $stmt = $conn->prepare("UPDATE department SET dept_name=?, manager_id=?, manager_start_date=? WHERE dept_id=?");
                    $stmt->bind_param("sisi", $_POST['dept_name'], $_POST['manager_id'], $_POST['manager_start_date'], $_POST['dept_id']);
                    $stmt->execute();
                    checkQueryResult($stmt, "Editing of department data successful");
                }

                // Display the form for editing
                echo "<div id='data'>";
                $editQuery = $conn->query("SELECT * FROM $section WHERE {$section}_id='{$_GET['id']}'");
                $editData = $editQuery->fetch_assoc();

                echo "<form method='post' action='editlist.php?name=$section&id={$_GET['id']}'>
                            <table>";

                // Display the form fields based on the section
                switch ($section) {
                    case "product":
                        echo "<tr><td style='padding:5px'>Product Name: </td><td><input name='product_name' type='text' value='{$editData['product_name']}' /></td></tr>
                              <input type='hidden' name='product_id' value='{$editData['product_id']}' />
                              <tr><td style='padding:5px'>Product type: </td>
                              <td><select name='product_type'>";

                        // Fetch department data
                        $dept_set = $conn->query("SELECT dept_id, dept_name FROM department");
                        while ($row = $dept_set->fetch_assoc()) {
                            if ($row['dept_id'] == $editData['product_type']) {
                                echo "<option value='{$row['dept_id']}' selected='selected'>{$row['dept_name']}</option>";
                            } else {
                                echo "<option value='{$row['dept_id']}'>{$row['dept_name']}</option>";
                            }
                        }

                        echo "</select></td></tr>
                              <tr><td style='padding:5px'>Supplier ID: </td>
                              <td><select name='supplier'>";

                        // Fetch supplier data
                        $supplier_set = $conn->query("SELECT sid, sname FROM supplier");
                        while ($row = $supplier_set->fetch_assoc()) {
                            if ($row['sid'] == $editData['supplier_id']) {
                                echo "<option value='{$row['sid']}' selected='selected'>{$row['sname']}</option>";
                            } else {
                                echo "<option value='{$row['sid']}'>{$row['sname']}</option>";
                            }
                        }

                        echo "</select></td></tr>
                              <tr><td style='padding:5px'>Quantity: </td><td><input name='quantity' type='text' value='{$editData['quantity']}' /></td></tr>
                              <tr><td style='padding:5px'>Market Price: </td><td><input name='mprice' type='text' value='{$editData['market_price']}' /></td></tr>
                              <tr><td style='padding:5px'>Cost Price: </td><td><input name='cprice' type='text' value='{$editData['cost_price']}' /></td></tr>";
                        break;

                    case "supplier":
                        echo "<tr><td style='padding:5px'>Name: </td><td><input name='name' type='text' value='{$editData['sname']}' /></td></tr>
                              <tr><td style='padding:5px'>Address: </td><td><input name='address' type='text' value='{$editData['saddress']}' /></td></tr>
                              <tr><td style='padding:5px'>Dealer: </td><td><input name='dealer' type='text' value='{$editData['sdealer']}' /></td></tr>
                              <tr><td style='padding:5px'>Phone: </td><td><input name='phone' placeholder='+91..' type='text' value='{$editData['sphone']}'/></td></tr>
                              <input type='hidden' value='{$_GET['id']}' name='sid' />
                              <tr><td style='padding:5px'>Email: </td><td><input name='email' placeholder='name@email.com' type='text' value='{$editData['semail']}'/></td></tr>";
                        break;

                    case "customer":
                        echo "<tr><td style='padding:5px'>First Name: </td><td><input name='fname' type='text' value='{$editData['first_name']}'/></td></tr>
                              <tr><td style='padding:5px'>Last Name: </td><td><input name='lname' type='text' value='{$editData['last_name']}'/></td></tr>
                              <tr><td style='padding:5px'>Address: </td><td><input name='caddress' type='text' value='{$editData['caddress']}' /></td></tr>                    
                              <input type='hidden' name='cid' value='{$_GET['id']}' />                 
                              <tr><td style='padding:5px'>Phone no.</td><td><input type='text' placeholder='+91..' name='cphone' value='{$editData['cphone']}'/></td></tr>";
                        break;

                    case "department":
                        echo "<tr><td style='padding:5px'>Department Name: </td><td><input name='dept_name' type='text' value='{$editData['dept_name']}' /></td></tr>
                              <input type='hidden' name='dept_id' value='{$editData['dept_id']}' />
                              <tr><td style='padding:5px'>Manager ID: </td><td><input name='manager_id' type='text' value='{$editData['manager_id']}' /></td></tr>
                              <tr><td style='padding:5px'>Manager Start Date: </td><td><input name='manager_start_date' type='date' value='{$editData['manager_start_date']}' /></td></tr>";
                        break;
                }

                echo "<tr><td style='padding:5px' colspan='2'><input type='submit' value='submit' /></td></tr>
                            </table>
                          </form>";
                echo "</div>";
            }
        }

        ?>
    </div>
</div>
<!-- body ends -->
<?php
require_once("include/footer.php");

// Close the database connection
$conn->close();
?>
