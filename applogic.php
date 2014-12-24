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
	$eq = mysql_query("SELECT * FROM entries WHERE owner='{$id}' ORDER BY date DESC");
	while($row = mysql_fetch_assoc($eq)) {
		$arr[] = $row;
	}
	respondWithSuccessData($arr);
}
else if($req=="submitEntry") {
	$areaArr = array("head","shoulders","knees","toes");
	$durArr = array("< 1 Hour", "2-3 Hours", "3-4 Hours", "4+ Hours");
	$id = $_POST['id'];
	$area = $areaArr[(int)$_POST['area']];
	$intensity = (int)$_POST['intensity'];
	$duration = $durArr[(int)$_POST['duration']];
	$d = date("Y-m-d", strtotime($_POST['date']));
	if(mysql_query("INSERT INTO entries VALUES(NULL, '{$id}', '{$intensity}', '{$area}', '{$d}', '{$duration}')")) {
		respondWithSuccess();
	}
	else respondWithError("An error occurred while submitting your entry. Please try again later.");
}
else if($req=="deleteEntry") {
	$id = $_POST['id'];
	if(mysql_num_rows(mysql_query("SELECT id FROM entries WHERE id='{$id}'"))==0)respondWithError("This entry was not found. Refresh your diary and try again.");
	$delete = mysql_query("DELETE FROM entries WHERE id='{$id}'");
	if($delete) {
		respondWithSuccess();
	}
	else respondWithError("This entry could not be deleted. Please try again later.");
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