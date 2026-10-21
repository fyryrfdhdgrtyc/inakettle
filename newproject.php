<?php
    session_start();

    if(!isset($_SESSION['uid'])){
        header('Location: /loginpage.php');
        exit();
    }


    $db = new SQLite3('inakettle.db');
    $uid = $_SESSION['uid'];
    $name = $_POST['name'];

    $stmt = $db->prepare('insert into Project(name, uid) values (:name, :uid)');
    $stmt->bindValue(':uid', $uid);
    $stmt->bindValue('name', $name);


    if($stmt->execute()){
        header('Location: /index.php');
        exit();
    }
?>