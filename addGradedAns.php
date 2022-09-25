<?php
    include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array
	$id=$response['id'];
    $Graded_ans=$response['Graded_ans'];
	$query="update `Exam` set `Outputs`='$Graded_ans' WHERE `Exam_id` = '$id'";
	$result = mysqli_query ($database,$query);

	if($result)	{
		echo "ExamTable";
		echo $id;
		echo "has added student Answer successfuly";
	}
	else {
		echo "ExamTable";
		echo $id;
        echo " was not added";
	}
    mysqli_close($database);
?>
