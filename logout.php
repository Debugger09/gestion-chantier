<?php
require_once 'includes/config.php';
session_start();
session_destroy();
redirect('login.php');
?>