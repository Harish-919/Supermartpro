<?php 
require_once("include/header.php");
?>

<div id="body">
    <?php include_once("include/left_content.php"); ?>
    
    <div class="rcontent">
        <h1><span>Hello<?php echo " " . ucfirst($_SESSION['username']) ?></span></h1>

        <div id="contentbox">
            <div id="data">Status:<br />
                <?php
                // Establish a MySQLi connection (assuming $conn is your MySQLi connection object)
                $query = "SELECT SUM(total_amount), SUM(profit) FROM buy";
                $result = $conn->query($query);

                // Check for errors
                if (!$result) {
                    die("Query failed: " . $conn->error);
                }

                $moneylist = $result->fetch_array();
                echo "<b>Earnings</b><br />
                      Overall Earnings: Rs. {$moneylist['SUM(total_amount)']}<br /><br />
                      <b>Profits</b><br />
                      Overall Profits: Rs. {$moneylist['SUM(profit)']}<br /><br />";

                // Close the result set
                $result->close();
                ?>
            </div>
        </div>
    </div>
</div>

<?php 
require_once("include/footer.php");
?>
