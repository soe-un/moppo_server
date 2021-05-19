<?php 

    error_reporting(E_ALL); 
    ini_set('display_errors', 1); 

    include('dbcon.php');
    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

        //android post
        $userID = $_POST["userID"];
        $typeFlag = $_POST["typeFlag"];
        $typeMoney = $_POST["typeMoney"];
        $typeID = $_POST["typeID"];

        // POST ERROR
        if(empty($userID)){
            $errMSG = "No userID";
        }else if(empty($typeFlag)){
            $errMSG = "No typeFlag";
        }else if(empty($typeMoney)){
            $errMSG = "No typeMoney";
        }else if(empty($typeID)){
            $errMSG = "No typeID";
        }else if( ($typeFlag != 1) && ($typeFlag != 0) ){
            $errMSG = "Wrong typeFlag";
        }

        if(!isset($errMSG)) //모두 입력이 되었다면 
        {
            try{
                //userNo
                $ustmt = $con->prepare('SELECT * FROM users WHERE userID = :userID');
                $ustmt->bindParam(':userID', $userID);

                //query 실행
                $ustmt->execute();
                $uresult = $ustmt->fetch(PDO::FETCH_ASSOC);

                $userNo = $uresult['idx'];

                //typeNo
                $tstmt = $con->prepare('SELECT * FROM users WHERE userID = :typeID');
                $tstmt->bindParam(':typeID', $typeID);

                //query 실행
                $tstmt->execute();
                $tresult = $ustmt->fetch(PDO::FETCH_ASSOC);

                $typeNo = $tresult['idx'];

                //constraint error
                if($uresult['idx']) {
                    $errMSG = "No userID";
                } else if ($tresult['idx']) {
                    $errMSG = "No typeID";
                } else {
                    $totalMoney = $uresult['totalMoney'];

                    //wrong case #1
                    if($totalMoney < $typeMoney){
                        $errMSG = "You don't have enough money";
                    } else {
                        $stmt = $con->prepare('INSERT INTO money VALUES(NULL, :userNo, :typeFlag, :typeMoney, :typeNo, SYSDATE())');
                        $stmt->bindParam(':userNo', $userNo);
                        $stmt->bindParam(':typeFlag', $typeFlag);
                        $stmt->bindParam(':typeMoney', $typeMoney);
                        $stmt->bindParam(':typeNo', $typeNo);
                        
                        if($stmt->execute()){
                            if($typeFlag == 1){ #add
                                $totalMoney = $totalMoney + $typeMoney;
                            }else if ($typeFlag == 0){ # sub
                                $totalMoney = $totalMoney - $typeMoney;
                            }

                            $rstmt = $con->prepare('UPDATE users SET totalMoney = :totalMoney, updatedTime = SYSDATE() WHERE userID = :userID');
                            $rstmt->bindParam(':totalMoney', $totalMoney);
                            $rstmt->bindParam(':userID', $userID);

                            if($rstmt->execute()){
                                $response = array();
                                $response["userID"] = $userID;
                                $response["totalMoney"] = $totalMoney;
                                echo json_encode($response); 
                            }
                        }else{ $errMSG = "fail"; }
                    }

                }
            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage()); 
            }
        }
    }

?>


<?php

    if (isset($errMSG)) echo $errMSG;

	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
   
    if( !$android )
    {
?>
    <html>
       <body>
            <form action="<?php $_PHP_SELF ?>" method="POST">
                ABOUT userID: <input type = "text" name = "userID" />
                ABOUT typeFlag: <input type = "text" name = "typeFlag" />
                ABOUT typeMoney: <input type = "text" name = "typeMoney" />
                ABOUT typeID: <input type = "text" name = "typeID" />
                <input type = "submit" name = "submit" />
            </form>
       </body>
    </html>

<?php 
    }
?>
