<?php
session_start();

require_once 'library.php';
require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

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
var_dump($_GET['tweetId']);
//$comments = Comment::loadAllCommentsByTweetId($connectionToDB, $_GET['tweetId']);
//var_dump($comments);
Comment::printAllCommentsOfTweet($connectionToDB, $_GET['tweetId']);

?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
    </head>
    <body>
        <form method="post">
            <textarea rows="5" cols="50" maxlength="140" placeholder="Komentarz" name="textOfComment"></textarea>
            <button type="submit">Dodaj komentarz</button>
        </form>
    </body>
</html>