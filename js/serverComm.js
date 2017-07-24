var serverComm = {};

serverComm.logging = false;

serverComm.register_subject = function(subject_id, callback_success, callback_exclude, callback_failure){
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'php/register_subject.php');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if(xhr.status == 200){
      var response = JSON.parse(xhr.responseText);
      if(serverComm.logging){
        console.log(response);
      }
      if(response.success){
        if(response.excluded){
          callback_exclude(response);
        } else {
          callback_success(response);
        }
      } else {
        callback_failure();
      }
    }
  };
  xhr.onerror = function(){
    callback_failure();
  }
  xhr.send(JSON.stringify({id: subject_id}));
}

serverComm.assign_condition = function(subject_id, n_conditions, callback_success, callback_failure){
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'php/assign_condition.php');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if(xhr.status == 200){
      var response = JSON.parse(xhr.responseText);
      if(serverComm.logging){
        console.log(response);
      }
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

serverComm.save_data = function(data){
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'php/save_data.php');
  xhr.setRequestHeader('Content-Type', 'application/json');
  xhr.onload = function() {
    if(xhr.status == 200){
      var response = JSON.parse(xhr.responseText);
      if(serverComm.logging){
        console.log(response);
      }
    }
  };
  xhr.send(JSON.stringify(data));
}
