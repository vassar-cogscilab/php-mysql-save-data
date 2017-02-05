<?php

/*

DATABASE TABLE:

This script expects the $table specified in database_config.php to have at least three columns:

rowID - INT - primary key - auto increment
subjectID - type is up to you. you will send subjectID value from JavaScript
assigned_condition - INT

You may optionally add a timestamp column with default value CURRENT_TIMESTAMP


HOW TO USE FROM JAVASCRIPT:

var n_conditions = 4;
var subject_id = "ABC123";

var xhr = new XMLHttpRequest();
xhr.open('POST', 'write_data.php');
xhr.setRequestHeader('Content-Type', 'application/json');
xhr.onload = function() {
  if(xhr.status == 200){
    var response = JSON.parse(xhr.responseText);
    console.log(response);
  }
};
xhr.send(JSON.stringify({n_conditions: n_conditions, id: subjectID}));

*/

include('database_config.php');

$subject_info = json_decode(file_get_contents('php://input'), true);

try {
  $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);

  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Table should have rowID, subjectID, assigned_condition, time
  $stmt = $conn->prepare("SELECT assigned_condition FROM `$table` WHERE `rowID` = (SELECT MAX(`rowID`) FROM `$table`);");

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
  $sql = "INSERT INTO `$table` (`subjectID`, `assigned_condition`) VALUES (:subjectID, :assignedCondition)";

  $insertstmt = $conn->prepare($sql);
  $insertstmt->bindValue(":subjectID", $subject_info['id']);
  $insertstmt->bindValue(":assignedCondition", $next_condition);
  $insertstmt->execute();

  $r = array('success' => true, 'assigned_condition' => $next_condition);
  echo json_encode($r);
} catch(PDOException $e) {
  $r = array('success' => false, 'error_message' => $e->getMessage());
  echo json_encode($r);
}

$conn = null;

?>
