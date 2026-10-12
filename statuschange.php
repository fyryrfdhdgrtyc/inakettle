<?php

    session_start();

    if(!isset($_SESSION['uid'])){
        header('Location: /loginpage.php');
        exit();
    }


    $db = new SQLite3('inakettle.db');
    $uid = $_SESSION['uid'];

    $tid = $_POST['tid'];
    $query = $db->prepare('select status from Task where id = :tid');
    $query->bindValue(':tid', $tid);
    $checkstatus = $query->execute();
    $currstatus = $checkstatus->fetchArray(SQLITE3_ASSOC);

    if($currstatus['status'] == 'todo'){
        $status = 'done';
    }
    else if($currstatus['status'] == 'done'){
        $status = 'todo';
    }


    $stmt = $db->prepare('update Task set status = :status where id = :tid');
    $stmt->bindValue(':tid', $tid);
    $stmt->bindValue(':status', $status);

    if($stmt->execute()){
        header('Location: /index.php');
        exit();
    }
?>