<?php

//クラス名をすべて取得
function Get_class_Id($pdo)
{
    try{
        $sql = "SELECT class_id FROM classes ORDER BY class_id ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $class_id = $stmt->fetchall(PDO::FETCH_ASSOC);
        return $class_id;
    }catch (PDOException $e) {
        header('Location: error_page.php');
        exit;
    }
}

//サニタイズ(XSS対策)
function check($posts)
{
    foreach ($posts as $column => $post) {
        $posts[$column] = htmlspecialchars($post, ENT_QUOTES, 'UTF-8');
    }
    return $posts;
}

function unique($pdo, $email)
{
    $sql = "SELECT * FROM students WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //メールアドレスが重複している場合trueを返す
    if ($user) {
        return $user;
    }
}

//生徒の情報をデータベースに登録
function Sign_Up($pdo,$posts)
{
    //カタカナ変換
    $posts["furigana"] = mb_convert_kana($posts["furigana"], 'KVC');

    //パスワードをハッシュ化
    $posts["password"] = password_hash($posts['password'], PASSWORD_DEFAULT);
    
    try{
        $sql = "INSERT INTO students VALUES(student_id,?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($posts["name"],$posts["furigana"],$posts["gender"],$posts["email"],$posts["password"],$posts["class"]));
        header('Location: completion.php');
    }
    catch (PDOException $e) {
        header('Location: error_page.php');
        exit;
    }
}

//DB接続
require_once('Components/connect.php');

//pdo変数
$pdo = Attendance();

//エラーメッセージ用の変数
$message = "";

//クラス名をすべて取得
$class_id = Get_class_Id($pdo);


//postデータを受け取る
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    //サニタイズ、入力値確認
    $posts = check($_POST);
    //email重複チェック
    $user = unique($pdo, $posts['email']);
    //emailが重複していた場合
    if ($user) {
        $message = "入力したメールアドレスは既に存在しています";
    }else{
        //パスワードが再入力したものと一致している場合
        if(strcmp($posts['password'],$posts['password_check']) == 0){
            //生徒の情報をデータベースに登録
            Sign_Up($pdo,$posts);
        }else{
            $message = "パスワードが異なります";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <title>ユーザー登録</title>
</head>
<body>
    <main class="d-flex justify-content-center">
        <div class="w-50 mt-3 p-5 bg-light">
            <form action="" method="post">
                <h3 class="h3 mb-3 fw-normal">ユーザー登録</h3>
                <div class="error_message">
                    <?= $message ?>
                </div> 
                <div>
                    <label for="name">氏名（空白を開けずに入力）</label>
                    <input type="text" name="name" class="form-control" id="name" maxlength="60" pattern="^[^ 　]+$" required>
                </div>
                <div class="mt-2">
                    <label for="furigana">フリガナ（空白を開けずに入力）</label>
                    <input type="text" name="furigana" class="form-control" id="furigana" maxlength="60" pattern="^[\u30A1-\u30FC\uFF66-\uFF9F\u3041-\u3096]+$" title="カタカナ、またはひらがなで入力してください" required>
                </div>
                <div class="mt-4">
                    <label for="none">性別</label>
                    <div>
                        <input type="radio" name="gender" value="男" id="male">
                        <label for="male">男</label>
                    </div>
                    <div>
                        <input type="radio" name="gender" value="女" id="female">
                        <label for="female">女</label>
                    </div>
                    <div>
                        <input type="radio" name="gender" value="未設定" id="none" checked>
                        <label for="none">設定しない</label>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" id="email" minlength="6" maxlength="254" pattern="[a-zA-Z0-9._%&+-]+@[a-z0-9.-]+\.[a-z]{2,}$" required>
                </div>
                <div class="mt-2">
                    <label for="password">パスワード</label>
                    <input type="password" name="password" class="form-control" id="password" maxlength="255" pattern="^[a-zA-Z0-9!-/:-@¥[-`{-~]+$" title="空白を開けずに半角英数字、または記号で入力してください" required>
                </div>
                <div class="mt-2">
                    <label for="password_check">パスワード（再入力）</label>
                    <input type="password" name="password_check" class="form-control" id="password_check"　maxlength="255" pattern="^[a-zA-Z0-9!-/:-@¥[-`{-~]+$" title="空白を開けずに半角英数字、または記号で入力してください" required>
                </div>
                <div class="mt-2" name="class" id="class">
                    <label>学科・クラス</label>        
                    <select name="class" class="form-control" required>
                        <option value="" hidden>選択してください</option>
                        <?php for($i = 0; $i < count($class_id); $i++){
                        echo "<option value='{$class_id[$i]["class_id"]}'>{$class_id[$i]["class_id"]}</option>";
                        }?>
                    </select>
                </div>
                <div class="mt-4">
                    <button class="w-100 btn btn-lg btn-primary">登録</button>
                </div>       
            </form>
        </div>
    </main>    
</body>
</html>