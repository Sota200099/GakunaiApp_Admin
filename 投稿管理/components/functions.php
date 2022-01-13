<?php
    function get_posts($pdo,$offset=0){
        $sql = "SELECT * FROM userlog WHERE delete_flag=0 ORDER BY id DESC LIMIT 10 OFFSET {$offset}";
        $stmt=$pdo->query($sql);
        return $stmt;
    }
    function max_id($pdo){
        $sql = "SELECT max(id) AS id FROM userlog";
        $stmt=$pdo->query($sql);
        $max_id =$stmt->fetch();
        return $max_id["id"]-10;//最大値から表示件数の10を引く
    }
    function get_rows_cnt($pdo){
        $sql ="SELECT count(*) FROM userlog";
        $stmt = $pdo->query($sql);
        return $stmt->fetch();
    }
    function get_rows_cnt_notdlt($pdo){
        $sql ="SELECT count(*) FROM userlog where delete_flag=0";
        $stmt = $pdo->query($sql);
        return $stmt->fetch();
    }

?>