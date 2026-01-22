<?php
function redirecciona($url){
$script = "<script language='javascript'>\nlocation.href='". $url ."';\n</script>";
return $script;
}

?>