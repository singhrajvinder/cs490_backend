<?php
	include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	$sql="select * from Exam where `status` = '2'";//only tables selected by instructor can be chosen
	$query = mysqli_query ($database,$sql);
	$result = mysqli_fetch_all($query,MYSQLI_ASSOC);

	$data=array();
	foreach ($result as $row){
		$temp=array(
			'Exam_id'=>$row['Exam_id'],
			'Exam_name'=> $row['Exam_name'],
			'Question_ID'=>$row['Question_ID']
		);
		array_push($data,$temp);
	}
	echo json_encode($data);
    mysqli_close($database);
?>
