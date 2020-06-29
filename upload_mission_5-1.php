<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <form method="POST" action="">
    <table>
        <tr><td>お名前:</td>
        <td><input type="text" name="name" size="50"></td><tr>
        <tr><td>コメント:</td>
        <td><input type="text" name="comment" size="100"></td><tr>
        <tr><td>パスワード:</td>
        <td><input type="password" name="password"></td><tr>
	    <tr><td><input type="submit" name="submit1" value="送信"></td><tr></table>

        <tr><td><input type="submit" name="display" value="表示"></td><tr></table><br><br>

        <!削除番号指定用フォームを作る!>
        削除対象番号:<input type="number" name="delete"><br>
        パスワード:<input type="password" name="del_pass"><br>
        <input type="submit" name="submit2" value="削除"><br><br>
        

        <!編集番号指定用フォームを作る!>
        編集対象番号:<input type="number" name="edit_num" value="<?=isset($_POST['edit_num'])?$_POST['edit_num']:null?>"><br>
        パスワード:<input type="password" name="edit_pass"><br>
        <input type="submit" name="submit3" value="編集"> 
    </form>
<?php
    //データベースへの接続//
    //echo "データベース接続開始<br>";
    $dsn='mysql:dbname=*****;host=localhost';
    $user='*****';
    $password='******';
    $option=array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING);
    $pdo=new PDO($dsn,$user,$password,$option);
    //echo "データベース接続終了<br>";

    //テーブル作成//
    //echo "テーブル作成開始<br>";
    $sql="CREATE TABLE IF NOT EXISTS tbtest"
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name VARCHAR(32),"
    ."comment TEXT,"
    ."date TEXT,"
    ."password TEXT"
    .");";
    $contents=$pdo->query($sql);
    //echo "テーブル作成終了<br>";

    //登録か削除か?//
    if(isset($_POST["submit1"]) && !isset($_POST["edit_id"])){
        if($_POST["name"]<>null && $_POST["comment"]<>null && $_POST["password"]<>null){
            $name=$_POST["name"];
            $comment=$_POST["comment"];
            $date=date("Y/m/d H:i:s");
            $password=$_POST["password"];
            
             //データベースへの登録//
            $sql=$pdo->prepare("INSERT INTO tbtest (name,comment,date,password) VALUES(:name,:comment,:date,:password)");
            $sql-> bindParam(':name',$name,PDO::PARAM_STR);
            $sql-> bindParam(':comment',$comment,PDO::PARAM_STR);
            $sql-> bindParam(':date',$date,PDO::PARAM_STR);
            $sql-> bindParam(':password',$password,PDO::PARAM_STR);
            $sql->execute();



        }else{
            if($_POST["name"]=null or $_POST["comment"]=null or $_POST["password"]=null){
                echo "必要事項を記入してください";
            }
        }

    }
     //削除の場合//
    if(isset($_POST["submit2"])){
        if($_POST["delete"]<>null && $_POST["del_pass"]<>null){
            //送信された値を読み込む//
            $id=$_POST["delete"];
            $del_password=$_POST["del_pass"];
            echo $id."<br>".$del_password."<br>";
            //データベースから取り出す//
            $sql="SELECT*FROM tbtest WHERE id=$id";
            $contents=$pdo->query($sql);
            foreach($contents as $content){
            }
            echo $del_password."<br>".$content['password']."<br>";
            if($del_password==$content['password']){
                //削除をする処理をする//
                $sql='delete from tbtest where id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':id',$id,PDO::PARAM_INT);
                $stmt->execute();
            }else{
                echo "パスワードが違います。<br>";
            }
        }else{
            echo "必要事項を入力してください";
        }
        
    }

   //編集の場合// 
    if(isset($_POST["submit3"])){
        if($_POST["edit_num"]<>null && $_POST["edit_pass"]<>null){

            $id=$_POST["edit_num"];
            $edit_password=$_POST["edit_pass"];

        //送信された値を読み込む//
            $sql="SELECT*FROM tbtest WHERE id=$id";
            $contents=$pdo->query($sql);
            foreach($contents as $content){
            }
            if($edit_password==$content['password']){
                //編集をする処理をする//
                //$sql='select from tbtest where id=:id';
                //$stmt=$pdo->prepare($sql);
                //$stmt->bindParam(':id',$id,PDO::PARAM_INT);
                //$stmt->execute();
            }else{
                echo "パスワードが違います。<br>";
                exit;
            } 

    
            if($content['id']==$id && $content['password']==$edit_password){
                //編集の処理をする//

?>
                        <form method="POST" action="">
                            <input type="hidden" name="edit_id" value="<?=$content['id']?>">
                            <table>
                            <tr><td>お名前：</td>
                            <td><input type="text" name="name" size="50" value="<?=$content['name']?>"></td><tr>
                            <tr><td>コメント：</td>
                            <td><input type="text" name="comment" size="100" value="<?=$content['comment']?>"></td><tr>
                            <tr><td>パスワード:</td>
                            <td><input type="password" name="password" value="<?=$content['password']?>"></td><tr>
                            <tr><td><input type="submit" name="submit1" value="送信"></td><tr></table>
                        </form>
<?php
            }
        }else{
            echo "必要事項を入力してください";
        }
    }
    //編集登録処理//
    if(isset($_POST["submit1"])&&isset($_POST["edit_id"])){
        $id=$_POST["edit_id"];
        $name=$_POST["name"];
        $comment=$_POST["comment"];
        $date=date("Y/m/d H:i:s");
        $password=$_POST["password"];
        
        $sql='update tbtest set name=:name,comment=:comment,date=:date,password=:password where id=:id';
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':id',$id,PDO::PARAM_INT);
        $stmt->bindParam(':name',$name,PDO::PARAM_STR);
        $stmt->bindParam(':comment',$comment,PDO::PARAM_STR);
        $stmt->bindParam(':date',$date,PDO::PARAM_STR);
        $stmt->bindParam(':password',$password,PDO::PARAM_STR);
        $stmt->execute();
    }
    //データ表示//
    if(isset($_POST["display"])){
        $sql='SELECT*FROM tbtest';
        $contents=$pdo->query($sql);
        $results=$contents->fetchAll();
        foreach($results as $row){
            echo $row['id'].' ,';
            echo $row['name'].' ,';
            echo $row['comment'].' ,';
            echo $row['date'].'<br>';
            echo"<hr>";

        }
    }
?>
</body>
</html>