<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission5-1</title>
</head>
<body>
<br>
＊同時に新規投稿・削除・編集をすることはできません。<br>
　それぞれ分けて実行をお願いします。<br>
＊パスワードなしでも投稿できますが、編集・削除はできません。<br>
<br>
<br>
<?php
//データベース接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
//投稿機能
    //名前もコメントも空でなければ以下の処理をする
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["ednumber"]) && empty($_POST["delete"])
    && empty($_POST["delpass"]) && empty($_POST["edit"]) && empty($_POST["edpass"])){
        //変数に代入
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $postpass=$_POST["postpass"];
        //日時取得
        $date=date("Y/m/d H:i:s");
        //データ入力
        $sql = $pdo -> prepare("INSERT INTO boardDB (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':password', $postpass, PDO::PARAM_STR);
        $sql -> execute();
        
    }
        
//削除機能
    if(!empty($_POST["delete"]) && !empty($_POST["delpass"]) && empty($_POST["name"]) && empty($_POST["comment"])
     && empty($_POST["edit"]) && empty($_POST["edpass"]) && empty($_POST["postpass"])){
        $delpass=$_POST["delpass"];
        $delnum=$_POST["delete"];
        $id=$delnum;
        //パスワード取得
        $sql = 'SELECT password FROM boardDB WHERE id=:id';
        $stmt = $pdo->prepare($sql);                  //差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); //その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             //SQLを実行する。
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){
            $DBdelpass=$row['password'];
        }
        
        //パスワードがあっていれば
        if(@$DBdelpass == $delpass){
            //入力したデータレコードを削除
            $sql = 'delete from boardDB where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $delsuc="を削除しました。";
        }
     }
    
    
//編集機能
//編集番号指定機能,データ表示機能
    if(!empty($_POST["edit"]) && !empty($_POST["edpass"]) && empty($_POST["name"]) && empty($_POST["comment"]) && 
    empty($_POST["postpass"]) && empty($_POST["delete"]) && empty($_POST["delpass"])){
        $edit=$_POST["edit"];
        $edpass=$_POST["edpass"];
        $id=$edit;
        //パスワード取得
        $sql = 'SELECT password FROM boardDB WHERE id=:id';
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            $DBedpass=$row['password'];
        }
        
        if(@$DBedpass == $edpass){
            //データレコード編集
            //入力したデータレコードを抽出、変数に代入
            $sql = 'SELECT * FROM boardDB WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(); 
            foreach ($results as $row){
                $ednum=$row['id'];
                $edname=$row['name'];
                $edcomment=$row['comment'];
                $edpassword=$row['password'];
                $ednumsuc="編集番号を送信しました。";
            }
        }
        
    }
    
    if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["ednumber"]) && empty($_POST["delete"]) && 
    empty($_POST["delpass"]) && empty($_POST["edit"]) && empty($_POST["edpass"])){
        $ednumber=$_POST["ednumber"];
        //入力されているデータレコードの内容を編集
        $id = $ednumber;
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $sql = 'UPDATE boardDB SET name=:name,comment=:comment WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $edsuc="を編集しました。";   
    }
    
?>

<br>
＜新規投稿・投稿編集（編集番号送信後）フォーム＞<br>
<form method="post" action="">
名前：<input type="text" name="name" placeholder="名前" value="<?php echo @$edname; ?>"><br>
コメント：<input type="text" name="comment" placeholder="コメント" value="<?php echo @$edcomment;?>"><br>
パスワード：<input type="text" name="postpass" placeholder="パスワード" value="<?php echo @$edpassword; ?>"><br>
<input type="hidden" name="ednumber" value="<?php echo @$ednum;?>">
<input type="submit" name="submit" value="投稿"><br>
<br>
＜削除フォーム＞<br>
削除番号：<input type="number" name="delete" placeholder="削除番号"><br>
パスワード：<input type="text" name="delpass" placeholder="パスワード"><br>
<input type="submit" name="submit" value="削除"><br>
<br>
＜編集番号送信フォーム＞<br>
編集番号<input type="number" name="edit" placeholder="編集番号"><br>
パスワード：<input type="text" name="edpass" placeholder="パスワード"><br>
<input type="submit" name="submit" value="送信"><br>
<br>

<?php
    if(!empty($ednumsuc)){
        echo $ednumsuc;
    }
    
    if(!empty($edsuc)){
        echo $ednumber.$edsuc;
    }
    
    if(!empty($delsuc)){
        echo $delnum.$delsuc;
    }
    
?>
    

<br>
<br>
</form>
    
<?php
    //入力したデータレコードを抽出し、表示
    $sql = 'SELECT * FROM boardDB';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['comment'].' ';
        echo $row['date'].'<br>';
        echo "<hr>";
    }
    
?>
</body>
</html>
