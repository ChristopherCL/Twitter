<?php
require_once __DIR__.'/../library.php';
//require_once 'connectionToTwitterDataBase.php';
//require_once __DIR__.'/../Functions/connectionToTwitterDataBase.php';

//$connectionToDB = connectionToTwitterDataBase();
//require_once __DIR__.'/../library.php';
//require_once __DIR__.'/../Functions/connectionToTwitterDataBase.php';

/*
function connectionToTwitterDataBase() {

    try {
                
        $serverName = "localhost";
        $userName = "root";
        $password = "coderslab";
        $dataBaseName = "Twitter";
                
        return new PDO("mysql:host=$serverName;
                                   dbname=$dataBaseName;
                                   charset=utf8", $userName, $password,
                                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        echo "Połączenie udane";
         }
    catch (PDOException $exeption) {
              echo 'Błąd podczas łączenia z bazą danych: ' . $exeption->getMessage();
              die();
    }
    var_dump($connectionToDB);
}
*/
//$connectionToDB = connectionToTwitterDataBase();

class Tweet {
    private $id;
    private $userId;
    private $textOfTweet;
    private $tweetCreationDate;
    private $userName;
    private $numberOfComments;
    
    public function __construct(){
        $this->id = -1;
        $this->userId = 0;
        $this->textOfTweet = "";
        $this->tweetCreationDate = "";
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId(){
        return $this->id;
    }
    
    public function setUserId($userId){
        $this->userId = $userId;
    }
    
    public function getUserId(){
        return $this->userId;
    }
    
    public function setTextOfTweet($textOfTweet){
        $this->textOfTweet = $textOfTweet;
    }
    
    public function getTextOfTweet(){
        return $this->textOfTweet;
    }
    
    public function setTweetCreationDate($tweetCreationDate){
        $this->tweetCreationDate = $tweetCreationDate;
    }
    
    public function getTweetCreationDate(){
        return $this->tweetCreationDate;
    }
    
    static public function loadTweetById($connectionToDB, $id){
        $stmt = $connectionToDB->prepare("SELECT * FROM Tweets WHERE id=:id");
        $result = $stmt->execute(['id' => $id]);
        if($result === true && $stmt->rowCount() > 0) {
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $loadedTweet = new Tweet();
            $loadedTweet->id = $row['id'];
            $loadedTweet->userId = $row['userId'];
            $loadedTweet->textOfTweet = $row['text'];
            $loadedTweet->tweetCreationDate = $row['creationDate'];
            return $loadedTweet;
        }
        return null;
    }
    
    static public function loadTweetsByUserId($connectionToDB, $userId) {
        $allUserTweets = [];
        $stmt = $connectionToDB->prepare("SELECT * FROM Tweets WHERE userId=:userId ORDER BY tweetCreationDate DESC");
        $result = $stmt->execute(['userId' => $userId]);
       // var_dump($stmt->fetchAll(PDO::FETCH_ASSOC));
        if($result === true && $stmt->rowCount() != 0) {
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $loadedTweet = new Tweet();
                $loadedTweet->id = $row['id'];
                $loadedTweet->userId = $row['userId'];
                $loadedTweet->textOfTweet = $row['textOfTweet'];
                $loadedTweet->tweetCreationDate = $row['tweetCreationDate'];
                $allUserTweets[] = $loadedTweet;
            }
            return $allUserTweets;
        }
        return null;
    }
    
    static public function printTweetsByUserId($connectionToDB, $userId) {
        $userTweets = self::loadTweetsByUserId($connectionToDB, $userId);
        $html ='';
        foreach($userTweets as $tweet) {
            $html .= self::render('tweetsTemplate', [
                                                'tweetId' => $tweet->getId(),
                                                'userId' => $tweet->getId(),
                                                'tweetCreationDate' => $tweet->getTweetCreationDate(),
                                                'textOfTweet' => $tweet->getTextOfTweet()
                                              ]);
        }
        echo $html;
        /*foreach($userTweets as $tweet) {
            echo $tweet->getId().', '. $tweet->getUserId().', '. $tweet->getTextOfTweet().', '.$tweet->getTweetCreationDate().'</br>';
            echo '<a href=comments.php?tweetId='.$tweet->getId().'>Pokaż komentarze</a></br>';
        }*/
    }
    
