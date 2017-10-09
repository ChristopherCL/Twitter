<?php
session_start();

require_once 'library.php';

if(!isset($_SESSION['loggedUserId'])) {
    header('Location: login.php');
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_SESSION['loggedUserId']) && isset($_POST['textOfComment'])) {
        $newComment = new Comment();
        $newComment->setUserId($_SESSION['loggedUserId']);
        $newComment->setPostId($_GET['tweetId']);
        $newComment->setTextOfComment($_POST['textOfComment']);
        $newComment->setCommentCreationDate(date('Y-m-d H:i:s'));
        if($newComment->saveToDataBase($connectionToDB)) {
            echo '<div class="Welcome">dodano nowy komentarz</div>';
        }
        else {
            echo '<div class="Welcome">błąd dodawnia komentarza</div>';
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
                <div class="Welcome">Komentarze</div>
                <form method="post">
                    <textarea rows="5" cols="50" maxlength="140" placeholder="Komentarz" name="textOfComment"></textarea>
                    </br>
                    <input type="submit" value="Dodaj komentarz">
                </form>
            </div>
            <a href="tweets.php"><div class="link" style="background-color: #304ac4">Tweety</div></a>
            <a href="index.php"><div class="link" style="background-color: #304ac4">Strona Główna</div></a>
            </br>
        </div>
        <div id="comments">
            <?php Comment::printAllCommentsOfTweet($connectionToDB, $_GET['tweetId']);?>
        </div>
    </body>
</html>