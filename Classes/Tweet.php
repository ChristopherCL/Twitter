<?php
require_once __DIR__.'/../library.php';

class Tweet {
    private $id;
    private $userId;
    private $textOfTweet;
    private $tweetCreationDate;
    private $userName;
    private $numberOfComments;
    
    public function getNumberOfComments() {
        return $this->numberOfComments;
    }

        public function __construct(){
        $this->id = -1;
        $this->userId = 0;
        $this->textOfTweet = "";
        $this->tweetCreationDate = "";
    }
    
    public function setId(int $id) {
        $this->id = $id;
    }
    
    public function getId() : int {
        return $this->id;
    }
    
    public function setUserId(int $userId){
        $this->userId = $userId;
    }
    
    public function getUserId() : int {
        return $this->userId;
    }
    
    public function setTextOfTweet(string $textOfTweet){
        $this->textOfTweet = $textOfTweet;
    }
    
    public function getTextOfTweet() : string {
        return $this->textOfTweet;
    }
    
    public function setTweetCreationDate(string $tweetCreationDate){
        $this->tweetCreationDate = $tweetCreationDate;
    }
    
    public function getTweetCreationDate() : string {
        return $this->tweetCreationDate;
    }
    
    static public function loadTweetById(PDO $connectionToDB, int $id){
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
    
    static public function loadTweetsByUserId(PDO $connectionToDB, int $userId) {
        $allUserTweets = [];
        $stmt = $connectionToDB->prepare("SELECT * FROM Tweets WHERE userId=:userId ORDER BY tweetCreationDate DESC");
        $result = $stmt->execute(['userId' => $userId]);

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
    
    static public function printTweetsByUserId(PDO $connectionToDB, int $userId) {
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
    }
    
    static public function render(string $template, array $data) {
        $html = file_get_contents(__DIR__.'/../Templates/'.$template.'.html');
        foreach($data as $key=>$value) {
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }
        return $html;
    }
    
    static public function loadAllTweets(PDO $connectionToDB) : array {
        $allTweets = [];
        $result = $connectionToDB->query("SELECT t.*, u.userName, count(c.id) as commentsNumber FROM Tweets t JOIN Users u ON t.userId = u.id LEFT JOIN Comments c ON t.id=c.postId GROUP BY t.id ORDER BY tweetCreationDate DESC");
  
        if($result !== false && $result->rowCount() != 0) {
            foreach($result as $row) {
                $loadedTweet = new Tweet();
                $loadedTweet->id = $row['id'];
                $loadedTweet->userId = $row['userId'];
                $loadedTweet->textOfTweet = $row['textOfTweet'];
                $loadedTweet->tweetCreationDate = $row['tweetCreationDate'];
                $loadedTweet->userName = $row['userName'];
                $loadedTweet->numberOfComments = $row['commentsNumber'];
                $allTweets[] = $loadedTweet;
            }
        }
        return $allTweets;
        
    }
    
    static public function printAllTweets($connectionToDB) {
        $userTweets = self::loadAllTweets($connectionToDB);
        if(is_array($userTweets)) {
            $html ='';
            foreach($userTweets as $tweet) {
                $html .= self::render('tweetsTemplate', [
                                                    'tweetId' => $tweet->getId(),
                                                    'userId' => $tweet->userName,
                                                    'tweetCreationDate' => $tweet->getTweetCreationDate(),
                                                    'textOfTweet' => $tweet->getTextOfTweet(),
                                                    'numberOfComments' => $tweet->getNumberOfComments()
                                                  ]);
            }
            echo $html;
        }
        else {
            echo 'Nie ma jeszcze żanych Tweetów. Bądz pierwszy!';
        }
    }
    
    public function saveToDataBase(PDO $connectionToDB) : bool {
         if($this->id == -1) {
             
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