<?php

session_start();
require_once 'library.php';
require_once __DIR__.'/Functions/connectionToTwitterDataBase.php';

$user = User::loadUserById($connectionToDB, $_SESSION['loggedUserId']);
var_dump($user);
$user->deleteUser($connectionToDB);