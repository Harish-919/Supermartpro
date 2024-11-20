<?php
// checkUid.php

$uid = $_GET['uid'];
$conn = new mysqli("localhost", "root", "", "supermarket");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$uidCheckQuery = $conn->prepare("SELECT id FROM employee WHERE uid = ?");
$uidCheckQuery->bind_param("s", $uid);
$uidCheckQuery->execute();
$uidCheckResult = $uidCheckQuery->get_result();

if ($uidCheckResult->num_rows > 0) {
    echo "Duplicate 'uid'. Please provide a unique 'uid'.";
} else {
    echo "UID is unique.";
}

$uidCheckQuery->close();
$conn->close();
?>
