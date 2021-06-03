<?php 

    error_reporting(E_ALL); 
    ini_set('display_errors', 1); 

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

        // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

		$userIdx = $_POST["userIdx"];

        if(empty($userIdx)){
            $errMSG = "아이디를 입력하세요.";
        }

        if(!isset($errMSG)) // 모두 입력이 되었다면 
        {
            try{
                // SQL문을 실행하여 데이터를 MySQL 서버의 tmplogin 테이블에 저장합니다. 
                $stmt = $con->prepare('SELECT * FROM users WHERE idx = :userIdx');
                $stmt->bindParam(':userIdx', $userIdx);

                //query 실행
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC); 


                $response = array();

                if($result['name']){
                	$response["success"] = true;
                	$response["totalMoney"] = $result['totalMoney'];
                    $response["nickname"] = $result['nickname'];
                	$inmoney = 0;
                	$stmtr = $con->prepare('SELECT * FROM money WHERE (userNo = :idx AND typeFlag = 1) AND (userNo != typeNo)');
                	$stmtr->bindParam(':idx', $result['idx']);
                	$stmtr->execute();
                	while($rowr = $stmtr->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){
                        	$inmoney += $rowr['typeMoney'];
                	}
                	$response['inmoney'] = $inmoney;
                }else{
                    $response["success"] = false;
                }

                echo json_encode($response);


            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage()); 
            }
        }

    }

?>
