<?php
/* ============================================

   FILE: config/config.php

   ============================================ */

class config

{

    private static $pdo = null;

    public static function getConnexion()

    {

        if (!isset(self::$pdo)) {

            try {

                self::$pdo = new PDO(

                    'mysql:host=localhost;dbname=nextgen_db',

                    'root',

                    '',

                    [

                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC

                    ]

                );

            } catch (PDOException $e) {
                error_log("config::getConnexion - PDO Exception: " . $e->getMessage());
                throw new Exception('Erreur de connexion à la base de données: ' . $e->getMessage());
            } catch (Exception $e) {
                error_log("config::getConnexion - General Exception: " . $e->getMessage());
                throw $e;
            }

        }

        return self::$pdo;

    }

}

?>
