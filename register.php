<?php

require_once 'library.php';
require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

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
    </head>
    <body>
        <form method="POST">
            <fieldset>
                <label>
                    Email:
                    <input type="text" name="userEmail" />
                </label>
                <br />
                <label>
                    Password:
                    <input type="password" name="userPassword" />
                </label>
                <br />
                <label>
                    Retyped password:
                    <input type="password" name="userRetypedPassword" />
                </label>
                <br />
                <label>
                    Full name:
                    <input type="text" name="userName" />
                </label>
                <br />
                <input type="submit" value="Register" />
            </fieldset>
        </form>
    </body>
</html>