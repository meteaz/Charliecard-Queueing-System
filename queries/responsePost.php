<?php
	$connecionInfoFile = parse_ini_file("connectionInfo.ini");
	$servername = "localhost";
	$username = $connecionInfoFile['username'];
	$password = $connecionInfoFile['password'];
	$dbname = $connecionInfoFile['dbname'];

	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 
	$date = date('Y-m-d H:i:s');
	
	//validate required fields
	$fnamethere= isset($_POST["firstName"]);		
	$lnamethere=isset($_POST["lastName"]);
	$bday1there=isset($_POST["birthDate3"]);
	$bday2there = isset($_POST["birthDate1"]);
	$bday3there = isset($_POST["birthDate2"]);
	$phone1there = isset($_POST["phone1"]);
	$phone2there = isset($_POST["phone2"]);
	$phone3there = isset($_POST["phone3"]);
	$add1there = isset($_POST["address1name"]);
	$citythere = isset($_POST["cityname"]);
	$statethere = isset($_POST["state"]);
	$zipthere = isset($_POST["zip"]);
	$countrythere = isset($_POST["country"]);
	$rtypethere = isset($_POST["requestType"]);
	$ptypethere = isset($_POST["passType"]);
	$pickupthere = isset($_POST["pickupType"]);
	
	
	//value constraints
	$bday1value = $_POST["birthDate3"]>1880;
	$bday2value = $_POST["birthDate2"]<32 and $_POST["birthDate2"]>0;
	$bday3value = $_POST["birthDate1"]<13 and $_POST["birthDate2"]>0;
	
	//type constraints
	$bday1type = is_numeric($_POST["birthDate3"]);
	$bday2type = is_numeric($_POST["birthDate2"]);
	$bday3type = is_numeric($_POST["birthDate1"]);
	$phone1type = is_numeric($_POST["phone1"]);
	$phone2type = is_numeric($_POST["phone2"]);
	$phone3type = is_numeric($_POST["phone3"]);
	$ziptype = is_numeric($_POST["zip"]);
	
	//$fnamethere and $lnamethere and $bday1there and $bday2there and $bday3there and $phone1there and $phone2there and $phone3there and $add1there
	//and $citythere and $statethere and $zipthere and $countrythere and $rtypethere and $ptypethere and $pickupthere and $bday1value and $bday2value and $bday3value
	//and $bday1type and $bday2type and $bday3type and $phone1type and $phone2type and $phone3type and $ziptype		
	
	$instorepickup = False;
	if($_POST["pickupType"]=="Yes"){
		$instorepickup = True;
	}
	$transferValuePHP = False;
	if($_POST["transferValue"]=="Yes"){
		$transferValuePHP = True;
	}

	$textNewDate = date($_POST["birthDate3"]."-".$_POST["birthDate1"]."-".$_POST["birthDate2"]);
	$expiryDate = date($_POST["expDate2"]."-".$_POST["expDate1"]."-".$_POST["expDateday"]);
	$sql = "
		REPLACE INTO mainQueue( 
		P_Id,
		firstname,
		lastname,
		birthdate,
		phonenumber,
		email,
		addressone,
		addresstwo,
		city,
		state,
		zipcode,
		country,
		requesttype,
		passtype,
		instorepickup,
		transfervalue,
		photopath,
		additionalInfo,
		middleInitial,
		exceptionreason,
		status,
		timeNew,
		expDate) 
		VALUES (
		DEFAULT,
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["firstName"]))) ."' ,
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["lastName"]))) ."',
		'" . htmlspecialchars($textNewDate) ."',
		'" . htmlspecialchars($_POST["phone1"].$_POST["phone2"].$_POST["phone3"]) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["email"]))) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["address1name"]))) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["address2name"]))) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["cityname"]))) ."',
		'" . htmlspecialchars($_POST["state"]) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["zip"]))) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["country"]))) ."',
		'" . htmlspecialchars($_POST["requestType"]) ."',
		'" . htmlspecialchars($_POST["passType"]) ."',
		'" . htmlspecialchars($instorepickup) ."',
		'" . htmlspecialchars($transferValuePHP) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["photoPath"]))) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["additionalInfo"]))) ."',
		'" . mysql_escape_string(trim(htmlspecialchars($_POST["middleInitial"]))) ."',
		'',
		'1',
		'" . $date . "',
		'".htmlspecialchars($expiryDate)."')";
	$fileName = 'statementLogs/sqlLogFile.txt';
	$theTime = date('m/d/Y H:i:s', time());
	$logText = $theTime . " - " . trim(preg_replace('/\t+/','',str_replace(array("\r","\n"),"",$sql))) . PHP_EOL;
	file_put_contents($fileName, $logText, FILE_APPEND);
	if ($conn->query($sql) === TRUE) {
		echo "New record created successfully";
	} else {
		error_log($conn->error);
	}

	$conn->close();
?>