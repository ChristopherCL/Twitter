<?php

require_once 'library.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userEmail = !empty($_POST['userEmail']) ? trim($_POST['userEmail']) : null;
    $userPassword = !empty($_POST['userPassword']) ? trim($_POST['userPassword']) : null;
    $userRetypedPassword = !empty($_POST['userRetypedPassword']) ? trim($_POST['userRetypedPassword']) : null;
    $userName = !empty($_POST['userName']) ? trim($_POST['userName']) : null;
    $user = User::loadUserByEmail($connectionToDB, $userEmail);
    
        if($userEmail && $userPassword && $userRetypedPassword && $userName && $userPassword === $userRetypedPassword && !$user) {
            $newUser = new User();
            $newUser->setUserEmail($userEmail);
            $newUser->setUserHashPassword($userPassword);
            $newUser->setUserName($userName);
            try {
                $newUser->saveToDataBase($connectionToDB);
                echo 'Rejestracja użytkownika zakończona powodzeniem';
            } catch (Exception $exeption) {
                echo 'Wystąpił błąd podczas rejestracji: '.$exeption->getMessage();
              }
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
            <div class="Welcome">Rejestracja</div>
            <form method="POST">

                <input type="text" name="userEmail" placeholder="Podaj E-mail"/>
                    <br />
                <input type="password" name="userPassword" placeholder="Podaj hasło"/>
                    <br />
                <input type="password" name="userRetypedPassword" placeholder="Powtórz hasło"/>
                    <br />
                <input type="text" name="userName" placeholder="Imię oraz nazwisko"/>
                    <br />
                <input type="submit" value="Zarejestruj się" />

            </form>
                <a href="login.php">
                    <div class="link">Logowanie</div>
                </a>
        </div>
    </body>
</html>