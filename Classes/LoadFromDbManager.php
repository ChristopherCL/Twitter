<?php

require_once 'connectionToTwitterDataBase.php';
require_once 'Tweet.php';
require_once 'User.php';

class LoadFromDbManager {
    
    private static $connectionToDB;
    
    public function __construct() {
        if(!self::$connectionToDB instanceof \PDO) {
            self::$connectionToDB = connectionToTwitterDataBase();
        }
    }
    
    public function getArrayKeys($array) {    //zapytać Mentora czy lepiej prywatna statyczna.
        return array_keys($array);
    }
    
    public function preparationClassName($name) {
        return substr($name,0,-1);
    }

    
    public function loadById($name, $id) {
        $className = self::preparationClassName($name);
        $stmt = self::$connectionToDB->prepare("SELECT * FROM $name WHERE id=:id");
        $result = $stmt->execute(['id' => $id]);
        
            if($result && $stmt->rowCount()>0) {
                $array = $stmt->fetch(PDO::FETCH_ASSOC);
                $object = new $className();
                    foreach(self::getArrayKeys($array) as $key) {
                        $object->{"set".ucfirst($key)}($array[$key]);
                    }
                return $object;
            }
            
    }
    
    public function loadAllById($name) {
        $objects = [];
        $className = self::preparationClassName($name);
        $result = self::$connectionToDB->query("SELECT * FROM $name");
        
            if($result !== 0 && $result->rowCount() != 0) {
                
                foreach($result->fetchAll(PDO::FETCH_ASSOC) as $array) {
                    $object = new $className();
                        foreach(self::getArrayKeys($array) as $key) {
                            $object->{"set".ucfirst($key)}($array[$key]);
                        }
                    $objects[] = $object;
                }
            }
            
            return $objects;      
    }
}

$DBManager = new LoadFromDbManager();

/*$tweet = $DBManager->loadById('Tweets', 3);
$user = $DBManager->loadById('Users', 5);
$tweet2 = $DBManager->loadById('Tweets', 4);

var_dump($tweet);
var_dump($user);
var_dump($tweet2);*/
$allTweets = $DBManager->loadAllById('Tweets');
$allUsers = $DBManager->loadAllById('Users');

var_dump($allUsers);
var_dump($allTweets);