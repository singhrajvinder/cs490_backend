<?php
	include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	 if(isset($response['Difficulty'])) $Difficulty = $response['Difficulty'];
	 if(isset($response['Question_type'])) $Question_type = $response['Question_type'];
	 if(isset($response['Keyword'])) $Keyword = $response['Keyword'];

	 if(isset($response['Difficulty']) && isset($response['Question_type']) && isset($response['Keyword'])){
		 $sql="SELECT * FROM `Questions` WHERE `Difficulty` = '$Difficulty' AND `Question_type` = '$Question_type' AND `Question` LIKE '%$Keyword%'";
	 }
	 else if(isset($response['Difficulty']) && isset($response['Question_type'])){
		 $sql="SELECT * FROM `Questions` WHERE `Difficulty` = '$Difficulty' AND `Question_type` = '$Question_type'";
	 }
	 else if(isset($response['Difficulty']) && isset($response['Keyword'])){
		 $sql="SELECT * FROM `Questions` WHERE `Difficulty` = '$Difficulty' AND `Question` LIKE '%$keyword%'";
	 }
	 else if(isset($response['Question_type']) && isset($response['Keyword'])){
		 $sql="SELECT * FROM `Questions` WHERE `Question_type` = '$Question_type' AND `Question` LIKE '%$keyword%'";
	 }
	 else if(isset($response['Difficulty'])){
		 $sql="SELECT * FROM `Questions` WHERE `Difficulty` = '$Difficulty'";
	 }
	 else if(isset($response['Question_type'])){
		 $sql="SELECT * FROM `Questions` WHERE `Difficulty` = '$Difficulty' AND `Question_type` = '$Question_type' AND `Question` LIKE '%$keyword%'";
	 }
	 else if(isset($response['Keyword'])){
		 $sql="SELECT * FROM `Questions` WHERE `Question` LIKE '%$keyword%'";
	 }

	print_r($sql);
	$query = mysqli_query ($database,$sql);
	$result=mysqli_fetch_all($query,MYSQLI_ASSOC);

	$data=[];
	foreach ($result as $row){
		$temp=array(
			'Question_id'=> $row['Question_id'],
			'Question'=>$row['Question'],
			'Username'=>$row['Username'],
			'Question_type'=>$row['Question_type'],
			'Difficulty'=>$row['Difficulty'],
			'Test_case1_output'=>$row['Test_case1_output'],
			'Test_case2_output'=>$row['Test_case2_output'],
			'Test_case3_output'=>$row['Test_case3_output'],
			'Test_case4_output'=>$row['Test_case4_output'],
			'Test_case5_output'=>$row['Test_case5_output'],
			'Test_case1'=>$row['Test_case1'],
			'Test_case2'=>$row['Test_case2'],
			'Test_case3'=>$row['Test_case3'],
			'Test_case4'=>$row['Test_case4'],
			'Test_case5'=>$row['Test_case5'],
			'Constraint'=>$row['Constraint']
		);
		array_push($data,$temp);
	}
	echo json_encode($data);
    mysqli_close($database);
?>
