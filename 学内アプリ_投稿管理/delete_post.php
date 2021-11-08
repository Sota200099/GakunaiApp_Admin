<?php
    require_once "connect.php";
    if($_SERVER["REQUEST_METHOD"] != "POST"){
        header("Location:error.php");
    }
    //投稿の削除
    $delete_post =$_POST["post_title"];
    echo $delete_post;
    if(isset($_POST["post_title"])){

        $sql ="DELETE FROM userlog WHERE :title = title";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":title" => $delete_post]);
        //返信の削除
        $sql ="DELETE FROM reply WHERE :title = title";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":title" => $delete_post]);
        header("location:data_management.php");
    }


?>