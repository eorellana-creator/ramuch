<?php
session_start();
session_destroy();

echo "<script language='javascript'>\nlocation.href='../index.php';\n</script>";

?>