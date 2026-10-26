<?php


    $db = new SQLite3('inakettle.db');

    $login = $_POST['name'];
    $password = $_POST['password'];
    $nickname = $_POST['nickname'];

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare('insert into User(login, nickname, passwd) values(:login, :nickname, :passwd)');
    $stmt->bindValue(':login', $login, SQLITE3_TEXT);
    $stmt->bindValue(':nickname', $nickname, SQLITE3_TEXT);
    $stmt->bindValue(':passwd', $hash, SQLITE3_TEXT);

    if($stmt->execute()){
        header("Location: /loginpage.html");
        exit();
    }
    else echo'registration error';
?>