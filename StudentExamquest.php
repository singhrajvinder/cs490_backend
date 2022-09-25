<?php
	include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	$Question_id = $response['Question_id'];

	$sql="select * from Questions where `Question_id` = '$Question_id'";
	$query = mysqli_query ($database,$sql);
	$result = mysqli_fetch_all($query,MYSQLI_ASSOC);

	$data=array();
	foreach ($result as $row){
		$temp=array(
			'Question_id'=> $row['Question_id'],
			'Question'=>$row['Question'],
			'Question_type'=>$row['Question_type'],
			'Difficulty'=>$row['Difficulty']
		);
		array_push($data,$temp);
	}
	echo json_encode($data);
    mysqli_close($database);
?>
