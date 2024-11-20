<?php
require_once("include/functions.php");
require_once("include/header.php");

// Create a MySQLi connection
$connection = new mysqli("localhost", "root", "", "supermarket");

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

?>

<div id="body">
    <?php include_once("include/left_content.php") ?>
    <div class="rcontent">
        <h1><span>Settings:</span></h1>
        <div id='contentbox'>
            <div id="data">
                <?php
                // Perform query
                $query = "SELECT * FROM login WHERE id ='{$_SESSION['user_id']}'";
                $result = $connection->query($query);

                if ($result === false) {
                    die("Query failed: " . $connection->error);
                }

                $row = $result->fetch_assoc();

                if (isset($_GET['change_pass']) && $_GET['change_pass'] == 1) {
                    if (isset($_GET['up_pass']) && $_GET['up_pass'] == 1) {
                        // Password update logic
                        if ($row['password'] == md5($_POST['old_pass']) && $_POST['new_pass'] == $_POST['new_pass_confirm']) {
                            $newPassword = $connection->real_escape_string(md5($_POST['new_pass']));
                            $updateQuery = "UPDATE login SET password = '{$newPassword}' WHERE id = {$_SESSION['user_id']}";
                            $success = $connection->query($updateQuery);

                            if ($success) {
                                echo "Password changed successfully.<br />";
                            } else {
                                echo "Password changing failed. Please <a href='settings.php?change_pass=1'>retry</a>";
                            }
                        } else {
                            echo "Password changing failed. Please <a href='settings.php?change_pass=1'>retry</a>";
                        }
                    } else {
                        // Display password change form
                        echo "Change your password.";
                        echo "<form method='post' action='settings.php?change_pass=1&up_pass=1'>
                                <table>
                                    <tr><td>Old Password:</td><td><input type='password' name='old_pass' /></td></tr>
                                    <tr><td>New password:</td><td><input type='password' name='new_pass' /></td></tr>
                                    <tr><td>Re-type password:</td><td><input type='password' name='new_pass_confirm' /></td></tr>
                                    <tr><td colspan='2'><input type='submit' value='update' /></td></tr>
                                </table>
                              </form>";
                    }
                } elseif ((isset($_GET['del_acc']) && $_GET['del_acc'] == 1) || (isset($_GET['del_other_acc']) && $_GET['del_other_acc'] == 1)) {
                    if (isset($_GET['del_confirm']) && $_GET['del_confirm'] == 1) {
                        // Delete account logic
                        $deleteQuery = "DELETE FROM login WHERE id={$_SESSION['user_id']}";
                        if (isset($_GET['id']) && $_SESSION["admin"] == 1) {
                            // Delete other account logic
                            $deleteQuery = "DELETE FROM login WHERE id={$_GET['id']}";
                        }
                        $success = $connection->query($deleteQuery);

                        if ($success) {
                            echo "Deletion Successful";
                            if (isset($_SESSION["admin"]) && $_SESSION["admin"] == 1) {
                                echo " of Employee ID {$_GET['id']}";
                            }
                            session_destroy();
                            header("Location: login.php");
                        } else {
                            echo "Deletion Unsuccessful";
                        }
                    } else {
                        // Display delete account confirmation
                        $confirmText = isset($_SESSION["admin"]) && $_SESSION["admin"] == 1 ? "of Employee ID {$_GET['id']}" : "";
                        echo "Are you sure you want to delete your account?
                                <a href='settings.php?del_acc=1&del_confirm=1{$confirmText}'><button>Yes</button></a>&nbsp;
                                <a href='settings.php'><button>No</button></a>";
                    }
                } else {
                    // Display other settings menu
                    if ($row["admin"] == 0) {
                        echo $row["username"] . " is not an admin<br />";
                        echo "<a href='settings.php?change_pass=1' >Change Password</a><br /><a href='settings.php?del_acc=1' >Delete account</a><br />";
                    } else {
                        echo $row["username"] . " is an admin<br />";
                        echo "<a href='settings.php?change_pass=1' >Change Password</a><br /><a href='settings.php?del_acc=1' >Delete account</a><br />";
                        echo "<a href='settings.php?del_other_acc=1' >Delete other accounts</a><br />";
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- body ends -->
<?php
require_once("include/footer.php");
?>
