<?php 
	require_once("include/header.php");
?>
<div id="body">
	<?php include_once("include/left_content.php"); ?>
    <div class="rcontent">
        <h1><span>Add Department:</span></h1>
        <div id="data">To view the list of departments <a style="text-decoration:none" href="viewlist.php?list=dept">Click Here</a><br /><br />
        <?php 
			// Establish a MySQLi connection
			$conn = new mysqli("localhost", "root", "", "supermarket");

			if ($conn->connect_error) {
			    die("Connection failed: " . $conn->connect_error);
			}

			if(isset($_GET['success'])){
				// Use prepared statements to prevent SQL injection
				$stmt = $conn->prepare("INSERT INTO department (manager_id, dept_name, manager_start_date) VALUES (?, ?, ?)");
				$stmt->bind_param("iss", $mid, $dname, $manager_start_date);

				$mid = $_POST['mid'];
				$dname = $_POST['dname'];
				$manager_start_date = $_POST['doj'];  // Assuming 'doj' is the form field for manager_start_date

				if($stmt->execute()) {
					echo "Addition of Department data successful";
				} else {
					echo "Addition not successful: " . $stmt->error;
				}

				$stmt->close();
			}
			else {
				$time = date("Y-m-d");
				echo "<form method='post' action='adddept.php?success=1'>
					  <table>
					    <tr><td style='padding:5px'>Dept Name: </td><td><input name='dname' type='text' /></td></tr>
						<tr><td style='padding:5px'>Manager: </td>
						<td><select name='mid'>
							<option value='NULL'>NULL</option>
							<option value='1'>Harry Denn</option>"; // Assuming Harry Denn has an ID of 1

				$manager_set = $conn->query("SELECT id, first_name, last_name FROM employee WHERE admin='1' AND dept_id='0'");
				while($row = $manager_set->fetch_assoc())
					echo "<option value='{$row['id']}'>{$row['first_name']} {$row['last_name']}</option>";

				echo "</select></td>
						</tr>						
						<tr><td style='padding:5px' colspan='2'>
							<input type='hidden' name='doj' value='{$time}' />
							<input type='submit' value='submit' /></td></tr>
					  </table></form>";
			} 

			// Close the MySQLi connection
			$conn->close();
		?>
        </div>
    </div>
</div>
<!-- body ends -->
<?php 
	require_once("include/footer.php");
?>
