<?php
session_start();

require_once 'library.php';
require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_SESSION['loggedUserId']) && isset($_POST['textOfTweet'])) {
        var_dump($_SESSION['loggedUserId']);
        var_dump($_POST['textOfTweet']);
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
//$userTweets = Tweet::printTweetsByUserId($connectionToDB, $_SESSION['loggedUserId']);


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
                <form method="post">
                    <textarea rows="5" cols="50" maxlength="140" placeholder="Wpisz tekst Tweeta..." name="textOfTweet"></textarea>
                    </br>
                    <input type="submit" value="Wyślij Tweet">
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
