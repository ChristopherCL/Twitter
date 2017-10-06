<?php

session_start();
require_once 'library.php';

$user = User::loadUserById($connectionToDB, $_SESSION['loggedUserId']);
$user->deleteUser($connectionToDB);