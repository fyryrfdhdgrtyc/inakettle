<?php
    session_start();

    if(!isset($_SESSION['uid'])){
        header('Location: /loginpage.php');
        exit();
    }


    $db = new SQLite3('inakettle.db');
    $uid = $_SESSION['uid'];
    
    $name = $_POST['name'];  
    $desc = $_POST['desc'];
    $priority = $_POST['priority'];
    $dl = $_POST['deadline'];
    


    $stmt = $db->prepare('insert into Task(name, description, tuid, priority, deadline, status) values(:name, :desc, :tuid, :priority, :deadline, :status)');
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':desc', $desc, SQLITE3_TEXT);
    $stmt->bindValue(':tuid', $uid, SQLITE3_INTEGER);
    $stmt->bindValue(':priority', $priority, SQLITE3_INTEGER);
    $stmt->bindValue(':deadline', $dl, SQLITE3_TEXT);
    $stmt->bindValue(':status', 1, SQLITE3_INTEGER);

    if($stmt->execute()){
        header('Location: /index.php');
        exit();
    }
?>