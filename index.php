<?php
session_start();
require_once 'library.php';

    if(!isset($_SESSION['loggedUserId'])) {
        header('Location: login.php');
    }

    $loggedUser = User::loadUserById($connectionToDB, $_SESSION['loggedUserId']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="stylesheet" href="CSS/style.css" type="text/css"/>
 
</head>
<body>
   
    <div class="container">
        <div id="Welcome">
            <p class="navbar-brand">Witaj: <?php echo $loggedUser->getUserName();?></p>
        </div>
            <ul>
                <li>
                    <a href="profile.php">Twój profil</a>
                </li>
                <li>
                    <a href="tweets.php">Tweety</a>
                </li>
                <li>
                    <a href="messages.php">Wiadomości</a>
                </li>
                <li>
                    <a href="logout.php">Wyloguj</a>
                </li>
            </ul>
    </div>
   
</body>
</html>

