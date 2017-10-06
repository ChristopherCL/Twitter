<?php
require_once __DIR__.'/../library.php';

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
    
    public function getId() : int {
        return $this->id;
    }
    
    public function getTextOfMessage() : string {
        return $this->textOfMessage;
    }
        
    public function setTextOfMessage(string $textOfMessage) {
        $this->textOfMessage = $textOfMessage;
    }
    
    public function getSenderId() : int {
        return $this->senderId;
    }
        
    public function setSenderId(int $senderId) {
        $this->senderId = $senderId;
    }
    
    public function getRecipientId() : int {
        return $this->recipientId;
    }
        
    public function setRecipientId(int $recipientId) {
        $this->recipientId = $recipientId;
    }
    
    public function getStatus() : int {
        return $this->status;
    }
        
    public function setStatus(int $status) {
        $this->status = $status;
    }
    
    public function getMessageCreationDate() : string {
        return $this->messageCreationDate;
    }
        
    public function setMessageCreationDate(string $messageCreationDate) {
        $this->messageCreationDate = $messageCreationDate;
    }
    
    public function saveToDataBase(PDO $connectionToDB) : bool {
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
    
    static public function loadAllReceivedMessagesByUserId(PDO $connectionToDB, int $userId) {
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
    
    static public function loadAllSendedMessagesByUserId(PDO $connectionToDB,int $userId) {
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
    
    static public function printAllSendedMessages(PDO $connectionToDB, int $userId) {
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
    
    static public function render(string $template, array $data) {
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