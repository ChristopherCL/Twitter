<?php

session_start();

if(isset($_SESSION['loggedUserId'])) {
    unset($_SESSION['loggedUserId']);
}

header('Location: login.php');