    static public function render($template, $data) {
        $html = file_get_contents(__DIR__.'/../Templates/'.$template.'.html');
        foreach($data as $key=>$value) {
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }
        return $html;
    }
    
    /*public function loadAllTweets($connectionToDB) {
        $allTweets = [];
        $result = $connectionToDB->query("SELECT * FROM Tweets");
        
        if($result === true && $result->rowCount() != 0) {
            foreach($result as $row) {
                $loadedTweet = new Tweet();
                $loadedTweet->id = $row['id'];
                $loadedTweet->userId = $row['userId'];
                $loadedTweet->textOfTweet = $row['text'];
                $loadedTweet->tweetCreationDate = $row['creationDate'];
                $allTweets[] = $loadedTweet;
            }
        }
        return $allTweets;
    }*/
    
    static public function loadAllTweets($connectionToDB) {
        $allTweets = [];
        $result = $connectionToDB->query("SELECT Tweets.*, Users.userName FROM Tweets, Users WHERE Tweets.userId = Users.id ORDER BY tweetCreationDate DESC");
  
        if($result !== false && $result->rowCount() != 0) {
            foreach($result as $row) {
                $loadedTweet = new Tweet();
                $loadedTweet->id = $row['id'];
                $loadedTweet->userId = $row['userId'];
                $loadedTweet->textOfTweet = $row['textOfTweet'];
                $loadedTweet->tweetCreationDate = $row['tweetCreationDate'];
                $loadedTweet->userName = $row['userName'];
                $allTweets[] = $loadedTweet;
            }
        }
        return $allTweets;
        
    }
    
    static public function printAllTweets($connectionToDB) {
        if($userTweets = self::loadAllTweets($connectionToDB)) {
            $html ='';
            foreach($userTweets as $tweet) {
                $html .= self::render('tweetsTemplate', [
                                                    'tweetId' => $tweet->getId(),
                                                    'userId' => $tweet->userName,
                                                    'tweetCreationDate' => $tweet->getTweetCreationDate(),
                                                    'textOfTweet' => $tweet->getTextOfTweet()
                                                  ]);
            }
            echo $html;
        }
        else {
            echo 'Nie ma jeszcze żanych Tweetów. Bądz pierwszy!';
        }
        /*foreach($userTweets as $tweet) {
            echo $tweet->getId().', '. $tweet->getUserId().', '. $tweet->getTextOfTweet().', '.$tweet->getTweetCreationDate().'</br>';
            echo '<a href=comments.php?tweetId='.$tweet->getId().'>Pokaż komentarze</a></br>';
        }*/
    }
    
    public function saveToDataBase($connectionToDB) {
         if($this->id = -1) {
             
             try{
             $stmt = $connectionToDB->prepare("INSERT INTO Tweets(userId, textOfTweet, tweetCreationDate)
                                               VALUES (:userId, :textOfTweet, :tweetCreationDate)");
             $result = $stmt->execute([
                                        'userId' => $this->userId,
                                        'textOfTweet' => $this->textOfTweet,
                                        'tweetCreationDate' => $this->tweetCreationDate
                                      ]);
             }
             catch(PDOException $exeption) {
                 echo "Błąd: ".$exeption->getMessage();
             }
             if($result !== false) {
                $this->id = $connectionToDB->lastInsertId();
                return true;
             }
         }
         return false;
         
    }
}
/*
$testTweet = new Tweet();
$testTweet->setTextOfTweet("Testowatresctweeta");
$testTweet->setTweetCreationDate("2017");
$testTweet->setUserId(6);

var_dump($testTweet);

var_dump($testTweet->saveToDataBase($connectionToDB));
*/
//$result = Tweet::loadTweetsByUserId($connectionToDB, 6);
//var_dump($result);
//Tweet::printTweetsByUserId($connectionToDB, 6);

//Tweet::printAllTweets($connectionToDB);