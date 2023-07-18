<?php

date_default_timezone_set("Asia/Tokyo");
$comment_array = array();
$error_messages = array();

//エスケープ処理
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

//DB接続
try {

    $pdo = new PDO('mysql:host=localhost;dbname=bbs',"root","");

} catch (PDOException $e) {
    echo $e->getMessage();
}

//フォームを打ち込んだとき
if (!empty($_POST["submitButton"])){


    //名前のチェック
    if(empty($_POST["username"])){
        echo "名前を入力してください";
        $error_messages["username"] = "名前を入力してください"; 
    }
    //コメントのチェック
    if(empty($_POST["comment"])){
        echo "コメントを入力してください";
        $error_messages["comment"]= "コメントを入力してください";
    }

    $postDate = date("Y-m-d H:i:s");

    if(empty($error_messages)){
        try {
            //escape処理
            $username_e = h( $_POST['username']);
            $comment_e = h($_POST['comment']);


            $stmt = $pdo->prepare("INSERT INTO `bbstable`(`username`, `comment`, `postDate`) VALUES (:username,:comment,:postDate)");
            $stmt->bindParam(':username',$username_e, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment_e, PDO::PARAM_STR);
            $stmt->bindParam(':postDate', $postDate, PDO::PARAM_STR);
        
            $stmt->execute();
        
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }


}


//DBからデータを取得
$sql = "SELECT `id`, `username`, `comment`, `postDate` FROM `bbstable` ;";
$comment_array = $pdo->query($sql);

//DBの接続を閉じる

$pdo = null;

?>




<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャンネル掲示板</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    
</body>
<h1 class="title">PHPで掲示板アプリ</h1>
    <hr>
    <div class="boardWrapper">

       
        <section>
            <?php foreach($comment_array as $comment): ?>
            <article>
                <div class="wrapper">
                    <div class="nameArea">
                        <span>名前：</span>
                        <p class="username"><?php echo $comment["username"];?></p>
                        <time>:<?php echo $comment["postDate"];?><time>
                    </div>
                    <p class="comment"><?php echo $comment["comment"];?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </section>
        <form method="POST" action="" class="formWrapper">
            <div>
                <input type="submit" value="書き込む" name="submitButton">
                <label for="usernameLabel">名前：</label>
                <input type="text" name="username">
            </div>
            <div>
                <textarea name="comment" class="commentTextArea"></textarea>
            </div>
        </form>
    </div>
</html>