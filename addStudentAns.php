<?php
    include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array
	$id=$response['id'];
    $Student_ans=$response['Student_ans'];
	$query="update `Exam` set `status`='3' ,`Student_ans`='$Student_ans' WHERE `Exam_id` = '$id'";
	$result = mysqli_query ($database,$query);

	if($result)	{
		echo "ExamTable";
		echo $id;
		echo "has added student Answer successfuly";
	}
	else {
		echo "ExamTable";
		echo $id;
        echo " was not added beacuse ". mysqli_error($database);
	}
    mysqli_close($database);
?>
