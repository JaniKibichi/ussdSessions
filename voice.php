<?php
// Be sure to include our gateway class
require_once('AfricasTalkingGateway.php');

// Specify your login credentials
$username   = "WeloveNerds";
$apikey     = "3ff0fe9052a80898fdc456dbb55caa29304303f6979e56041dbd3d97a319f42d";

// Specify your Africa's Talking phone number in international format
$from = "+254711082880";

// Specify the numbers that you want to call to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$to   = "+254720726501";

// Create a new instance of our awesome gateway class
$gateway = new AfricasTalkingGateway($username, $apikey);

// Any gateway errors will be captured by our custom Exception class below, 
// so wrap the call in a try-catch block
try 
{
  // Make the call
  $results = $gateway->call($from, $to);

  //This will loop through all the numbers you requested to be called
  foreach($results as $result) {
	//Only status "Queued" means the call was successfully placed
	echo $result->status;
	echo $result->phoneNumber;
	echo "<br/>";
  }
		
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error while making the call: ".$e->getMessage();
}

// DONE!!! 