<?php

    session_start();

    if(!isset($_SESSION['uid'])){
        header('Location: /loginpage.php');
        exit();
    }


    $db = new SQLite3('inakettle.db');
    $pid = $_POST['pid'];

    $stmt2 = $db->prepare('delete from Task where pid = :id');
    $stmt2->bindValue(':id', $pid);
    
    $stmt = $db->prepare('delete from Project where id = :pid');
    $stmt->bindValue(':pid', $pid);

    


    if($stmt2->execute()){
        if($stmt->execute()){
            $_SESSION['pid'] = 0;
            header('Location: /index.php');
            exit();
        }
    }    
?>