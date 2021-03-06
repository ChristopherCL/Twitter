<?php

session_start();
require_once 'library.php';

if(!isset($_SESSION['loggedUserId'])) {
    header('Location: login.php');
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['textOfMessage'])) {
        $newMessage = new Message;
        $newMessage->setSenderId($_SESSION['loggedUserId']);
        $newMessage->setRecipientId($_POST['recipientId']);
        $newMessage->setTextOfMessage($_POST['textOfMessage']);
        $newMessage->setStatus(1);
        $newMessage->setMessageCreationDate(date('Y-m-d H:i:s'));
        try {
            $newMessage->saveToDataBase($connectionToDB);
            echo 'Wysłano wiadomość do użytkownika';
        } catch (Exception $exeption) {
            echo 'Błąd wysyłania wiadomości: '.$exeption->getMessage();
        }
    }
}

$users = User::loadAllUsers($connectionToDB);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
            <link rel="stylesheet" href="CSS/style.css" type="text/css"/>

    </head>
    <body>
        <div class="container">
            <div class="Welcome">Wiadomości</div>
            <div class="textInput">
                <form method="post">
                    <select name="recipientId" id="select">
                    <?php
                        foreach($users as $user) {
                            echo '<option value="'.$user->getId().'">'.$user->getUserName().'</option>';
                        }
                    ?>
                    </select>
                    </br>
                    <textarea rows="5" cols="50" maxlength="140" placeholder="Twoja wiadomość..." name="textOfMessage"></textarea>
                    </br>
                    <input type="submit" value="Wyślij Wiadomość">
                </form>
            </div>
            <a href="index.php"><div class="link" style="background-color: #304ac4">Strona Główna</div></a>
            <div id="messages">
                <div id="ReceivedMessages">
                    Otrzymane wiadomości:</br>
                    <?php Message::printAllReceivedMessages($connectionToDB, $_SESSION['loggedUserId']);?>
                </div>
                 <div id="SendedMessages">
                    Wysłane wiadomości:</br>
                    <?php Message::printAllSendedMessages($connectionToDB, $_SESSION['loggedUserId']);?>
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
    </body>
</html>