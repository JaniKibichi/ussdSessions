<?php
require_once('sankaraDB.php');

//Receive post to activate call
$callSessionId = $_POST['sessionId'];
$caller = $_POST['callerNumber'];
$isActive  = $_POST['isActive'];

if ($isActive == 1)  {  
  // Specify the numbers to call
// Query random user, make call, record the conversation
  $dialQuery="SELECT * FROM users ORDER BY RAND() LIMIT 1";
  $dialResult=$db->query($dialQuery);
  $dialAvail=$dialResult->fetch_assoc();
  $dialOut = $dialAvail['phonenumber'];

  // Forward by dialing customer service numbers and record the conversation
  // Compose the response
  $response  = '<?xml version="1.0" encoding="UTF-8"?>';
  $response .= '<Response>';
  $response .= '<Dial record="true" sequential="true" phoneNumbers="+254787235065" />';
  $response .= '</Response>';
  
  // Print the response onto the page so that our gateway can read it
  header('Content-type: text/plain');
  echo $response;

} else {
  
  // Read in call details (duration, cost). This flag is set once the call is completed.
  // Note that the gateway does not expect a response in thie case
  
  $duration     = $_POST['durationInSeconds'];
  $currencyCode = $_POST['currencyCode'];
  $amount       = $_POST['amount'];
  //$recording    = $_POST['recordingUrl'];
  // You can then store this information in the database for your records
  //$dialQuery = "insert into `services`(`callSession_id`, `phoneNumber`,`service`, 'amount', 'currencyCode', 'recording')
  //values('".$sessionId."','".$phoneNumber."', 'calling out','".$amount."', '".$currencyCode."''".$recording."')";
  //$db->query($dialQuery);
}

?>