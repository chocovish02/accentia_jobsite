<?php
$conn = new mysqli("localhost", "root", "", "accentia_jobs");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
