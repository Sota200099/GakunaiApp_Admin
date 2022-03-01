<?php
    require_once "./components/connect.php";
    require_once "./components/functions.php";

    $sql = "TRUNCATE TABLE reply";//返信テーブルのリセット
    $pdo->query($sql);
    $sql = "TRUNCATE TABLE userlog";//投稿テーブルのリセット
    $pdo->query($sql);
    header("Location:index.php");
?>