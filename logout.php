<?php
// logout.php
require_once __DIR__ . '/includes/session.php';
$_SESSION = [];
session_destroy();
header('Location: /ecall/auth/login.php');
exit;
