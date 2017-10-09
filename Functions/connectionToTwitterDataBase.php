<?php

function connectionToTwitterDataBase() {

    try {
                
        $serverName = "localhost";
        $userName = "root";
        $password = "coderslab";
        $dataBaseName = "TWITTER3";
                
        return new PDO("mysql:host=$serverName;
                                   dbname=$dataBaseName;
                                   charset=utf8", $userName, $password,
                                   [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
         }
    catch (PDOException $exeption) {
              echo 'Błąd podczas łączenia z bazą danych: ' . $exeption->getMessage();
              die();
    }
}

