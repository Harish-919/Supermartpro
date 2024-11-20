<?php
require_once("include/header.php");

$conn = new mysqli("localhost", "root", "", "supermarket");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<div id='body'>";
include_once("include/left_content.php");
echo "<div class='rcontent'>";
echo "<h1><span>Add Employee:</span></h1>";
echo "<div id='data'>To view the list of employees <a style='text-decoration:none' href='viewlist.php?list=employee'>Click Here</a><br /><br />";

// Include JavaScript for UID uniqueness check
echo "<script>
    function checkUid() {
        var uid = document.getElementById('uid').value;

        if (uid.trim() !== '') {
            var xmlhttp = new XMLHttpRequest();

            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('uidMessage').innerHTML = this.responseText;
                }
            };

            xmlhttp.open('GET', 'checkUid.php?uid=' + uid, true);
            xmlhttp.send();
        } else {
            document.getElementById('uidMessage').innerHTML = '';
        }
    }
</script>";

if (isset($_GET['third']) && isset($_POST['user'])) {
    $user = $conn->real_escape_string($_POST['user']);
    $password = md5($_POST['password']);
    $admin = $conn->real_escape_string($_POST['admin']);
    $id = $conn->real_escape_string($_POST['id']);

    $userQuery = $conn->prepare("INSERT INTO login (username, password, id, admin) VALUES (?, ?, ?, ?)");
    $userQuery->bind_param("ssii", $user, $password, $id, $admin);
    
    $userResult = $userQuery->execute();

    if (!$userResult) {
        echo "Addition not successful: " . $conn->error;
    } else {
        echo "Addition of employee user data successful";
    }
    $userQuery->close();
}

elseif (isset($_GET['third'])) {
    echo "You are not supposed to visit this page. Please go <a href='addemp.php'>back</a>";
}

// Second page
if (isset($_GET['second']) && isset($_POST['fname'])) {
    $fname = $conn->real_escape_string($_POST['fname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $dept_id = $conn->real_escape_string($_POST['dept_id']);
    $salary = $conn->real_escape_string($_POST['salary']);
    $pnum = $conn->real_escape_string($_POST['pnum']);
    $address = $conn->real_escape_string($_POST['address']);
    $uid = $conn->real_escape_string($_POST['uid']);
    $jdate = $conn->real_escape_string($_POST['jdate']);
    $bdate = $conn->real_escape_string($_POST['bdate']);
    $edate = $conn->real_escape_string($_POST['edate']);
    $perks = $conn->real_escape_string($_POST['perks']);
    $admin = $conn->real_escape_string($_POST['admin']);

    // Check if the uid already exists
    $uidCheckQuery = $conn->prepare("SELECT id FROM employee WHERE uid = ?");
    $uidCheckQuery->bind_param("s", $uid);
    $uidCheckQuery->execute();
    $uidCheckResult = $uidCheckQuery->get_result();

    if ($uidCheckResult->num_rows > 0) {
        echo "Error: Duplicate 'uid'. Please provide a unique 'uid'.";
    } else {
        // 'uid' is unique, proceed with the insertion
        $employeeQuery = $conn->prepare("INSERT INTO employee (first_name, last_name, dept_id, salary, phone_number, address, uid, join_date, dob, end_date, perks, admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $employeeQuery->bind_param("ssiiisssisii", $fname, $lname, $dept_id, $salary, $pnum, $address, $uid, $jdate, $bdate, $edate, $perks, $admin);

        $employeeResult = $employeeQuery->execute();

        if (!$employeeResult) {
            echo "Addition of employee data not successful: " . $conn->error;
        } else {
            $empidResult = $conn->query("SELECT id FROM employee WHERE uid='$uid'");
            $empidRow = $empidResult->fetch_assoc();
            $empid = $empidRow['id'];

            echo "<form method='post' action='addemp.php?third=1'>
        <table>
            <!-- other input fields -->
            <tr><td style='padding:5px'>Username:</td>
                <td><input type='text' name='user' /></td></tr>
            <tr><td style='padding:5px'>Password:</td>
                <td><input type='password' name='password' /></td></tr>
            <input type='hidden' name='admin' value='$admin' />
            <input type='hidden' name='id' value='$empid' />
            <tr><td colspan='2' style='padding:5px'><input type='submit' value='submit' /></td></tr>
        </table>
    </form>";


            echo "Addition of employee data successful";
        }
        $employeeQuery->close();
    }

    $uidCheckQuery->close();
}

// First page
else if (!isset($_GET['second'])) {
    $time = date("Y-m-d");
    echo "<form method='post' action='addemp.php?second=1'>
            <table>
                <tr><td style='padding:5px'>First Name:</td>
                    <td><input type='text' name='fname' /></td></tr>
                <tr><td style='padding:5px'>Last Name:</td>
                    <td><input type='text' name='lname' /></td></tr>
                <tr><td style='padding:5px'>Dept: </td>
                    <td><input list='depts' name='dept_id' placeholder='0' value='NULL'>
                            <datalist id='depts'>";

    $deptResult = $conn->query("SELECT dept_id, dept_name FROM department WHERE manager_id='0'");
    while ($row = $deptResult->fetch_assoc()) {
        echo "<option value='{$row['dept_id']}'>{$row['dept_name']}</option>";
    }

    echo "</datalist>
                    </td></tr>
                <tr><td style='padding:5px'>Salary</td>
                    <td><input type='text' name='salary' /></td></tr>
                <tr><td style='padding:5px'>Phone No.</td>
                    <td><input type='text' placeholder='+91..' name='pnum' /></td></tr>
                <tr><td style='padding:5px'>Address</td>
                    <td><input type='text' name='address' /></td></tr>
                <tr><td style='padding:5px'>Uid</td>
                    <td>
                        <input type='text' name='uid' id='uid' onblur='checkUid()' />
                        <div id='uidMessage'></div>
                    </td>
                </tr>
                <tr><td style='padding:5px'>Dob</td>
                    <td><input type='text' name='bdate' placeholder='YYYY-MM-DD' /></td></tr>
                <input type='hidden' name='jdate' value='{$time}' />
                <input type='hidden' name='edate' value='0000-00-00' />
                <input type='hidden' name='perks' value='0'/>
                <tr><td style='padding:5px'>Admin</td><td><select name='admin'>
                        <option value='1'>Admin</option>
                        <option value='0'>Not Admin</option>
                    </select></td></tr>
                <tr><td colspan='2'><input type='submit' name='submit' value='Submit' /></td></tr>
            </table>
        </form>";
}

echo "</div>";
echo "</div>";
echo "</div>";

include_once("include/footer.php");

$conn->close();
?>
