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
        <div id="mainPage">
            <div id="Welcome">Witaj: <?php echo $loggedUser->getUserName();?></div>
            <a href="profile.php">
                <div class="link">Twój profil</div>
            </a>
            <a href="tweets.php">
                <div class="link">Tweety</div>
            </a>
            <a href="messages.php">
                <div class="link">Wiadomości</div>
            </a>
            <a href="logout.php">
                <div class="link"> Wyloguj</div>
            </a>
        </div>
    </div>
   
</body>
</html>

