<?php
    function get_posts($pdo,$offset){
        $sql = "SELECT * FROM userlog ORDER BY id ASC LIMIT 10 OFFSET {$offset}";
        $stmt=$pdo->query($sql);
        return $stmt;
    }
?>