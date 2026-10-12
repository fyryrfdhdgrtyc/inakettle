<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    session_start();

    if(!isset($_SESSION['uid'])){
        header('Location: /loginpage.php');
        exit();
    }


    $db = new SQLite3('inakettle.db');
    $uid = $_SESSION['uid'];

    $stmt = $db->prepare('delete from User where id = :id');
    $stmt->bindValue(':id', $uid);

    $stmt2 = $db->prepare('delete from Task where tuid = :id');
    $stmt2->bindValue(':id', $uid);

    if($stmt->execute()){
        if($stmt2->execute()){
            header('Location: /logout.php');
            exit();
        }
        
    }
?>