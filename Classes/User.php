<?php
require_once __DIR__.'/../library.php';

//require_once __DIR__.'/../Functions/connectionToTwitterDataBase.php';
//require_once __DIR__.'/../library.php';

/*function connectionToTwitterDataBase() {

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
}*/
//$connectionToDB = connectionToTwitterDataBase();

//$connectionToDB = connectionToTwitterDataBase();
//require_once 'connectionToTwitterDataBase.php';
//var_dump($connectionToDB);
class User {
    
    private $id;
    private $userName;
    private $userHashPassword;
    private $userEmail;
    private $numberOfReceivedMessages;
    private $numberOfSendedTweets;
    private $numberOfSendedComments;
    
    public function __construct() {
        $this->id = -1;
        $this->userName = '';
        $this->userHashPassword = '';
        $this->userEmail = '';
    }
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function setUserName($userName) {
        $this->userName = $userName;
    }
    
    public function getUserName() {
        return $this->userName;
    }
    
    public function setUserHashPassword($newUserPassword) {
        $this->userHashPassword = password_hash($newUserPassword, PASSWORD_BCRYPT);
    }
    
    public function getUserHashPassword() {
        return $this->userHashPassword;
    }
    
    public function setUserEmail($userEmail) {
        $this->userEmail = $userEmail;
    }
    
    public function getUserEmail() {
        return $this->userEmail;
    }
    
