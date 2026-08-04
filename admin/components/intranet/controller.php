<?php
$view = $_GET['view'] ?? 'dashboard';
if ($view === 'dashboard') {
    include('views/dashboard.php');
}
?>
