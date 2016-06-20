<?php
//I have created table users with columns username varchar(30), phonenumber varchar(20), city varchar(30), status enum('ACTIVE', 'SUSPENDED')

//and table session_levels with columns session_id varchar(50), phoneNumber varchar(25), level tinyint(1)
/*This code just shows the registration part but not the booking*/
if(!empty($_POST)){
require_once('sankaraDB.php');
require_once('AfricasTalkingGateway.php');
require_once('config.php');

//receiving the POST from AT
$sessionId=$_POST['sessionId'];
$serviceCode=$_POST['serviceCode'];
$phoneNumber=$_POST['phoneNumber'];
$text=$_POST['text'];

//Explode to get the value of the latest interaction think 1*1
$textArray = explode('*', $text);

$userResponse = trim(end($textArray));

//Check the level
$level = 0;
$sql = "select `level` from `session_levels` where `session_id`='" . $sessionId . "'";
$levelQuery = $db->query($sql);
if($result = $levelQuery->fetch_assoc()) {
  $level = $result['level'];
}

//check if user is not in db
$firstQuery="SELECT * FROM users WHERE `phonenumber` LIKE '%".$phoneNumber."%' LIMIT 1";
$firstResult=$db->query($firstQuery);
$userAvail=$firstResult->fetch_assoc();


//If User Available we DONT SAY Kwaheri we give the services 
if($userAvail && $userAvail['city']!=NULL && $userAvail['username']!=NULL) {
  
  //check if user types anything
  if($userResponse=="" || $userResponse=="0" && $level == 0){
    //Graduate the user to the next level, so you dont serve them the same menu
     $s1Query = "insert into `session_levels`(`session_id`, `phoneNumber`,`level`)
        values('".$sessionId."','".$phoneNumber."', 1)";
     $db->query($s1Query);
  //Serve our services menu
  $response = "CON Karibu " . $userAvail['username']  . ". Please choose a service.\n";
  $response .= " 1. Send me todays voice tip.\n";
  $response .= " 2. Please call me!\n";
  $response .= " 3. Send me Airtime!\n";

 }elseif($level ==1){
    if($userResponse ==NULL){
      $response = "CON You have to choose a service.\n";
      $response .= " Press 0 to go back.\n";

      //Demote the user to level 0
        $s1levelUpdate = "update `session_levels` set `level`=0 where `session_id`='".$sessionId."'";
        $db->query($s1levelUpdate);

    }elseif($userResponse == "1"){
      $response = "END Please check your SMS inbox.\n";

      //Send SMS with Music
       //send SMS to get Age
            $code = '20880';
            $recipients = $phoneNumber;
            $message    = "https://hahahah12-grahamingokho.c9.io/kaka.mp3";
            $gateway    = new AfricasTalkingGateway($username, $apikey);
            try { $results = $gateway->sendMessage($recipients, $message, $code); }
            catch ( AfricasTalkingGatewayException $e ) {  echo "Encountered an error while sending: ".$e->getMessage(); }
            
    }elseif($userResponse == "2"){
          $response = "END Please wait while we place your call.\n";

        //Make a call
          // Specify your Africa's Talking phone number in international format
          $from = "+254711082880";
          // Specify the numbers to call - Query random user, make call, record the conversation
          $callQuery="SELECT * FROM users WHERE `phonenumber`= '".$phoneNumber."' ORDER BY RAND() LIMIT 1";
          $callResult=$db->query($callQuery);
          $callAvail=$callResult->fetch_assoc();
          
          $callOut = $callAvail['phonenumber'];

          //Call User and connect them to Random number from script on callback;
          $to   = $callOut;
          // Create a new instance of our awesome gateway class
          $gateway = new AfricasTalkingGateway($username, $apikey);
          // Any gateway errors will be captured by our custom Exception class below, 
          // so wrap the call in a try-catch block
          try 
          {
          // Make the call
          $gateway->call($from, $to);
          
          // Our API will now contact your callback URL once the recipient answers the call!
          }
          catch ( AfricasTalkingGatewayException $e )
          {
          echo "Encountered an error while making the call: ".$e->getMessage();
          }
    }elseif($userResponse == "3"){
    $response = "END Please wait while we load your account.\n";
    
    // Search DB and the Send Airtime
    $recipients = array( array("phoneNumber"=>"".$phoneNumber."", "amount"=>"KES 10") );
    //JSON encode
    $recipientStringFormat = json_encode($recipients);
    //Create an instance of our awesome gateway class and pass your credentials
    $gateway = new AfricasTalkingGateway($username, $apiKey);    
    // Thats it, hit send and we'll take care of the rest. Any errors will be captured in the Exception class as shown below
   try {
    $results = $gateway->sendAirtime($recipientStringFormat);



   }
   catch(AfricasTalkingGatewayException $e){
    echo $e->getMessage();
   }
  
  //Done
    }
  }
  
}else{
  if($level == 0) {
    //Graduate the user to the next level, so you dont serve them the same menu
     $insertQuery = "insert into `session_levels`(`session_id`, `phoneNumber`,`level`)
        values('".$sessionId."','".$phoneNumber."', 1)";
     $db->query($insertQuery);
     //Insert the phoneNumber, since it comes with the first POST
     $userInsert = "insert into `users`(`phonenumber`) values('".$phoneNumber."')";
     $db->query($userInsert);
     //Serve the menu for name
     $response = "CON Please enter your name";
  }
  elseif($level == 1) {
    //If user sends back nothing, we resend name request
     if($userResponse == "") {
        $response = "CON Name not supposed to be empty. Please enter your name \n";
     }
     else {
      //if user enters name we update the name
        $userUpdate = "update `users` set `username`='".$userResponse."' where `phonenumber`
          like '%". $phoneNumber ."%'";
          $db->query($userUpdate);
        //We graduate the user to the city level
        $levelUpdate = "update `session_levels` set `level`=2
                        where `session_id`='".$sessionId."'";
        $db->query($levelUpdate);
        //We request for the city
        $response = "CON Please enter your city";
     }
  }
  elseif($level == 2) {
     if($userResponse == "") {
        $response = "CON City not supposed to be empty. Please reply with your city";
     }
     else {
        $userUpdate = "Update `users` set `city`='".$userResponse."'
                      where `phonenumber` = '". $phoneNumber ."'";
        $db->query($userUpdate);
        $response = "END Thank you for registering";
     }
  }
}
//Print the response onto the page so that the ussd API/gateway can read it
header("Content-type: text/plain");
echo $response;
}


?>