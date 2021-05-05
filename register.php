<?php
	$con = mysqli_connect("localhost", "mop", "moppo", "mop");
	mysqli_query($con, "SET NAMES utf8");

	$userID = $_POST["userID"];
	$userPwd = $_POST["userPwd"];
	$userName = $_POST["userName"];
	$nickName = $_POST["nickName"];


	$statement = mysqli_prepare($con, "INSERT INTO tmplogin (?,?,?,?)");
	mysqli_stmt_bind_param($statement, "ssss", $userID, $userPwd, $userName, $nickName);
	mysqli_stmt_execute($statement);

	$response = array();
	$response["success"] = true;

	echo json_encode($response);

?>