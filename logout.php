<?php
// logout.php

// Khởi tạo session
session_start();

// Hủy tất cả các biến session
session_unset();

// Hủy session
session_destroy();

// Chuyển hướng về login.php
header("Location: login.php");
exit();
?>