<?php

session_start();
require_once 'library.php';

if(!isset($_SESSION['loggedUserId'])) {
    header('Location: login.php');
}

$user = User::loadUserById($connectionToDB, $_SESSION['loggedUserId']);
$user->deleteUser($connectionToDB);

header('Location: login.php');