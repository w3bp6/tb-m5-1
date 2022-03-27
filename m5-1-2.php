<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>mission5-1</title>
  </head>
  <body>
    <p>この掲示板のテーマ：おすすめのもの（食べ物、本、映画など、勧めたいものならなんでも！）</p>
    <?php
        //データベースに接続
        $dsn='データーベース名';
        $user='ユーザー名';
        $password='パスワード';
        $pdo=new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //4-2 データベース内にテーブルを作成
        $sql="CREATE TABLE IF NOT EXISTS mission5_1"
        ."("
        ."id INT AUTO_INCREMENT PRIMARY KEY,"
        ."name char(32),"
        ."comment TEXT,"
        ."password TEXT,"
        ."datetime TEXT"
        .");";
        $stmt=$pdo->query($sql);
        
        /*//4-3 DBのテーブル一覧を表示
        $sql='SHOW TABLES';
        $result=$pdo->query($sql);
        foreach($result as $row){
            echo $row[0];
            echo '<br>';
        }
        echo "<hr>";
        
        //4-4 作成したテーブルの構成詳細を確認
        $sql='SHOW CREATE TABLE mission5_1';
        $result=$pdo->query($sql);
        foreach($result as $row){
            echo $row[1];
        }
        echo "<hr>";*/
        
        //新規投稿
        if(isset($_POST["submit1"])){
            //名前とコメントとパスワードが入力されている時
            if(!empty(($_POST["name"])&&($_POST["comment"])&&($_POST["password"]))){
                //POSTデータ受け取り
                $name=$_POST["name"];   //入力送信された名前を代入
                $comment=$_POST["comment"];  //入力送信されたコメントを代入
                $pass=$_POST["password"];   //入力送信されたパスワードを代入
                $date=date("Y/m/d H:i:s");  //日付
                
                //入力したデータレコードを抽出
                $stmt=$pdo->prepare('SELECT*FROM mission5_1');
                $stmt->execute();
                
                /*$num=0;
                foreach($stmt as $parts){
                    if($parts['id']>0){
                        $num=$parts['id'];
                        break;
                    }
                }
                $num++;*/
                
                //データベース内のテーブルを読み込み、POSTで受け取った内容を書き込む
                $sql=$pdo->prepare("INSERT INTO mission5_1 (id, name, comment, password, datetime) VALUES (:id, :name, :comment, :password, :datetime)");
                $sql->bindParam(':id', $num, PDO::PARAM_STR);
                $sql->bindParam(':name', $name, PDO::PARAM_STR);
                $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql->bindParam(':password', $pass, PDO::PARAM_STR);
                $sql->bindParam(':datetime', $date, PDO::PARAM_STR);
                $sql->execute();
                
                echo "Accepted.<br>";
            }
            else{
                echo "Either name or comment or password is empty.<br>";
            }
        }
        //編集ボタンを押した時
        elseif(!empty($_POST["editNum"])){
            //パスワードが入力されているとき
            if(!empty(($_POST["password"])&&($_POST["edit"]))){
                $edit=$_POST["edit"];   //編集対象番号を入力
                $edpass=$_POST["password"];     //入力送信されたパスワードを代入
                
                //入力したデータレコードを抽出
                $stmt=$pdo->prepare('SELECT*FROM mission5_1');
                $stmt->execute();
                
                foreach($stmt as $parts){
                    //投稿番号が編集対象番号と一致する時 && 入力されたパスワードが一致する時
                    if(($edit==$parts['id'])&&($edpass==$parts['password'])){
                        echo "Password is correct.<br>";
                        $edname=$parts['name'];
                        $edtxt=$parts['comment'];
                        $edpass=$parts['password'];
                        break;
                    }
                    //パスワードが一致しないとき
                    elseif($edit==$parts['id'] && $edpass==$parts['password']){
                        echo "Password is not correct.<br>";
                        break;
                    }
                }
            }
            else{
                echo "Either number or password is empty.<br>";
            }
            //echo $edname." ".$edtxt."<br>";
        }
        
        //編集
        elseif(isset($_POST["submit2"])){
            //編集対象番号とパスワードが入力されている時
            if(!empty(($_POST["edit2"])&&($_POST["edpass"]))){
                $edit=$_POST["edit2"];       //編集対象番号を代入
                $pass=$_POST["edpass"];   //入力送信されたパスワードを代入
                
                $stmt=$pdo->prepare('SELECT*FROM mission5_1');
                $stmt->execute();
                
                foreach($stmt as $parts){
                    //編集対象番号と一致した時
                    if($edit==$parts['id']){
                        //入力された値にUPDATE
                        $sql='UPDATE mission5_1 SET name=:name, comment=:comment, password=:password, datetime=:datetime WHERE id=:id';
                        $stmt2=$pdo->prepare($sql);
                        $stmt2->bindParam(':name', $name2, PDO::PARAM_STR);
                        $stmt2->bindParam(':comment', $txt, PDO::PARAM_STR);
                        $stmt2->bindParam(':password', $pass, PDO::PARAM_STR);
                        $stmt2->bindParam(':datetime', $date, PDO::PARAM_STR);
                        $stmt2->bindParam(':id', $edit, PDO::PARAM_STR);
                        
                        $edit=$_POST["edit2"];       //編集対象番号を代入
                        $name2=$_POST["edname"];        //編集後の名前を代入
                        $txt=$_POST["edcomment"];        //編集後のコメントを代入
                        $pass=$_POST["edpass"];   //入力送信されたパスワードを代入
                        $date=date("Y/m/d H:i:s");  //日付
                        
                        $stmt2->execute();
                        echo "Accepted.<br>";
                        break;
                    }
                }
            }
            else{
                echo "Either number or password is empty.<br>";
            }
        }
        
        //削除ボタンを押した時
        elseif(!empty($_POST["delete"])){
            //削除対象番号とパスワードが入力されている時
            if(!empty(($_POST["number"])&&($_POST["password"]))){
                $number=$_POST["number"];
                $pass=$_POST["password"];
                
                $stmt=$pdo->prepare('SELECT*FROM mission5_1');
                $stmt->execute();
                
                foreach($stmt as $parts){
                    //番号とパスワードが一致する時
                    if(($number==$parts['id'])&&($pass==$parts['password'])){
                        $sql='DELETE FROM mission5_1 WHERE id=:number';
                        $stmt2=$pdo->prepare($sql);
                        $stmt2->bindValue(':number', $number, PDO::PARAM_INT);
                        $stmt2->execute();
                        echo "Accepted.<br>";
                        break;
                    }
                }
            }
            else{
                echo "Either number or password is empty.<br>";
            }
        }

    ?>

    <!--編集ボタンが押されてない or 編集番号とパスワードが空の時 or 投稿番号と編集番号は一致する時がパスワードが一致しない時-->
    <?php if(!isset($_POST["editNum"]) || empty(($_POST["edit"]) && ($_POST["password"])) || $parts['id']==$edit && $parts['pass']!=$edpass) : ?>
        <form action="" method="post">
            <p><input type="text" name="name" placeholder="名前"></p>
            <p><input type="text" name="comment" placeholder="コメント"></p>
            <p><input type="password" name="password" placeholder="パスワード"></p>
            <p><input type="submit" name="submit1" value="送信"></p>
        </form>
        <!--編集ボタンを押した時かつ編集番号が入力されているとき-->
        <?php elseif(isset($_POST["editNum"])/* && !empty(($_POST["edit"]))*/) : ?>
        <form action="" method="post">
            <p><input type="hidden" name="edit2" value="<?php echo $edit; ?>"></p>
            <p><input type="text" name="edname" value="<?php /*if(!empty($edname))*/ echo $edname; ?>"></p>
            <p><input type="text" name="edcomment" value="<?php /*if(!empty($edtxt))*/ echo $edtxt; ?>"></p>
            <p><input type="password" name="edpass" value="<?php /*if(!empty($edpass))*/ echo $edpass; ?>"></p>
            <p><input type="submit" name="submit2" value="送信"></p>
        </form>
        <?php endif; ?>
        <form action="" method="post">
            <p><input type="number" name="number" placeholder="削除対象番号"></p>
            <p><input type="password" name="password" placeholder="パスワード"></p>
            <p><input type="submit" name="delete" value="削除"></p>
        </form>
        <form acton="" method="post">
            <p><input type="number" name="edit" placeholder="編集対象番号"></p>
            <p><input type="password" name="password" placeholder="パスワード"></p>
            <p><input type="submit" name="editNum" value="編集"></p>
        </form>
         
        <?php 
            //4-6 入力したデータレコードを抽出し、表示する
            $sql='SELECT * FROM mission5_1';
            $stmt=$pdo->query($sql);
            $results=$stmt->fetchAll();
            echo "<hr>";
            foreach($results as $row){
                echo $row['id'].', ';
                echo $row['name'].', ';
                echo $row['comment'].', ';
                //echo $row['password'].', ';
                echo $row['datetime'].'<br>';
            }
            echo "<hr>";
        ?>
  </body>
</html>
