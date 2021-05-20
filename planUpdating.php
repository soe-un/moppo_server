<?php

    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

        $rawData = file_get_contents("php://input");
        $jsonData = json_decode($rawData, true);

        $insert_list = $jsonData[0];
        $update_list = $jsonData[1];
        $delete_list = $jsonData[2];
        $userNo = $jsonData[3][0]['userNo'];
        $cnt = 0;

        if($insert_list[0]['empty'] === "no") //insert가 있을 때 
        {
            foreach ($insert_list as $value) {

                $iplan_name = $value['plan_name'];
                $iplan_order = $value['plan_order'];
                $iincome = $value['income'];
                $iis_complete = $value['is_complete'];
                $itimestamp = $value['timestamp'];

                try{
                    $stmt = $con->prepare('INSERT INTO plans VALUES(NULL, :userNo, :plan_name, :plan_order, :income, :is_complete, STR_TO_DATE(:timestamp, \'%Y-%m-%d\'))');
                    $stmt->bindParam(':userNo',      $userNo);
                    $stmt->bindParam(':plan_name',   $iplan_name);
                    $stmt->bindParam(':plan_order',  $iplan_order);
                    $stmt->bindParam(':income',      $iincome);
                    $stmt->bindParam(':is_complete', $iis_complete);
                    $stmt->bindParam(':timestamp',   $itimestamp);
                    
                    //query 실행
                    if($stmt->execute()){
                        $isuccessMSG = "insert success";
                        $cnt += 1;
                    }else{ $ierrMSG = "insert fail"; }

                }catch(PDOException $e) {
                    die("Database error: " . $e->getMessage()); 
                }
            }
        }

        if($update_list[0]['empty'] === "no") //update가 있을 때 
        {
            foreach ($update_list as $value) {

                $uplan_name = $value['plan_name'];
                $uplan_order = $value['plan_order'];
                $uincome = $value['income'];
                $uis_complete = $value['is_complete'];
                $userver_idx = $value['server_idx'];

                try{
                    $stmt = $con->prepare('UPDATE plans SET userNo = :userNo, plan_name = :plan_name, plan_order = :plan_order, income = :income, is_complete = :is_complete
                        WHERE idx = :server_idx');
                    $stmt->bindParam(':userNo', $userNo);
                    $stmt->bindParam(':plan_name', $uplan_name);
                    $stmt->bindParam(':plan_order', $uplan_order);
                    $stmt->bindParam(':income', $uincome);
                    $stmt->bindParam(':is_complete', $uis_complete);
                    $stmt->bindParam(':server_idx', $userver_idx);
                    
                    //query 실행
                    if($stmt->execute()){
                        $usuccessMSG = "update success";
                        $cnt += 1;
                    }else{ $uerrMSG = "update fail"; }

                }catch(PDOException $e) {
                    die("Database error: " . $e->getMessage()); 
                }
            }
        }

        if($delete_list[0]['empty'] === "no") //delete가 있을 때
        {
            foreach ($delete_list as $value) {
                $server_idx = $value['server_idx'];
                try{
                    $stmt = $con->prepare('DELETE from plans WHERE idx = :server_idx');
                    $stmt->bindParam(':server_idx', $server_idx);

                    //query 실행
                    if($stmt->execute()){
                        $dsuccessMSG = "delete success";
                        $cnt += 1;
                    }else{ $derrMSG = "delete fail"; }

                }catch(PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
            }
        }



        $response0 = array("message", null);
        $response1 = array("cnt",(string)$cnt);

        $response = array($response0, $response1);

        echo json_encode($response);
    }
?>
