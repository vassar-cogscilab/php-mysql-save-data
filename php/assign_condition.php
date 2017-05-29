<?php

include('database_config.php');

$subject_info = json_decode(file_get_contents('php://input'), true);

try {
  $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);

  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Table should have rowID, subjectID, assignedCondition, time
  $stmt = $conn->prepare("SELECT assignedCondition FROM `$table_conditions` WHERE `row` = (SELECT MAX(`row`) FROM `$table_conditions`);");

  $stmt->execute();
  $last_condition = $stmt->fetchColumn();

  if($last_condition === false){
    $next_condition = 0;
  } else {
    $next_condition = $last_condition + 1;
    if($next_condition >= $subject_info['n_conditions']){
      $next_condition = 0;
    }
  }

  // Second stage is to create prepared SQL statement using the column
  // names as a guide to what values might be in the JSON.
  // If a value is missing from a particular trial, then NULL is inserted
  $sql = "INSERT INTO `$table_conditions` (`subjectID`, `assignedCondition`) VALUES (:subjectID, :assignedCondition)";

  $insertstmt = $conn->prepare($sql);
  $insertstmt->bindValue(":subjectID", $subject_info['id']);
  $insertstmt->bindValue(":assignedCondition", $next_condition);
  $insertstmt->execute();

  $r = array('success' => true, 'assignedCondition' => $next_condition);
  echo json_encode($r);
} catch(PDOException $e) {
  $r = array('success' => false, 'error_message' => $e->getMessage());
  echo json_encode($r);
}

$conn = null;

?>
