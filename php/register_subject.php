<?php

include('database_config.php');

$subject_info = json_decode(file_get_contents('php://input'), true);

try {
  $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);

  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Table should have rowID, subjectID, assigned_condition, time
  $stmt = $conn->prepare("SELECT COUNT(*) AS `count` FROM `$table_register` WHERE `workerID` = :id;");
  $stmt->bindValue(":id", $subject_info['id']);
  $stmt->execute();
  $last_condition = $stmt->fetchColumn();

  if($last_condition == 0){
    $r = array('excluded' => false);
  } else {
    $r = array('excluded' => true);
  }

  // Second stage is to create prepared SQL statement using the column
  // names as a guide to what values might be in the JSON.
  // If a value is missing from a particular trial, then NULL is inserted
  $sql = "INSERT INTO `$table_register` (`workerID`, `completionCode`) VALUES (:id, :code)";

  $code = generateRandomString(6);

  $insertstmt = $conn->prepare($sql);
  $insertstmt->bindValue(":id", $subject_info['id']);
  $insertstmt->bindValue(":code", $code);
  $insertstmt->execute();

  $r["success"] = true;
  $r["code"] = $code;
  echo json_encode($r);
} catch(PDOException $e) {
  $r = array('success' => false, 'error_message' => $e->getMessage());
  echo json_encode($r);
}

$conn = null;

function generateRandomString($length = 10) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>
