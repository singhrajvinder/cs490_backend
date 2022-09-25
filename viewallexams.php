<?php
	include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	$sql="select * from Exam";
	$query = mysqli_query ($database,$sql);
	$result = mysqli_fetch_all($query,MYSQLI_ASSOC);

	$data=array();
	foreach ($result as $row){
		$temp=array(
			'Exam_id'=>$row['Exam_id'],
			'Exam_name'=> $row['Exam_name'],
			'Question_ID'=>$row['Question_ID'],
			'Student_ans'=>$row['Student_ans'],
			'Grade'=>$row['Grade'],
			'instructor_grade'=>$row['instructor_grade'],
			'instructor_comment'=>$row['instructor_comment'],
			'Outputs'=>$row['Outputs'],
			'status'=>$row['status']
		);
		array_push($data,$temp);
	}
	echo json_encode($data);
	mysqli_free_result($query);
    mysqli_close($database);
?>
