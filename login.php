<?php 

    error_reporting(E_ALL); 
    ini_set('display_errors', 1); 

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

        // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

		$userID = $_POST["userID"];
		$userPwd = $_POST["userPwd"];
		// echo 1234;
        if(empty($userID)){
            $errMSG = "아이디를 입력하세요.";
        }else if(empty($userPwd)){
            $errMSG = "비밀번호를 입력하세요.";
        }

        if(!isset($errMSG)) // ID와 PWD 모두 입력이 되었다면 
        {
            try{
                // SQL문을 실행하여 데이터를 MySQL 서버의 tmplogin 테이블에 저장합니다. 
                $stmt = $con->prepare('SELECT * FROM tmplogin WHERE userID = :userID AND userPwd = :userPwd');
                $stmt->bindParam(':userID', $userID);
                $stmt->bindParam(':userPwd', $userPwd);

                //query 실행
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC); 


                $response = array();

                if($result['userName']){
                	$response["success"] = true;
                	$response["userID"] = $userID;
                	$response["userPwd"] = $userPwd;
                    
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
                ID: <input type = "text" name = "ID" />
                PASSWORD: <input type = "text" name = "PASSWORD" />
                NAME: <input type = "text" name = "NAME" />
                NICKNAME: <input type = "text" name = "NICKNAME" />
                <input type = "submit" name = "submit" />
            </form>
       
       </body>
    </html>

<?php 
    }
?>