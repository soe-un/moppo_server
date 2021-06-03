<?php 

    //error_reporting(E_ALL); 
    //ini_set('display_errors', 1); 

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

	$userNo = $_POST["userNo"];
	
	
        if(empty($userNo)){
            $errMSG = "No userNo";
        }

        if(!isset($errMSG)) // userNo OK 
        {
            try{ 
                $stmt = $con->prepare('SELECT * FROM plans WHERE userNo = :userNo');
                $stmt->bindParam(':userNo', $userNo);

                //query 실행
                $stmt->execute();
        	$response = array();

        	while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                	$data = array();
                	$data['server_idx'] 	= $row['idx'];
                	$data['plan_name'] 	= $row['plan_name'];
                	$data['plan_order'] 	= $row['plan_order'];
			$data['income'] 	= $row['income'];
			$data['is_complete'] 	= $row['is_complete'];
			
			$data['timestamp'] 	= $row['timestamp'];

                	array_push($response, $data);
        	}

        	echo json_encode($response);

            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage()); 
            }
        }

    }

?>


<?php

    if (isset($errMSG)) echo $errMSG;
/*    if (isset($successMSG)) echo $successMSG;
*/
	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
   
    if( !$android )
    {
?>
    <html>
       <body>

            <form action="<?php $_PHP_SELF ?>" method="POST">
                userNo: <input type = "text" name = "userNo" />
                <input type = "submit" name = "submit" />
            </form>
       
       </body>
    </html>

<?php 
    }
?>
