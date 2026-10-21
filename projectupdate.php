<?php 
    session_start();

     if(!isset($_SESSION['uid'])){
        
    }

    $_SESSION['pid'] = $_POST['pid'];

    header('Location: /index.php');
    exit();
?>