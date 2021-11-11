<?php
    require_once "connect.php";
    require_once "functions.php";

    $sql = "TRUNCATE TABLE reply";
    $pdo->query($sql);
    $sql = "TRUNCATE TABLE userlog";
    $pdo->query($sql);
    header("Location:index.php");
?>