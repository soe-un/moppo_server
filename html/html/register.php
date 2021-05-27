<?php
	error_reporting(E_ALL); 
	ini_set('display_errors', 1); 

	include('dbcon.php');


	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

	if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
	{
		$userID = $_POST["userID"];
		$userPwd = $_POST["userPwd"];
		$name = $_POST["name"];
		$nickname = $_POST["nickname"];
	        
		//error check
		if(empty($userID)){
			$errMSG = "아이디를 입력하세요.";
		}else if(empty($userPwd)){
			$errMSG = "비밀번호를 입력하세요.";
		}else if(empty($name)){
			$errMSG = "이름을 입력하세요.";
		}else if(empty($nickname)){
			$errMSG = "별명을 입력하세요.";
		}

		if(!isset($errMSG)){
			try {
				$stmts = $con->prepare('SELECT * FROM users WHERE userID = :userID');
				$stmts->bindParam(':userID', $userID);
				$stmts->execute();
				$dupresult = $stmts->fetch(PDO::FETCH_ASSOC); 
				
				if($dupresult['name']) {
					$errMSG = "중복 아이디입니다.";
				} else {
                	$stmt = $con->prepare('INSERT INTO users VALUES(NULL, :userID, :userPwd, :name, :nickname, 0, SYSDATE())');
                	$stmt->bindParam(':userID', $userID);
                	$stmt->bindParam(':userPwd', $userPwd);
					$stmt->bindParam(':name', $name);
					$stmt->bindParam(':nickname', $nickname);

                			//query 실행
                	if($stmt->execute()){
						$successMSG = "success";
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

                		
            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage()); 
            }
		}
	}
?>


<?php
/*
	if (isset($errMSG)) echo $errMSG;
	if (isset($successMSG)) echo $successMSG;
*/
	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

	if( !$android )
	{
?>
	<html>
		<body>
			<form action="<?php $_PHP_SELF ?>" method="POST">
                		ID: <input type = "text" name = "userID" />
                		PASSWORD: <input type = "text" name = "userPwd" />
                		NAME: <input type = "text" name = "name" />
                		NICKNAME: <input type = "text" name = "nickname" />
                	<input type = "submit" name = "submit" />
			</form>
		</body>
	</html>

<?php
	}
?>
