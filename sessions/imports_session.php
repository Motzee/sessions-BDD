<?php   
session_start();

function myLoader($className) {
    $class = str_replace('\\', '/', $className) ;
    require_once($class.'.php') ;
}

spl_autoload_register('myLoader') ;

$db = new sessions\DbUsers() ;

if(isset($_SESSION['id']) && $_SESSION['id'] != NULL) {
    $user = $db->recupererUser($_SESSION['id']) ;
}
