<?php
   include "dblog.php";
 $str_json = file_get_contents('php://input');
 $response = json_decode($str_json, true); // decoding received JSON to array

 if(isset($response['name']))$name = $response['name'];
 if(isset($response['Question_ID'])) $Question_ID = $response['Question_ID'];

 $sql="insert into `Exam` (`Exam_name`, `Question_ID`, `status`) VALUES ('$name', '$Question_ID', 1)";
 $query = mysqli_query ($database,$sql);

 if($query){
   $temp=array(
     'Exam_id'=>mysqli_insert_id($database),
     'message'=>"Add Exam Table is done successfuly"
   );
   echo json_encode($temp);
 }
   else echo "Add Exam Table is failed because". mysqli_error($database);
   mysqli_close($database);
?>
