<?php

require_once __DIR__.'/../library.php';

class Comment {
    private $id;
    private $userId;
    private $postId;
    private $commentCreationDate;
    private $textOfComment;
    private $commentSender;

    public function __construct() {
        $this->id = -1;
        $this->userId = '';
        $this->postId = '';
        $this->commentCreationDate = '';
        $this->textOfComment = '';
    }
        
        public function getCommentId() : int {
            return $this->commentId;
        }
        
        public function getUserId() : int {
            return $this->userId;
        }
        
        public function setUserId(int $userId) {
            $this->userId = $userId;
        }
        
        public function getPostId() : int {
            return $this->postId;
        }
        
        public function setPostId(int $postId) {
            $this->postId = $postId;
        }
        
        public function getCommentCreationDate() {
            return $this->commentCreationDate;
        }
        
        public function setCommentCreationDate($commentCreationDate) {
            $this->commentCreationDate = $commentCreationDate;
        }
        
        public function getTextOfComment() : string {
            return $this->textOfComment;
        }
        
        public function setTextOfComment(string $textOfComment) {
            $this->textOfComment = $textOfComment;
        }
        
        public function saveToDataBase(PDO $connectionToDB) : bool {
            if($this->id == -1) {
            $stmt = $connectionToDB->prepare("INSERT INTO Comments(userId, postId, commentCreationDate, textOfComment)
                                              VALUES (:userId, :postId, :commentCreationDate, :textOfComment)");
            $result = $stmt->execute(['userId' => $this->userId,
                                      'postId' => $this->postId,
                                      'commentCreationDate' => $this->commentCreationDate,
                                      'textOfComment' => $this->textOfComment
                                    ]);
        
            if($result !== false) {
                $this->id = $connectionToDB->lastInsertId();
                return true;
            }
            return false;
            }
            /*else {
                $stmt = $connectionToDB->prepare("UPDATE Comments SET commentId=:commentId, userId=:userId, postId=:postId, commentCreationDate=:commentCreationDate, text=:text WHERE id=:id");
                $result = $stmt->execute(['userId' => $this->userId,
                                          'postId' => $this->postId,
                                          'commentCreationDate' => $this->commentCreationDate,
                                          'text' => $this->text,
                                          'id' => $this->commentId
                                        ]);
                if($result === true) {
                    return true;
                }
            }*/
            return false;    
        }
        
        static public function loadAllCommentsByTweetId(PDO $connectionToDB, int $tweetId) {
            $allCommentsOfTweet = [];
            $stmt = $connectionToDB->prepare("SELECT Comments.*, Users.userName FROM Comments, Users WHERE postId = :tweetId AND Comments.userId = Users.id ORDER BY commentCreationDate DESC");
            $result = $stmt->execute(['tweetId' => $tweetId]);
            if($result !== false && $stmt->rowCount() != 0) {
                foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $comments) {
                    $comment = new Comment();
                    $comment->id = $comments['id'];
                    $comment->userId = $comments['userId'];
                    $comment->postId = $comments['postId'];
                    $comment->textOfComment = $comments['textOfComment'];
                    $comment->commentCreationDate = $comments['commentCreationDate'];
                    $comment->commentSender = $comments['userName'];
                    $allCommentsOfTweet[] = $comment;
                }
                return $allCommentsOfTweet;
            }
            else {
                return false;
            }
        }
        
        static public function render(string $template,array $data) : string {
        $html = file_get_contents(__DIR__.'/../Templates/'.$template.'.html');
        foreach($data as $key=>$value) {
            $html = str_replace('{{'.$key.'}}', $value, $html);
        }
        return $html;
        }
        
        static public function printAllCommentsOfTweet(PDO $connectionToDB,int $tweetId) {
            if($tweetComments = self::loadAllCommentsByTweetId($connectionToDB, $tweetId)){
                $html = '';
                foreach ($tweetComments as $comment) {
                    $html .= self::render('commentsTemplate',   [
                                                                'tweetCreationDate' => $comment->getCommentCreationDate(),
                                                                'commentSender' =>$comment->commentSender,
                                                                'textOfComment' => $comment->getTextOfComment()
                                                                ]);
                }
                echo $html;
                }
            else {
                echo "Ten Tweet nie posiada jeszcze komentarzy.";
            }
        }
        
        public function deleteComment(PDO $connectionToDB) : bool {
        if($this->tweetId != -1) {
            $stmt = $connectionToDB->prepare("DELETE FROM Comments WHERE id=:id");
            $result = $stmt->execute(['id' => $this->tweetId]);
                if($result === true) {
                    $this->tweetId = -1;
                    return true;
                }
                return false;
        }
        return true;
    }
}