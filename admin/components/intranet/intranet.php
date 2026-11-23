<?php
include("includes/intranet.php");
$rolIntranet = intranetExigirAcceso($mysql);
if (!intranetCrearTablas($mysql)) {
    echo '<div class="alert alert-danger">No fue posible preparar el almacenamiento de la Intranet. Contacta al desarrollador.</div>';
    return;
}
include("controller.php");
?>
