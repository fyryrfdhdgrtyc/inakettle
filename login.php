<?php

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
            $_SESSION['pid'] = 0;
            header("Location: /index.php");
            exit();
        }
        else echo'login error';
    }
    else echo 'login error';

?>