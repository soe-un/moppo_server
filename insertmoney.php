<?php

    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {
	$userNo = $_POST["userNo"];
	$typeMoney = $_POST["typeMoney"];
	$qflag = $_POST["qflag"];

	if(empty($userNo)){ $errMSG = "NOuserNO"; }
	else if(empty($typeMoney)){ $errMSG = "NotypeMoney"; }
	else if(empty($qflag)){ $errMSG = "Noqflag"; }

	if(!isset($errMSG)){
		if($qflag == 0){ // new record
			try{
				$stmt = $con->prepare('INSERT INTO money VALUES (NULL, :userNo, 1, :typeMoney, :userNo, SYSDATE())');
				$stmt->bindParam(':userNo', $userNo);
				$stmt->bindParam(':typeMoney', $typeMoney);

				if($stmt->execute()){
					$successMSG = "success";
				}else{
					$errMSG = "fail";
				}

			
				if(isset($errMSG)) {
					$response = $errMSG;
				}
				if(isset($successMSG)){
					$response = $successMSG;
				} 

				echo $response;
			
			} catch(PDOException $e){
				die("Database error: ". $e->getMessage());
			}
	
		}

		if($qflag == 1){ //update record
		




		}

		if($qflag == 2){ //delete record
		}
	}





    }



?>
