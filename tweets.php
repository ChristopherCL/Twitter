<?php
session_start();

require_once 'library.php';
//require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

if(!isset($_SESSION['loggedUserId'])) {
    header('Location: login.php');
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_SESSION['loggedUserId']) && isset($_POST['textOfTweet'])) {

        $newTweet = new Tweet();
        $newTweet->setUserId($_SESSION['loggedUserId']);
        $newTweet->setTextOfTweet($_POST['textOfTweet']);
        $newTweet->setTweetCreationDate(date('Y-m-d H:i:s'));
        
        if($newTweet->saveToDataBase($connectionToDB)) {
            echo "dodano nowego tweeta";
        }
        else {
            echo "błąd dodawnia tweeta";
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
        <div class="container">
            <div class="textInput">
                <div class="Welcome">Tweety</div>
                <form method="post">
                    <textarea rows="3" cols="150" maxlength="140" placeholder="Wpisz tekst Tweeta..." name="textOfTweet"></textarea>
                    </br>
                    <input type="submit" value="Wyślij Tweet">
                    <a href="index.php"><div class="link" style="background-color: #304ac4">Strona Główna</div></a>
                    </br>
                    </br>
                </form>
            </div>
            <div id="Tweets">
                <?php Tweet::printAllTweets($connectionToDB);?>
            </div>
            
        </div>
    </body>
</html>
