<?php
    require_once "connect.php";
    require_once "functions.php";
    session_start();
    $logs=[];
    $PAGE_MAX=10;
    $get_count = get_count($pdo);//削除済み投稿を除く
    $dtl_not_count = get_rows_cnt_notdlt($pdo);

    //$now_data_max =max_id($pdo)+10;//現在の最大idの取得
    //$page_numbers =ceil($now_data_max/10);


    //ページ番号が送られたとき、表示できるデータが無ければboard.phpにリダイレクト
    //現在のページの取得
    if(!isset($_GET["page_num"])){
        if($dtl_not_count[0]<=0){
            header("Location:board.php");
        }
        $current_page=1;
    }else{

        if(intval($_GET["page_num"])<=ceil($get_count[0]/10)){
            $current_page=$_GET["page_num"];
        }else{
            $current_page=ceil($get_count[0]/10);
        }
    }
    //不正アクセス制御

    //ページ数より多いか,1より少ないとき
    if(isset($_GET["page_num"])){
        if($_GET["page_num"]<1 || $_GET["page_num"]>ceil($get_count[0]/10)){
            header("Location:index.php");//トップページへリダイレクト
        }
    }

    function get_count($pdo){
        $sql = "SELECT count(*) as cnt FROM userlog WHERE delete_flag=0";//削除済みでない投稿を全て取得するsql
        $stmt=$pdo->query($sql);
        return $stmt->fetch();
    }
    $paging_id = (($current_page-1)*$PAGE_MAX);//開始indexの作成

    $db_cnt = get_rows_cnt($pdo);

    if($db_cnt[0]>0){
        //直近10件を降順で表示
        $stmt = get_posts($pdo,$paging_id);

        while($data =$stmt->fetch()){
            $logs[]=$data;
        }

        $page_numbers =ceil($db_cnt[0]/10);//最終ページの番号



    }


?>
<script src="js/functions.js"></script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Document</title>
</head>
<body class="bg-lsBlue">

    <script src="js/jquery-3.6.0.min.js"></script>   
    <h2 style="text-align:center;" class="mb-5"><a href="./index.php">投稿管理ページ</a></h2>
 
        <div class="container">
        <?php if($db_cnt[0]>0 && $dtl_not_count[0]>0):?>
            <h4 class="mb-4"><?=$current_page?>ページ目</h4>

            <div class="paging">
                <?php foreach($logs as $value):?>
                    <form action="delete_post.php" method="post">
                        <?php if($value["delete_flag"]==0):?>
                            <div class="mb-5 card">
                            <span class="card-body" style="width:70%;height:20%;font-size: 1.3em;">タイトル：<?=$value["title"]?>　投稿者：<?=$value["name"]?>　<?=$value["date"]?></span>
                            <p class="ml-4" style="font-size: 1.3em;">本文：　<?=$value["text"]?></p>
                            <button type="submit" class="<?=$value["title"]?> btn btn-outline-dark" value="<?=$value["title"]?>" onclick="return input_confirm()">投稿を削除</button>
                            </div>
                        <?php endif?>
                        <input type="hidden" name="post_title" value="<?=$value["title"]?>,<?=$value["name"]?>,<?=$value["date"]?>">
                    </form>
                <?php endforeach;?>
            </div>
            <?php endif?>
            <?php if($dtl_not_count[0]<=0):?>
                <p>現在投稿はありません</p>
            <?php endif?>
        </div>



    

    <?php if($db_cnt[0]>0 && $dtl_not_count[0]>0):?>
        <?php $previous = ($current_page-1);$next =($current_page+1);
            if($previous<=0){
                $previous =1;
            }
            if($next>$page_numbers){
                $next = $page_numbers;
            }
            
            
            $page_start=$current_page-2;//現在のページの2個前まで
            if($page_start<=1){//ページが無いとき
                $page_start=1;
            }

            $page_amount = $current_page+2;//現在のページから2つ後まで
            if($page_amount>=$page_numbers){//ページ個数が最大値を超えていたなら
                $page_amount=$page_numbers;
            }
            
            if($current_page==1){
                $page_amount+=2;
        
            }
            if($page_numbers==1){
                $page_amount=1;
            }
            if($page_numbers==2){
                $page_amount=2;
            }
            if($current_page>=3){
                if($current_page==$page_numbers){
                    $page_start-=2;
                }
                if($current_page==$page_numbers-1){
                    $page_start--;
                }
            }

            /*if($previous<=0){//現在のページが1ページ目のとき
                $previous=1;
            }

            if($next> $page_numbers){
                $next = $page_numbers;
            }

            if($next> $page_numbers && ($page_numbers==0)){
                $next = 1;
            }
            
            if($current_page<=$page_amount){
                $page_amount=$current_page;
            }
            if($current_page==1){
                $page_amount+=2;
            }
            if($current_page==2){
                $page_amount++;
            }*/

        ?>

    <div class="pagination container d-flex justify-content-center mb-5" style="margin-top: 4%;">
        <?php echo '<button class="btn btn-primary mr-5 page-item"><a href ="./data_management.php?page_num='.($previous).'" style="color:white;">'."前へ".'</a></button>'?>
        <div class="buttons" style="text-align: center;">
            <?php for($i=$page_start;$i<=$page_amount;$i++){

                echo '<button class="btn btn-primary mr-5 page-item"><a href ="./data_management.php?page_num='.$i.'" style="color:white;">'.$i.'</a></button>';
            }
            ?>
        </div>

        <?php echo '<button class="btn btn-primary mr-2 page-item"><a href ="./data_management.php?page_num='.($next).'" style="color:white;">'."次へ".'</a></button>'?>
    </div>

    <?php endif?>
</body>
</html>