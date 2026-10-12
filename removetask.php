<?php

    session_start();

    if(!isset($_SESSION['uid'])){
        header('Location: /loginpage.php');
        exit();
    }


    $db = new SQLite3('inakettle.db');
    $uid = $_SESSION['uid'];

    $tid = $_POST['tid'];

    $stmt = $db->prepare('delete from Task where id = :tid');
    $stmt->bindValue(':tid', $tid);


    if($stmt->execute()){
        header('Location: /index.php');
        exit();
    }
?>