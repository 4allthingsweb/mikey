<?
include "connect_prepared.php";

function DuplicateMySQLRecord ($table, $id_field, $id) {
  // load the original record into an array
 // $result = mysql_query("SELECT * FROM {$table} WHERE {$id_field}={$id}");
 // $original_record = mysql_fetch_assoc($result);
  $sql="SELECT * FROM {$table} WHERE {$id_field}={$id}";
		$result = $conn->query($sql);
		$original_record = $result->fetch_assoc();

  // insert the new record and get the new auto_increment id
//  mysql_query("INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)");
  $sql="INSERT INTO {$table} (`{$id_field}`) VALUES (NULL)";
 $result = $conn->query($sql);
 $newid = $conn->insert_id;

  // generate the query to update the new record with the previous values
  $query = "UPDATE {$table} SET ";
  foreach ($original_record as $key => $value) {
    if ($key != $id_field) {
        $query .= '`'.$key.'` = "'.str_replace('"','\"',$value).'", ';
    }
  }
  $query = substr($query,0,strlen($query)-2); # lop off the extra trailing comma
  $query .= " WHERE {$id_field}={$newid}";
$result = $conn->query($query);

  // return the new id
  return $newid;
}

include "twilio_send.php";

//get a unique id
$chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$length = 10;

$max_i = strlen($chars)-1;
$value = '';
for ($i=0;$i<$length;$i++)
{
$value .= $chars{mt_rand(0,$max_i)};
}

$newjobcode=$value;
// get a unique ID

$userid=$_COOKIE['theid'];
$id=$_GET['jobid'];
$thetable="loadjob";
$newid = DuplicateMySQLRecord ($thetable,'loadid', $id);
$userid=$_COOKIE['theid'];

$today=date("m/d/Y");
//$timestamp = strtotime(date("H:i")) + 60*60;
$timestamp = strtotime(date("H:i"));
$currenttime = date('h:i A', $timestamp);

$sql="update ".$thetable." set orderdate='".$today."',ordertime='".$currenttime."',jobcode='$newjobcode', otherjob=".$id.",enteredby=".$userid.",entereddate='".$now."', techid=0,techid2=0,status='ENTERED' where loadid=".$newid;
		$result2 = $conn->query($sql);

$sql="update ".$thetable." set otherjob=".$newid." where loadid=".$id;
		$result2 = $conn->query($sql);


// get the job information
$sql2 = "SELECT * from loadjob,customer where custid=customerid and loadid=".$newid;
		$result2 = $conn->query($sql2);
		while($row2 = $result2->fetch_assoc()){
					$carrier=$row2['carrier'];
					$ponum=$row2['ponum'];
					$jobcode=$row2['jobcode'];
					$customerid=$row2['customerid'];
					$orderdate=$row2['orderdate'];
					$ordertime=$row2['ordertime'];
					$location=strtoupper($row2['office']);
					$nearestcity=$row2['nearestcity'];
					$bdhwy=$row2['bdhwy'];
					$bdmile=$row2['bdmile'];
					$bdloc=strtoupper($row2['bdloc']);
					$bdaddr=strtoupper($row2['bdaddr']);
					$bdcity=strtoupper($row2['bdcity']);
					$bdstate=strtoupper($row2['bdstate']);
					$bdzip=$row2['bdzip'];
					$direction=$row2['direction'];
					$custname=$row2['custname'];
					$billingfirst=$row2['billingfirst'];
					$billinglast=$row2['billinglast'];
			}
					// send the link
								if ($custname=='') {
								$custname=$billingfirst." ".$billinglast;
							}
				$location=str_replace("\r\n","",$location);
						$message = "CUSTOMER: ".strtoupper($custname)."\r\n\r\n";
						if ($carrier){
						$message.="CARRIER: ".strtoupper($carrier)."\r\n\r\n";
						}
						$message.= "LOCATION: ".strtoupper($location)."\r\n\r\n";
						$message.= "shacklefordenterprises.com/dispatchadmin/techview.php?bp=1&loadid=".$jobid;
						//$markmessage = "shacklefordenterprises.com/dispatchadmin/techview.php?bp=1&loadid=".$jobid;
							  
						//send the email
						$shacktext="4232901234";
						$to = $shacktext;
						$subject = $custname." - ".$jobid;
						$from = "mshack@shacklefordenterprises.com";
						$headers = "From: $from";
					
						//$client->account->messages->create(array( 
						//	'To' => "+1".$shacktext, 
						//'From' => "+1"."4236161245", 
						//	'Body' => $message
						//));
						


header ('location:jobview.php?loadid='.$newid);


?>