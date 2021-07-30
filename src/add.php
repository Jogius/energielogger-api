<?php
require "./vendor/autoload.php";
// Require dependencies
use Dotenv\Dotenv;
require "./utils/DatabaseConnector.php";

// Set response headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Read values from .env
$dotenv = new DotEnv(__DIR__);
$dotenv->load();

// Initialize Database connection
$dbConnection = (new DatabaseConnector())->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (
  empty($data->token) ||
  strcmp($data->token, getenv("TOKEN")) != 0
)
{
  http_response_code(401);
  echo json_encode(array("message" => "Invalid token."));
  return;
}

if (
  empty($data->bezug) ||
  empty($data->einspeisung) ||
  empty($data->ertrag) ||
  empty($data->timestamp)
)
{
  http_response_code(400);
  echo json_encode(array("message" => "Invalid request data."));
  return;
}

try
{
  $query = "INSERT INTO data(bezug, einspeisung, ertrag, timestamp) VALUES(:bezug, :einspeisung, :ertrag, :timestamp);";

  $statement = $dbConnection->prepare($query);
  $statement->bindParam(":bezug", $data->bezug);
  $statement->bindParam(":einspeisung", $data->einspeisung);
  $statement->bindParam(":ertrag", $data->ertrag);
  $statement->bindParam(":timestamp", date("Y-m-d H:i:s", $data->timestamp / 1000));

  $success = $statement->execute();
  
  if ($success)
  {
    http_response_code(201);
    echo json_encode(array("message" => "Success."));
  }
  else
  {
    http_response_code(403);
    echo json_encode(array("message" => "Internal error."));
  }
}
catch (PDOException $e)
{
  http_response_code(400);
  echo json_encode(array("message" => $e->getmessage()));
}


