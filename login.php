<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    $db = new SQLite3('inakettle.db');

    $login = $_POST['name'];
    $password = $_POST['password'];

    $stmt = $db->prepare('select id, passwd, nickname from User where login = :login');
    $stmt->bindValue(':login', $login, SQLITE3_TEXT);

    $user = $stmt->execute();
    
    if($user){
        $usr = $user->fetchArray(SQLITE3_ASSOC);
        $passwd = $usr['passwd'];
        if(password_verify($password, $passwd)){
            session_start();
            $_SESSION['uid'] = $usr['id'];
            header("Location: /index.php");
            exit();
        }
        else echo'kiss my ass';
    }
    else echo 'kiss my ass';

?>