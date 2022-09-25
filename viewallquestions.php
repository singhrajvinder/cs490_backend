<?php
	include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	$sql="select * from Questions";
	$query = mysqli_query ($database,$sql);
	$result=mysqli_fetch_all($query,MYSQLI_ASSOC);

	$data=array();
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
	mysqli_free_result($query);
    mysqli_close($database);
?>
