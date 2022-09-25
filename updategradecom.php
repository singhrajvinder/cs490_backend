<?php
    include "dblog.php";
    $str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	$id=$response['id'];
    $Grade=$response['Grade'];
    $instructor_comment=$response['instructor_comment'];

    $query="";

    if(isset($response['Grade']) && isset($response['instructor_comment'])){
        $query="update `Exam` set `status`='4', `instructor_grade`='$Grade', `instructor_comment`='$instructor_comment'  WHERE `Exam_id` = '$id'";
    }
    else if(isset($response['Grade'])){
        $query="update `Exam` set `status`='4', `instructor_grade`='$Grade' WHERE `Exam_id` = '$id'";
    }
    else if(isset($response['instructor_comment'])){
        $query="update `Exam` set `status`='4', `instructor_comment`='$instructor_comment' WHERE `Exam_id` = '$id'";
    }
	echo $query;
	$result = mysqli_query ($database,$query);

	if($result)	{
		echo "ExamTable";
		echo $id;
		echo "has added student Answer successfuly";
	}
	else {
		echo "ExamTable";
		echo $id;
        echo "was not added";
	}
    mysqli_close($database);
?>
