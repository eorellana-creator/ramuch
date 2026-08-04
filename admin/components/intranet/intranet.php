<?php
include("includes/intranet.php");
$rolIntranet = intranetExigirAcceso($mysql);
intranetCrearTablas($mysql);
include("controller.php");
?>
