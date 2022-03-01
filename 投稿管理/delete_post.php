<?php
    require_once "./components/connect.php";
    session_start();
    if($_SERVER["REQUEST_METHOD"] != "POST"){
        header("Location:error.php");
    }
    //投稿の削除
    $delete_post =explode(",",$_POST["post_title"]);

    if(isset($_POST["post_title"])){

        $sql ="update userlog SET delete_flag=1 
        WHERE :title = title AND :name=name AND :date=date";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":title" => $delete_post[0], ":name" => $delete_post[1], ":date" => $delete_post[2]]);
        //返信の削除
        $sql ="update reply SET delete_flag=1
        WHERE :title = title AND :name=name AND :distinationDate = distinationDate";//対象のデータのdelete_flagをtrueに変更
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":title" => $delete_post[0], ":name" => $delete_post[1], ":distinationDate" => $delete_post[2]]);


        
        $_SESSION["delete"]=true;//削除フラグをtrue
        header("location:data_management.php?page_num={$_SESSION["current_page"]}");
    }


?>