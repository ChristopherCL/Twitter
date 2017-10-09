<?php

session_start();
require_once 'library.php';
require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

if(!isset($_SESSION['loggedUserId'])) {
    header('Location: login.php');
}
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
            <link rel="stylesheet" href="CSS/style.css" type="text/css"/>

    </head>
    <body>
        <div class="container">
            
            <div id="profile">
                <p>Informacje dotyczące Twojego konta:</p>
                <?php User::printUserActivity($connectionToDB, $_SESSION['loggedUserId']);?>
                <a href="deleteAccount.php">
                    <div class="link" style="background-color: red">Usuń swoje konto</div>
                </a>
            </div>
            
        </div>
    </body>
</html>

