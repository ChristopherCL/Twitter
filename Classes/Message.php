<?php
require_once __DIR__.'/../library.php';
//require_once 'connectionToTwitterDataBase.php';
//require_once __DIR__.'/../Functions/connectionToTwitterDataBase.php';

//$connectionToDB = connectionToTwitterDataBase();

class Message {
    private $id;
    private $textOfMessage;
    private $senderId;
    private $recipientId;
    private $status;
    private $messageCreationDate;
    private $senderUserName;
    private $recipientUserName;
    
    public function __construct() {
        $this->id = -1;
        $this->textOfMessage = '';
        $this->senderId = '';
        $this->recipientId = '';
        $this->status = '';
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getTextOfMessage() {
        return $this->textOfMessage;
    }
        
    public function setTextOfMessage($textOfMessage) {
        $this->textOfMessage = $textOfMessage;
    }
    
    public function getSenderId() {
        return $this->senderId;
    }
        
    public function setSenderId($senderId) {
        $this->senderId = $senderId;
    }
    
    public function getRecipientId() {
        return $this->recipientId;
    }
        
    public function setRecipientId($recipientId) {
        $this->recipientId = $recipientId;
    }
    
    public function getStatus() {
        return $this->status;
    }
        
    public function setStatus($status) {
        $this->status = $status;
    }
    
    public function getMessageCreationDate() {
        return $this->messageCreationDate;
    }
        
    public function setMessageCreationDate($messageCreationDate) {
        $this->messageCreationDate = $messageCreationDate;
    }
    
    public function saveToDataBase($connectionToDB) {
        if($this->id == -1) {
        $stmt = $connectionToDB->prepare("INSERT INTO Messages(textOfMessage, senderId, recipientId, status, messageCreationDate)
                                          VALUES (:textOfMessage, :senderId, :recipientId, :status, :messageCreationDate)");
        $result = $stmt->execute(['textOfMessage' => $this->textOfMessage,
                                  'senderId' => $this->senderId,
                                  'recipientId' => $this->recipientId,
                                  'status' => $this->status,
                                  'messageCreationDate' => $this->messageCreationDate
                                ]);

        if($result !== false) {
            $this->id = $connectionToDB->lastInsertId();
            return true;
        }
        return false;
        }
        /*else {
            $stmt = $connectionToDB->prepare("UPDATE Messages SET senderId=:senderId, recipientId=:recipientId, status=:status WHERE id=:id");
            $result = $stmt->execute(['senderId' => $this->senderId,
                                      'recipientId' => $this->recipientId,
                                      'status' => $this->status,
                                      'id' => $this->messageId
                                    ]);
            if($result === true) {
                return true;
            }
        }*/
        return false;    
    }
    
    static public function loadAllReceivedMessagesByUserId($connectionToDB, $userId) {
        $allUserMessages = [];
        $stmt = $connectionToDB->prepare("SELECT Messages.*, Users.userName FROM Messages, Users WHERE recipientId = :recipientId AND Users.id = Messages.senderId ORDER BY messageCreationDate DESC");
        $result = $stmt->execute(['recipientId' => $userId]);

        if($result !== false && $stmt->rowCount() != 0) {
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $messages) {
                $message = new Message();
                $message->id = $messages['id'];
                $message->senderId = $messages['senderId'];
                $message->recipientId = $messages['recipientId'];
                $message->status = $messages['status'];
                $message->textOfMessage = $messages['textOfMessage'];
                $message->messageCreationDate = $messages['messageCreationDate'];
                $message->senderUserName = $messages['userName'];
                $allUserMessages[] = $message;
            }
            return $allUserMessages;
        }
        else {
            return false;
        }

    }
    
    static public function loadAllSendedMessagesByUserId($connectionToDB, $userId) {
        $allUserMessages = [];
        $stmt = $connectionToDB->prepare("SELECT Messages.*, Users.userName FROM Messages, Users WHERE senderId = :senderId AND Users.id = Messages.recipientId ORDER BY messageCreationDate DESC");
        $result = $stmt->execute(['senderId' => $userId]);

        if($result !== false && $stmt->rowCount() != 0) {
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $messages) {
                $message = new Message();
                $message->id = $messages['id'];
                $message->senderId = $messages['senderId'];
                $message->recipientId = $messages['recipientId'];
                $message->status = $messages['status'];
                $message->textOfMessage = $messages['textOfMessage'];
                $message->messageCreationDate = $messages['messageCreationDate'];
                $message->recipientUserName = $messages['userName'];
                $allUserMessages[] = $message;
            }
            return $allUserMessages;
        }
        else {
            return false;
        }

    }
    
