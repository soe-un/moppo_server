<?php
	error_reporting(E_ALL); 
	ini_set('display_errors', 1); 

	include('dbcon.php');


	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

	if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
	{
		$userNo = $_POST["userNo"];
		$typeMoney = $_POST["typeMoney"];
		$typeNo = $_POST["typeNo"];
	        
		//error check
		if(empty($userNo)){
			$errMSG = "No userNo";
		}else if(empty($typeMoney)){
			$errMSG = "No typeMoney";
		}else if(empty($typeNo)){
			$errMSG = "No typeNo";
		}

		if(!isset($errMSG)){
			try {
				$stmt = $con->prepare('SELECT * FROM users WHERE idx = :userNo ');

				$stmt->bindParam(':userNo', $userNo);
				$stmt->execute();
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				if($res['totalMoney']){
					if($res['totalMoney'] < $typeMoney){
						$errMSG = "NO money";
					}else{
                				$stmt = $con->prepare('INSERT INTO money VALUES(NULL, :userNo, 0, :typeMoney, :typeNo, SYSDATE())');
                				$stmt->bindParam(':userNo', $userNo);
                				$stmt->bindParam(':typeMoney', $typeMoney);
						$stmt->bindParam(':typeNo', $typeNo);
				
                				//query 실행
                				if($stmt->execute()){
							$stmts = $con->prepare('INSERT INTO money VALUES(NULL, :typeNo, 1, :typeMoney, :userNo, SYSDATE())');
							$stmts->bindParam(':userNo', $userNo);
							$stmts->bindParam(':typeMoney', $typeMoney);
							$stmts->bindParam(':typeNo', $typeNo);
							if($stmts->execute()){
								$successMSG = "success";
							}
						}else{ $errMSG = "fail"; }
					}

					$response = array();
					if(isset($errMSG)) {
						$response["success"] = false;
						$response["message"] = $errMSG;
					}
					if(isset($successMSG)){
						$response["success"] = true;
						$response["message"] = $successMSG;
					}
					echo json_encode($response);
				}                		
			} catch(PDOException $e) {
				die("Database error: " . $e->getMessage()); 
			}
		}
	}
?>
