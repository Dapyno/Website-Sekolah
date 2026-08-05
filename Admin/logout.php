<?php
session_start();
$_SESSION = array();
session_destroy();
header('Location: login.php?msg=' . urlencode('Anda telah berhasil logout'));
exit();
?>