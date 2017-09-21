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
    </head>
    <body>
        <form method="post">
            <textarea rows="5" cols="50" maxlength="140" placeholder="What's going on? " name="textOfTweet"></textarea>
            <button type="submit">Wyślij Tweet</button>
        </form>
    </body>
</html>

<?php Tweet::printAllTweets($connectionToDB);