    public function saveToDataBase($connectionToDB) {
        if($this->id == -1) {
            $stmt = $connectionToDB->prepare("INSERT INTO Users(userName, userEmail, userHashPassword)
                                              VALUES (:userName, :userEmail, :userPassword)");
            $result = $stmt->execute(['userName' => $this->userName,
                                      'userEmail' => $this->userEmail,
                                      'userPassword' => $this->userHashPassword
                                    ]);
        
            if($result !== false) {
                $this->id = $connectionToDB->lastInsertId();
                return true;
            }
            return false;
        }
        else {
            $stmt = $connectionToDB->prepare("UPDATE Users SET userName=:userName, userHashPassword=:userHashPassword, userEmail=:userEmail WHERE id=:id");
            $result = $stmt->execute([
                                       'userName' => $this->userName,
                                       'userHashPassword' => $this->userHashPassword,
                                       'userEmail' => $this->userEmail,
                                       'id' => $this->id
                                    ]);
            if($result === true) {
                return true;
            }
        }
        return false;
    }
    
    static public function loadUserById($connectionToDB, $id) {
        $stmt = $connectionToDB->prepare("SELECT * FROM Users WHERE id=:id");
        $result = $stmt->execute(['id' => $id]);
      
        if($result === true && $stmt->rowCount() > 0) {
            try {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $loadedUser = new User();
                $loadedUser->id = $row['id'];
                $loadedUser->userName = $row['userName'];
                $loadedUser->userHashPassword = $row['userHashPassword'];
                $loadedUser->userEmail = $row['userEmail'];
                return $loadedUser;
            }
            catch(PDOException $exeption) {
                echo "Błąd: ".$exeption->getMessage();
            }
        }
        return null;
    }
    
    static public function loadUserActivity($connectionToDB, $userId) {
        $stmt = $connectionToDB->prepare("SELECT (SELECT COUNT(Comments.userId) FROM Comments WHERE userId = :userId) as numberOfComments, (SELECT COUNT(Tweets.userId) FROM Tweets WHERE userId = :userId) as numberOfTweets, (SELECT COUNT(Messages.recipientId) FROM Messages WHERE recipientId = :userId) as numberOfMessages FROM Messages, Comments, Tweets, Users WHERE Tweets.id = Comments.postId AND Users.id = Tweets.userId AND Users.id = Comments.userId AND Users.id = Messages.senderId ");
        $result = $stmt->execute(['userId' => $userId]);
      
        if($result === true && $stmt->rowCount() > 0) {
            try {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $loadedUser = new User();
                $loadedUser->numberOfSendedComments = $row['numberOfComments'];
                $loadedUser->numberOfSendedTweets = $row['numberOfTweets'];
                $loadedUser->numberOfReceivedMessages = $row['numberOfMessages'];
                return $loadedUser;
            }
            catch(PDOException $exeption) {
                echo "Błąd: ".$exeption->getMessage();
            }
        }
        return null;
    }
    
    static public function render($template, $data) {
        $html = file_get_contents(__DIR__.'/../Templates/'.$template.'.html');
        foreach($data as $key=>$value) {
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }
        return $html;
    }
    
    static public function printUserActivity($connectionToDB, $userId) {
            $userActivity = self::loadUserActivity($connectionToDB, $userId);
            $html = '';
          
                $html .= self::render('userActivityTemplate',   [
                                                            'numberOfTweets' => $userActivity->numberOfSendedTweets,
                                                            'numberOfComments' => $userActivity->numberOfSendedComments,
                                                            'numberOfMessages' => $userActivity->numberOfReceivedMessages
                                                            ]);
            
            echo $html;
        }
    
    static public function loadAllUsers($connectionToDB) {
        $tableOfAllUsers = [];
        
        $result = $connectionToDB->query("SELECT * FROM Users");

        //var_dump($result->fetchAll(PDO::FETCH_ASSOC));// - zapytać Mentora dlaczego wywołaniu tutaj var_dumpa, var_dump(tableOfAllUseres) w linii 128 wychodzi pusty
      
        if($result !== false && $result->rowCount() != 0) {
            
            foreach($result as $row) { 
               // $row = $result->fetch(PDO::FETCH_ASSOC);
                $loadedUser = new User();
                $loadedUser->id = $row['id'];
                $loadedUser->userName = $row['userName'];
                $loadedUser->userHashPassword = $row['userHashPassword'];
                $loadedUser->userEmail = $row['userEmail'];
                $tableOfAllUsers[] = $loadedUser;

            }
            
        }      
        //var_dump($tableOfAllUsers);        
        return $tableOfAllUsers;
    }
    
    public static function loadUserByEmail($connectionToDB, $userEmail) {
        $stmt = $connectionToDB->prepare("SELECT * FROM Users WHERE userEmail=:userEmail ");
        $stmt->execute(['userEmail' => $userEmail]);
        if($stmt->rowCount() === 1) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        else {
            return false;
        }
    }
    
    public static function login($connectionToDB, $userEmail, $userPassword) {
        $stmt = $connectionToDB->prepare("SELECT * FROM Users WHERE userEmail=:userEmail ");
        $stmt->execute(['userEmail' => $userEmail]);
        if($stmt->rowCount() === 1) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($userPassword, $userData['userHashPassword'])) {
                    return $userData['id'];
                }
                else {
                    return false;
                }
        }
        else {
            return false;
        }
    }
    
    public function deleteUser($connectionToDB) {
        if($this->id != -1) {
            $stmt = $connectionToDB->prepare("DELETE FROM Users WHERE id=:id");
            $result = $stmt->execute(['id' => $this->id]);
                if($result === true) {
                    $this->id = -1;
                    return true;
                }
                return false;
        }
        return true;
    }
}

//$TestUser = new User();
//var_dump($TestUser);
//$TestUser->setUserName("Krzysztof");
//$TestUser->setUserHashPassword(1234);
//$TestUser->setUserEmail("krzysztof@gmail.com");
//var_dump($TestUser);
//$TestUser->saveToDataBase($connectionToDB);
//var_dump($TestUser);
//$result2 = User::loadUserById($connectionToDB, 6);
//var_dump($result2);

//$result3 = User::loadAllUsers($connectionToDB);
//var_dump($result3);

//$result2->deleteUser($connectionToDB);

//$result2->setUserEmail('Kubica@gmail.com');
//$result2->saveToDataBase($connectionToDB);

//$result = User::loadUserByEmail($connectionToDB, 'Kubica@gmail.com');
//var_dump($result);

//$result4 = User::login($connectionToDB, 'Kubica@gmail.com', '1234');
//var_dump($result4);

//$result = User::loadUserActivity($connectionToDB, 1);
//var_dump($result);

//User::printUserActivity($connectionToDB, 1);