<?php 

    error_reporting(E_ALL); 
    ini_set('display_errors', 1); 

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

        $rawData = file_get_contents("php://input");
        $jsonData = json_decode($rawData);

        if(!$jsonData){ //json is not valid
            $errMSG = "WRONG INPUT";
        } else {
            $insert_list = json_decode($rawData)->{"insert_list"};
            $update_list = json_decode($rawData)->{"update_list"};
            $delete_list = json_decode($rawData)->{"delete_list"};

            $no_insert = $insert_list->{"empty"} === "yes" ? true : false;
            $no_update = $update_list->{"empty"} === "yes" ? true : false;
            $no_delete = $delete_list->{"empty"} === "yes" ? true : false;

            if(!no_insert) //insert가 있을 때 
            {
                try{
                    $stmt = $con->prepare('INSERT INTO plans VALUES(NULL, :userNo, :plan_name, :plan_order, :income, :is_complete, STR_TO_DATE(:timestamp, '%Y-%m-%d')');
                    $stmt->bindParam(':userNo',      $insert_list->{"userNo"});
                    $stmt->bindParam(':plan_name',   $insert_list->{"plan_name"});
                    $stmt->bindParam(':plan_order',  $insert_list->{"plan_order"});
                    $stmt->bindParam(':income',      $insert_list->{"income"});
                    $stmt->bindParam(':is_complete', $insert_list->{"is_complete"});
                    $stmt->bindParam(':timestamp',   $insert_list->{"timestamp"});
                    
                    //query 실행
                    if($stmt->execute()){
                        $isuccessMSG = "insert success";
                    }else{ $ierrMSG = "insert fail"; }

                }catch(PDOException $e) {
                    die("Database error: " . $e->getMessage()); 
                }
            }

            if(!no_update){ //update가 있을 때
                try{
                    $stmt = $con->prepare('UPDATE plans SET userNo = :userNo, plan_name = :plan_name, plan_order = :plan_order, income = :income, is_complete = :is_complete
                        WHERE idx = :server_idx');
                    $stmt->bindParam(':userNo', $update_list->{"userNo"});
                    $stmt->bindParam(':plan_name', $update_list->{"plan_name"});
                    $stmt->bindParam(':plan_order', $update_list->{"plan_order"});
                    $stmt->bindParam(':income', $update_list->{"income"});
                    $stmt->bindParam(':is_complete', $update_list->{"is_complete"});
                    $stmt->bindParam(':server_idx', $update_list->{"server_idx"});
                    
                    //query 실행
                    if($stmt->execute()){
                        $usuccessMSG = "update success";
                    }else{ $uerrMSG = "update fail"; }

                }catch(PDOException $e) {
                    die("Database error: " . $e->getMessage()); 
                }
            }

            if(!no_delete){ //update가 있을 때
                try{
                    $stmt = $con->prepare('DELETE from plans WHERE idx = :server_idx');
                    $stmt->bindParam(':server_idx', $delete_list->{"server_idx"});
                    
                    //query 실행
                    if($stmt->execute()){
                        $dsuccessMSG = "delete success";
                    }else{ $derrMSG = "delete fail"; }

                }catch(PDOException $e) {
                    die("Database error: " . $e->getMessage()); 
                }
            }

            if((isset($isuccessMSG) && isset($usuccessMSG)) && isset($dsuccessMSG)){
                $successMSG = "ALL DONE";
            } else {
                $errMSG = "SOMETHING PROBLEM"
            }

        }

    }

?>


<?php

    if (isset($errMSG)) echo $errMSG;
    if (isset($successMSG)) echo $successMSG;

	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
   
    if( !$android )
    {    }
?>
