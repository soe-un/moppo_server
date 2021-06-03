<?php
	error_reporting(E_ALL); 
	ini_set('display_errors', 1); 

	include('dbcon.php');


	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

	if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
	{
		$userNo = $_POST["userNo"];
		$typeRate = $_POST["typeRate"];
		$cnt = 0;

		//error check
		if(empty($userNo)){
			$errMSG = "No userNo";
		}else if(empty($typeRate)){
			$errMSG = "No typeRate";
		}

		if(!isset($errMSG)){
			try {
				//기존 후원 금액에서 비율만큼 더해서 적용
				$stmtr = $con->prepare('SELECT * FROM money WHERE (typeNo = :userNo AND typeFlag = 0) AND (userNo != typeNo)');
				$stmtr->bindParam(':userNo', $userNo);
				$stmtr->execute();
				while($rowr = $stmtr->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){
					$inmoney = $rowr['typeMoney'];
					$typeNo = $rowr['userNo'];
					$typeMoney = (int)($typeRate * 0.001 * $inmoney);
					$stmt = $con->prepare('INSERT INTO money VALUES(NULL, :userNo, 1, :typeMoney, :userNo, SYSDATE())');
					$stmt->bindParam(':userNo', $typeNo);
					$stmt->bindParam(':typeMoney', $typeMoney);

					if($stmt->execute()){
						$successMSG = "success";
						$cnt += 1;
					}else{
						$errMSG = "error";
					}
				}

				$response = array();
				if(isset($errMSG)) {
					$response["success"] = false;
					$response["message"] = $errMSG;
				}
				if(isset($successMSG)){
					$response["success"] = true;
					$response["message"] = $successMSG;
					$response["cnt"] = $cnt;
				}else{
					$response["success"] = false;
					$response["message"] = "NO SUPPORT";
				}
				echo json_encode($response);
				              		
			} catch(PDOException $e) {
				die("Database error: " . $e->getMessage()); 
			}
		}
	}
?>

