<?php   
require_once 'brcache/BRCacheConnection.php';

$con = new BRCacheConnection();
$con->setAutoCommit(false);
$con->put("teste", "teste_value");
$value = $con->get("teste");
$con->commit();
echo $value;
?>