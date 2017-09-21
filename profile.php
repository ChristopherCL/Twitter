<?php

session_start();
require_once 'library.php';
require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

User::printUserActivity($connectionToDB, $_SESSION['loggedUserId']);

?>

<a href="deleteAccount.php">Usuń swoje konto</a>