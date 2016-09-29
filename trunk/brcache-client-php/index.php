<?php   
require_once 'brcache/BRCacheConnection.php';

$con = new BRCacheConnection();
$con->put("teste", "teste_value");
$value = $con->get("teste");
echo $value;
?>