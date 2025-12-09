<?php

class config
{
  private static function createConnexion()
  {
    return new PDO(
      'mysql:host=127.0.0.1;port=3306;dbname=nextgen_db;charset=utf8mb4',
      'root',
      '',
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION sql_mode='STRICT_TRANS_TABLES'",
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
      ]
    );
  }

  public static function getConnexion()
  {
    try {
      return self::createConnexion();
    } catch (PDOException $e) {
      if (stripos($e->getMessage(), 'gone away') !== false || stripos($e->getMessage(), 'Packets out of order') !== false) {
        return self::createConnexion();
      }
      die('Erreur de connexion MySQL: ' . $e->getMessage());
    }
  }
}