<?php
	include "dblog.php";
	$str_json = file_get_contents('php://input');
	$response = json_decode($str_json, true); // decoding received JSON to array

	if(isset($response['Question'])) $Question = $response['Question'];
	if(isset($response['Constraint'])) $Constraint = $response['Constraint'];
	if(isset($response['Username'])) $Username = $response['Username'];
	if(isset($response['Question_type'])) $Question_type = $response['Question_type'];
	if(isset($response['Difficulty'])) $Difficulty = $response['Difficulty'];
	if(isset($response['Test_case1'])) $Test_case1 = $response['Test_case1'];
	if(isset($response['Test_case2'])) $Test_case2 = $response['Test_case2'];
	if(isset($response['Test_case3'])) $Test_case3 = $response['Test_case3'];
	if(isset($response['Test_case4'])) $Test_case4 = $response['Test_case4'];
	if(isset($response['Test_case5'])) $Test_case5 = $response['Test_case5'];
	if(isset($response['Test_case1_output'])) $Test_case1_output = $response['Test_case1_output'];
	if(isset($response['Test_case2_output'])) $Test_case2_output = $response['Test_case2_output'];
	if(isset($response['Test_case3_output'])) $Test_case3_output = $response['Test_case3_output'];
	if(isset($response['Test_case4_output'])) $Test_case4_output = $response['Test_case4_output'];
	if(isset($response['Test_case5_output'])) $Test_case5_output = $response['Test_case5_output'];

	$sql="INSERT INTO `Questions`(`Question`, `Test_case1_output`, `Question_type`, `Difficulty`, `Test_case1`, `Username`, `Test_case2_output`, `Test_case2`, `Test_case3`, `Test_case4`, `Test_case5`, `Test_case3_output`, `Test_case4_output`, `Test_case5_output`, `Constraint`) VALUES ('$Question', '$Test_case1_output', '$Question_type', '$Difficulty', '$Test_case1', '$Username', '$Test_case2_output', '$Test_case2', '$Test_case3', '$Test_case4', '$Test_case5', '$Test_case3_output', '$Test_case4_output', '$Test_case5_output', '$Constraint')";
	$query = mysqli_query ($database,$sql);
	print_r($sql);

	if($query)	echo "Add question is done successfuly";
		else echo "Notice: add question is failed becasue".mysqli_error($database);
    mysqli_close($database);
?>
