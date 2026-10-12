<?php

    session_start();

    if(!isset($_SESSION['uid'])){
        header('Location: /loginpage.php');
        exit();
    }


    $db = new SQLite3('inakettle.db');
    $uid = $_SESSION['uid'];
    $name = $_POST['name'];

    $stmt = $db->prepare('update User set nickname = :name where id = :id');
    $stmt->bindValue(':id', $uid);
    $stmt->bindValue(':name', $name);

    if($stmt->execute()){
        header('Location: /index.php');
        exit();
    }
?>