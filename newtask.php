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


    $stmt = $db->prepare('insert into Task(name, description, tuid, priority) values(:name, :desc, :tuid, :priority)');
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':desc', $desc, SQLITE3_TEXT);
    $stmt->bindValue(':tuid', $uid, SQLITE3_INTEGER);
    $stmt->bindValue(':priority', $priority, SQLITE3_INTEGER);

    if($stmt->execute()){
        header('Location: /index.php');
        exit();
    }
?>