<?php

class Database
{
    private static $dbHost = "localhost";
    private static $dbName = "burger_code";
    private static $dbUser = "root";
    private static $dbUserPassword = "";

    private static $connexion = null;
    
    
    /**
     * Fonction qui permet de se connecter à notre BDD
     *
     * @return void
     */
    public static function connect()
    {
        try
        {
            self::$connexion = new PDO('mysql:host=' . self::$dbHost . ';dbname=' . self::$dbName,self::$dbUser,self::$dbUserPassword);
        }
        catch(PDOException $e)
        {
            die($e->getMessage());
        }
        self::$connexion->query("SET NAMES UTF8");//Solution encodage UTF8
        return self::$connexion;
    }

    /**
     * Fonction qui permet de se déconnecter de notre BDD
     *
     * @return void
     */
    public static function disconnect()
    {
        self::$connexion = null;
    }
       
}

?>