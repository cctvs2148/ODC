<?php
require_once __DIR__ . '/../includes/session.php';
session_start();
session_unset();
session_destroy();
header('Location: /ODC/index.php');
exit;
