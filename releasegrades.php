<?php
	include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	$id=$response['id'];

	$query="update `Exam` set `status`='5' where `Exam_id` = '$id'";
	$result = mysqli_query ($database,$query);

	if($result)	{
		echo "ExamTable";
		echo $id;
		echo "has been released successfuly";
	}
	else {
		echo "ExamTable";
		echo $id;
		echo " was not released";
	}
    mysqli_close($database);
?>
