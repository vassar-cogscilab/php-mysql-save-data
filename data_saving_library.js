function register_mturk_worker(workerID, callback_success, callback_failure){
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'php/exclude/check_subject_exclusion.php');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if(xhr.status == 200){
      var response = JSON.parse(xhr.responseText);
      if(response.success){
        callback_success(response);
      } else {
        callback_failure();
      }
    }
  };
  xhr.onerror = function(){
    callback_failure();
  }
  xhr.send(JSON.stringify({id: workerID}));
}

function assign_condition(subject_id, n_conditions, callback_success, callback_failure){
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'php/assign/assign_subject_condition.php');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if(xhr.status == 200){
      var response = JSON.parse(xhr.responseText);
      console.log(response.success);
      console.log(response.assigned_condition);
      if(response.success){
        callback_success(response);
      } else {
        callback_failure();
      }
    }
  };
  xhr.onerror = function() {
    callback_failure();
  }
  xhr.send(JSON.stringify({n_conditions: n_conditions, id: subject_id}));
}

function save_data(data){
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'php/write/write_data.php');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if(xhr.status == 200){
      var response = JSON.parse(xhr.responseText);
      console.log(response.success);
    }
  };
  xhr.send(JSON.stringify(data));
}