    static public function printAllSendedMessages($connectionToDB, $userId) {
            if($allReceivedMessages = self::loadAllSendedMessagesByUserId($connectionToDB, $userId)) {
                $html = '';
                foreach ($allReceivedMessages as $message) {
                    $html .= self::render('sendedMessagesTemplate',   [
                                                                'messageCreationDate' => $message->getMessageCreationDate(),
                                                                'recipientUserName' => $message->recipientUserName,
                                                                'textOfMessage' => $message->getTextOfMessage()
                                                                ]);
                }
                echo $html;
            }
            else {
                echo 'Nie wysłałeś jeszcze żadnej wiadmości';
            }
        }
    
    static public function render($template, $data) {
        $html = file_get_contents(__DIR__.'/../Templates/'.$template.'.html');
        foreach($data as $key=>$value) {
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }
        return $html;
        }
        
        static public function printAllReceivedMessages($connectionToDB, $userId) {
            if($allReceivedMessages = self::loadAllReceivedMessagesByUserId($connectionToDB, $userId)) {
                $html = '';
                foreach ($allReceivedMessages as $message) {
                    $html .= self::render('receivedMessagesTemplate',   [
                                                                'messageCreationDate' => $message->getMessageCreationDate(),
                                                                'senderUserName' => $message->senderUserName,
                                                                'textOfMessage' => $message->getTextOfMessage()
                                                                ]);
                }
                echo $html;
            }
            else {
                echo 'Nie otrzymałeś jeszcze żadnych wiadmości';
            }
        }
    
    /*static public function loadAllReceivedMessagesByUserId($connectionToDB, $userId) {
        $allUserMessages = [];
        $stmt = $connectionToDB->prepare("SELECT * FROM Messages WHERE recipientId = :recipientId");
        $result = $stmt->execute(['recipientId' => $userId]);

        if($result !== false && $stmt->rowCount() != 0) {
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $messages) {
                $message = new Message();
                $message->id = $messages['id'];
                $message->senderId = $messages['senderId'];
                $message->recipientId = $messages['recipientId'];
                $message->status = $messages['status'];
                $message->textOfMessage = $messages['textOfMessage'];
                $message->messageCreationDate = $messages['messageCreationDate'];
                $allUserMessages[] = $message;
            }
            return $allUserMessages;
        }
        else {
            return false;
        }

    }*/
    
    /*static public function loadAllSendedMessagesByUserId($connectionToDB, $userId) {
        $allUserMessages = [];
        $stmt = $connectionToDB->prepare("SELECT * FROM Messages WHERE senderId = :senderId");
        $result = $stmt->execute(['senderId' => $userId]);

        if($result !== false && $stmt->rowCount() != 0) {
            foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $messages) {
                $message = new Message();
                $message->id = $messages['id'];
                $message->senderId = $messages['senderId'];
                $message->recipientId = $messages['recipientId'];
                $message->status = $messages['status'];
                $message->textOfMessage = $messages['textOfMessage'];
                $message->messageCreationDate = $messages['messageCreationDate'];
                $allUserMessages[] = $message;
            }
            return $allUserMessages;
        }
        else {
            return false;
        }

    }*/
        
    public function deleteMessage($connectionToDB) {
        if($this->tweetId != -1) {
            $stmt = $connectionToDB->prepare("DELETE FROM Messages WHERE id=:id");
            $result = $stmt->execute(['id' => $this->id]);
                if($result === true) {
                    $this->messageId = -1;
                    return true;
                }
                return false;
        }
        return true;
    }
}

/*$message = new Message();
var_dump($message);
$message->setRecipientId(2);
$message->setSenderId(43);
$message->setStatus(1);

var_dump($message);

$message->saveToDB($connectionToDB);

var_dump($message);*/
//$messages = Message::loadAllMessagesByUserId($connectionToDB, 5);
//var_dump($messages);

//$massages2 = Message::loadAllSendedMessagesByUserId($connectionToDB, 43);
//var_dump($massages2);

/*$newMessage = new Message;
        $newMessage->setSenderId(2);
        $newMessage->setRecipientId(3);
        $newMessage->setTextOfMessage('hfihfe');
        $newMessage->setStatus(1);
        $newMessage->setMessageCreationDate('2014');
        var_dump($newMessage);
        $newMessage->saveToDataBase($connectionToDB);
        try {
            $newMessage->saveToDataBase($connectionToDB);
            echo 'Wysłano wiadomość do użytkownika';
        } catch (Exception $exeption) {
            echo 'Błąd wysyłania wiadomości: '.$exeption->getMessage();
        }*/

    // Message::printAllReceivedMessages($connectionToDB, 1);

//$result = Message::loadAllSendedMessagesByUserId($connectionToDB, 2);
//var_dump($result);
//Message::printAllSendedMessages($connectionToDB, 2);
//Message::printAllReceivedMessages($connectionToDB, 2);