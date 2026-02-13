<?php

class DatabaseService
{
    public function connect()
    {
        $host = 'localhost';
        $dbName = 'ticketing_app';
        $user = 'root';
        $password = 'root';

        try {
            $bdd = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $user, $password);
            
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $bdd;

        } catch (Exception $e) {
            die('Erreur de connexion : '.$e->getMessage());
        }
    }
}