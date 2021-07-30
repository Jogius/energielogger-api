<?php
use Dotenv\Dotenv;

class DatabaseConnector
{
  private $dbConnection = null;

  public function __construct()
  {
    $host = getenv("DB_HOST");
    $port = getenv("DB_PORT");
    $db = getenv("DB_DATABASE");
    $username = getenv("DB_USERNAME");
    $password = getenv("DB_PASSWORD");

    try
    {
      $this->dbConnection = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
          $username,
          $password
      );
      $this->dbConnection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

      $statement = "
        CREATE TABLE IF NOT EXISTS data (
          bezug DOUBLE NOT NULL,
          einspeisung DOUBLE NOT NULL,
          ertrag DOUBLE NOT NULL,
          timestamp bigint(20) NOT NULL,
          PRIMARY KEY (timestamp)
        ) ENGINE=INNODB;
      ";

      $this->dbConnection->exec($statement);
    }
    catch (PDOException $e)
    {
      exit($e->getMessage());
    }
  }

  public function getConnection()
  {
    return $this->dbConnection;
  }
}
