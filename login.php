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
    </head>
    <body>
        <form action="" method="POST">
            <fieldset>
                <legend>Formularz rejestracji</legend>
                <label for="userEmail">Email:</label>
                    <input type="text" name="userEmail" id="userEmail"/>
               
                <br />
                <label>
                    Password:
                    <input type="password" name="userPassword" />
                </label>
                <br />
                <input type="submit" value="Login" />
            </fieldset>
        </form>
        <a href="register.php">If you do not have an account - sign up!</a>
    </body>
</html>