<?php
    require_once "./components/connect.php";
    require_once "./components/functions.php";

    $sql = "TRUNCATE TABLE reply";
    $pdo->query($sql);
    $sql = "TRUNCATE TABLE userlog";
    $pdo->query($sql);
    header("Location:index.php");
?>