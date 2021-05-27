<?php 

    //error_reporting(E_ALL); 
    //ini_set('display_errors', 1); 

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

        $stmt = $con->prepare('SELECT * FROM users order by totalMoney desc limit 15');

	//query 실행
	$stmt->execute();
	$response = array();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		$data = array();
		$data['idx'] = $row['idx'];
		$data['nickname'] = $row['nickname'];
		$data['totalMoney'] = $row['totalMoney'];
		$data['userID'] = $row['userID'];
		$inmoney = 0;
		$stmtr = $con->prepare('SELECT * FROM money WHERE (userNo = :idx AND typeFlag = 1) AND (userNo != typeNo)');
		$stmtr->bindParam(':idx', $row['idx']);
		$stmtr->execute();
		while($rowr = $stmtr->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)){
			$inmoney += $rowr['typeMoney'];
		}
		$data['inmoney'] = $inmoney; 
		array_push($response, $data);
	}

        echo json_encode($response);

    }

?>
