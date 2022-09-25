<?php
    include "dblog.php";
    $str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array
    echo $response;
	$id=$response['id'];
    $Grade=$response['Grade'];

	$query="update `Exam` set `Grade`='$Grade' WHERE `Exam_id` = '$id'";
	$result = mysqli_query ($database,$query);

	if($result)	{
		echo "ExamTable ";
		echo $id;
		echo " has added student Answer successfuly";
	}
	else {
		echo "ExamTable";
		echo $id;
        echo " was not added";
	}
?>
