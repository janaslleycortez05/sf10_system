<?php
$conn = new mysqli("localhost","root","","sf10_system");
if ($conn->connect_error) die("DB Error");
session_start();
?>