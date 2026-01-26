<?php
require_once '../config.php';

// Session'ı temizle
session_destroy();
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

// Login sayfasına yönlendir
redirect('login.php');
