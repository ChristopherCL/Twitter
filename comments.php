<?php
session_start();

require_once 'library.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_SESSION['loggedUserId']) && isset($_POST['textOfComment'])) {
        var_dump($_SESSION['loggedUserId']);
        var_dump($_POST['textOfComment']);
        $newComment = new Comment();
        $newComment->setUserId($_SESSION['loggedUserId']);
        $newComment->setPostId($_GET['tweetId']);
        $newComment->setTextOfComment($_POST['textOfComment']);
        $newComment->setCommentCreationDate(date('Y-m-d H:i:s'));
        if($newComment->saveToDataBase($connectionToDB)) {
            echo "dodano nowy komentarz";
        }
        else {
            echo "błąd dodawnia komentarza";
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
            <link rel="stylesheet" href="CSS/style.css" type="text/css"/>

    </head>
    <body>
        <div class ="container">
            <div class="textInput">
                <form method="post">
                    <textarea rows="5" cols="50" maxlength="140" placeholder="Komentarz" name="textOfComment"></textarea>
                    </br>
                    <input type="submit" value="Dodaj komentarz">
                </form>
            </div>
        </div>
        <div id="comments">
            <?php Comment::printAllCommentsOfTweet($connectionToDB, $_GET['tweetId']);?>
        </div>
    </body>
</html>