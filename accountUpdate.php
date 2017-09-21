<?php

session_start();

require_once 'library.php';
require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUserEmail = !empty($_POST['newUserEmail']) ? trim($_POST['newUserEmail']) : null;
    $newUserPassword = !empty($_POST['newUserPassword']) ? trim($_POST['newUserPassword']) : null;
    $newUserRetypedPassword = !empty($_POST['newUserRetypedPassword']) ? trim($_POST['newUserRetypedPassword']) : null;
    $newUserName = !empty($_POST['newUserName']) ? trim($_POST['newUserName']) : null;
    $loggedUser = User::loadUserById($connectionToDB, $_SESSION['loggedUserId']);
    
        if($newUserEmail && $newUserPassword && $newUserRetypedPassword && $newUserName && $newUserPassword === $newUserRetypedPassword && $loggedUser) {
            $loggedUser->setUserEmail($newUserEmail);
            $loggedUser->setUserHashPassword($newUserPassword);
            $loggedUser->setUserName($newUserName);
            try {
                $loggedUser->saveToDataBase($connectionToDB);
                echo 'Zmiana danych użytkownika zakończona powodzeniem';
            } catch (Exception $exeption) {
                echo 'Wystąpił błąd podczas zmiany danych: '.$exeption->getMessage();
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
                    Nowy Email:
                    <input type="text" name="newUserEmail" />
                </label>
                <br />
                <label>
                    Nowe Hasło:
                    <input type="password" name="newUserPassword" />
                </label>
                <br />
                <label>
                    Powtórz nowe hasło:
                    <input type="password" name="newUserRetypedPassword" />
                </label>
                <br />
                <label>
                    Nowe imię oraz nazwisko:
                    <input type="text" name="newUserName" />
                </label>
                <br />
                <input type="submit" value="Aktualizuj swoje dane" />
            </fieldset>
        </form>
    </body>
</html>