<?php
connect("root", "paindiaries", "paindiaries");
$req = $_POST['request'];
if($req=="register") {
	$email = mysql_real_escape_string(strtolower(trim($_POST['email'])));
	$pass = md5($_POST['pass']);
	$fn = mysql_real_escape_string(trim($_POST['firstname']));
	$ln = mysql_real_escape_string(trim($_POST['lastname']));
	$dob = date("Y-m-d", strtotime($_POST['dob']));
	$gender = $_POST['gender'];
	$sq = mysql_query("SELECT id FROM users WHERE email='{$email}' LIMIT 1");
	if(mysql_num_rows($sq)==0) {
		$query = mysql_query("INSERT INTO users VALUES(NULL, '{$email}', '{$pass}', '{$fn}', '{$ln}', '{$dob}', '{$gender}')");
		if($query) {
			$idq = mysql_query("SELECT id FROM users WHERE email='{$email}'");
			$idqr = mysql_fetch_assoc($idq);
			respondWithSuccessData(array("id"=>$idqr['id']));
		}
		else respondWithError("An error occurred while creating your account. Please try again later.");
	}
	else respondWithError("There is already an account with that email.");
}
else if($req=="login") {
	$email = mysql_real_escape_string(strtolower(trim($_POST['email'])));
	$pass = md5($_POST['pass']);
	$sq = mysql_query("SELECT id FROM users WHERE email='{$email}' AND password='{$pass}' LIMIT 1");
	if(mysql_num_rows($sq)!=0) {
		$sqr = mysql_fetch_assoc($sq);
		respondWithSuccessData(array("id"=>$sqr['id']));
	}
	else respondWithError("The credentials you have provided are invalid.");
}
else if($req=="fetchEntries") {
	$id = $_POST['id'];
	$arr = array();
	$eq = mysql_query("SELECT * FROM entries WHERE owner='{$id}'");
	while($row = mysql_fetch_assoc($eq)) {
		$arr[] = $row;
	}
	respondWithSuccessData($arr);
}
else if($req=="submitEntry") {
	
}
else {
	respondWithError("Invalid Request.");
}

function respondWithError($error) {
	echo(json_encode(array("success"=>0, "error"=>$error)));
	exit();
}

function respondWithSuccessData($data) {
	echo(json_encode(array("success"=>1, "data"=>$data)));
	exit();
}

function respondWithSuccess() {
	echo(json_encode(array("success"=>1)));
	exit();
}

function connect($username, $password, $database) {
	if(strlen($database)==0 || strlen($password)==0) {
		echo("Authorization to DB is invalid.");
	}
	else {
		$link = mysql_connect('localhost', $username, $password) or die("Error occured while connecting to the database.");
		if ($link) {
			mysql_select_db($database) or die('Error occured while selecting the database.');
		}
	}
}

?>