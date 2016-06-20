<?php
// First read in a couple of POST variables passed in with the request

// This is a unique ID generated for this call
$sessionId = $_POST['sessionId'];

// Check to see whether this call is active
$isActive  = $_POST['isActive'];

if ($isActive == 1)  {
  //simulating a database call
  
  // Compose the response
  $response  = '<?xml version="1.0" encoding="UTF-8"?>';
  $response .= '<Response>';
  $response .= '<Dial phoneNumbers="+254787235065" record="true" ringBackTone="http://1ac4c2a1.ngrok.io/Rabbit.mp3" sequential="true"/>';
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
  
  // You can then store this information in the database for your records

}
