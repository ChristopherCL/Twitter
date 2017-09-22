<?php

session_start();
require_once 'library.php';
//require_once __DIR__.'/connectionToTwitterDataBase.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userEmail = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : null;
    $userPassword = isset($_POST['userPassword']) ? trim($_POST['userPassword']) : null;

        if($loggedUserId = User::login($connectionToDB, $userEmail, $userPassword)) {
            $_SESSION['loggedUserId'] = $loggedUserId;
            header('Location: index.php');
        }
        else { 
            echo "Niepoprawny adres email lub hasło.";     
        }
        
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
            <form action="" method="POST">
           
                </br>
                <input type="text" name="userEmail" placeholder="Podaj email"/>
                </br>
                </br>
                <input type="password" name="userPassword" placeholder="Podaj hasło"/>
                </br>
                <input type="submit" value="Zaloguj" />
            </form>
                <a href="register.php">
                    <div class="link">Zarejestruj się</div>
                </a>
        </div>
    </body>
</html>