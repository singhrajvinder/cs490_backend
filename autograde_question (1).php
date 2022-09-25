<?php


$url = 'https://afsaccess4.njit.edu/~rs2264/viewexams.php';


$curl = curl_init($url);

$json = file_get_contents('php://input');

/*
$data_arr = array(
    'id'=>32
);
*/



// $encode_arr = json_encode($data_arr);


curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

$result = curl_exec($curl);
$response = json_decode($result,true);
print_r($result);
$Exam_id = $response['Exam_id'];
$Questions=$response['Question_ID']; // made change <-------
$Question_ID=json_decode($Questions, true); // made change <-------
$ques_ans=$response['Student_ans']; // made change <-------
$string = preg_replace("/\r|\n/", '\\n', $ques_ans); // \n problem 
$Student_ans=json_decode($string, true); // made change <-------
print_r("ans decode");

print_r($Question_ID);
print_r($Student_ans);
// echo $ques_ans;

$arrLength = count($Question_ID); // made change <-------
$result = array(); // made change <-------
for($i = 0; $i < $arrLength; $i++) { // made change <-------
    $temp = array_merge($Question_ID[$i], $Student_ans[$i]); // made change <-------
    array_push($result, $temp); // made change <-------
    //print_r($Student_ans[$i]);
}



$Test_case1 = "";
$Test_case1_output = "";
$Test_case2 = "";
$Test_case2_output = "";
$Test_case3 = "";
$Test_case3_output = "";
$Test_case4 = "";
$Test_case4_output = "";
$Test_case5 = "";
$Test_case5_output = "";
$p= 0;
$Constraint = "";
$std_ans = "";

curl_close($curl);
$data=array();

foreach($result as $id) { // made change <-------
    $url = 'https://afsaccess4.njit.edu/~rs2264/questionsearch.php';


    $curl = curl_init($url);

    $data2 = array(
        'Question_id'=> $id['Question_ID']
    );

    $data2_encode = json_encode($data2);

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data2_encode);// made change <-------
    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

    $result = curl_exec($curl);
    $response = json_decode($result, true);

    $Test_case1_output = $response['Test_case1_output']; // made change the quuatations where wrong <-------
    $Test_case2_output = $response['Test_case2_output']; // made change the quuatations where wrong <-------
    $Test_case3_output = $response['Test_case3_output']; 
    $Test_case4_output = $response['Test_case4_output']; 
    $Test_case5_output = $response['Test_case5_output']; 
    $Test_case1 = $response['Test_case1']; // made change the quuatations where wrong  <-------
    $Test_case2 = $response['Test_case2'];// made change the quuatations where wrong  <-------
    $Test_case3 = $response['Test_case3'];
    $Test_case4 = $response['Test_case4'];
    $Test_case5 = $response['Test_case5'];
    $Constraint = $response['Constraint'];
    $p = $id['points']; // made change <-------
    $std_ans = $id['solution']; // made change <-------


    //echo "test case 1 \n";
    //echo $Test_case1, "\n";
    //echo " \n";

    //echo "test case 1 output \n";
    //echo $Test_case1_output, "\n";
    //echo " \n";

    //echo "test case 2 \n";
    //echo $Test_case2, "\n";
    //echo " \n";

    ////echo "test case 2 output \n";
    ////echo $Test_case2_output, "\n";
    ////echo " \n";


    // grade question here

    $graded=array(
        'Question_id'=> $id['Question_ID'],
        'doesFunctionNameMatch'=> 0,
        'testCaseCount'=> 0,
        'testCase1Passed'=> 0,
        'testCase2Passed'=> 0,
        'testCase3Passed'=> 0,
        'testCase4Passed'=> 0,
        'testCase5Passed'=> 0,
        'constraintPassed'=> 0,
        'Grade'=> $p
    );

    //print_r($graded);
    //echo " \n";

    if($Test_case1 != NULL) $graded["testCaseCount"]++;
    if($Test_case2 != NULL) $graded["testCaseCount"]++; 
    if($Test_case3 != NULL) $graded["testCaseCount"]++; 
    if($Test_case4 != NULL) $graded["testCaseCount"]++; 
    if($Test_case5 != NULL) $graded["testCaseCount"]++;

    $points_per_testcase = 0.60/$graded["testCaseCount"];

    $func_name = get_string_between($std_ans, 'def ', '(');
    // create python script to execute
    $code_arch = "{{func_def}}\r\n\r\nprint({{func_name}}(params}}))\r\n\r\n";
    $base_script = str_replace("{{func_def}}", $std_ans, $code_arch);
    $base_script = str_replace("{{func_name}}", $func_name, $base_script);

    //echo "code architecture \n";
    //echo $code_arch, "\n";
    //echo " \n";

    //echo "base script \n";
    //echo $base_script, "\n";
    //echo " \n";


    $testscript1 = "testcase1";
    $testscript2 = "testcase2";
    $testscript3 = "testcase3";
    $testscript4 = "testcase4";
    $testscript5 = "testcase5";

    // check if function name matches
    // if it does change 'doesFunctionNameMatch' to true
    // if it does not change function name throughout script in case of recurrsion
    // and set 'doesFunctionNameMatch to false, remember to deduct 5 points later
    $correct_func_name = explode("(", $Test_case1, 2)[0];

    if($Constraint != NULL){
        if($func_name == $correct_func_name) {
            $graded["doesFunctionNameMatch"] = $graded['doesFunctionNameMatch'] + $p*0.10;
        }else {
            $base_script = str_replace($func_name, $correct_func_name, $base_script);
            $graded["Grade"] = $graded["Grade"] - $p*0.10;
        }
    }else {
        if($func_name == $correct_func_name) {
            $graded["doesFunctionNameMatch"] = $graded['doesFunctionNameMatch'] + $p*0.40;
        }else {
            $base_script = str_replace($func_name, $correct_func_name, $base_script);
            $graded["Grade"] = $graded["Grade"] - $p*0.40;
        }
    }

    $const_search = strpos($std_ans, $Constraint);
    if($const_search == true){
        $graded["constraintPassed"] = $graded["constraintPassed"] + $p*0.30;

    }else {
        $graded["Grade"] = $graded["Grade"] - $p*0.30;
    }


//--------------------------------------------TEST CASE 1--------------------------------------------------------------
    // test case 1 do the same for all test cases
    // open file where python script will be saved
    $py_ans_file = "$testscript1.py";
    //$chmod1 = shell_exec('chmod 777 testcase1.py');
    $open_file = fopen($py_ans_file, 'w') or die("File " . $py_ans_file . "cannot be opened");

    //echo "file name \n";
    //echo $py_ans_file, "\n";
    //echo " \n";


    // insert parameters for test case 1
    $params = get_string_between($Test_case1, '(', ')');
    //echo "parameters from testcase1 \n";
    //echo $params, "\n";
    //echo " \n";
    $script_params = str_replace("params}}", $params, $base_script);

    //echo "parameters for test case 1 \n";
    //echo $script_params, "\n";
    //echo " \n";


    $tc1_std_ans = $script_params;
    $final_script = $tc1_std_ans;

    fwrite($open_file, $final_script);

    // run script and store output into $output
    $cmd = "timeout 1 python $py_ans_file 2>&1";
    $output1 = exec($cmd);

    //echo "output from python \n";
    //echo $output1, "\n";
    //echo " \n";

    // check if the expected output from the test case has quotation marks
    // if it does remove them from the beginning and end of the output
    // this is needed because the output from the script will not have quotes
    // and we need to check if the test case output and script output match
    $expec_output1 = $Test_case1_output;

    //echo "expected output from testcase1 \n";
    //echo $expec_output1, "\n";
    //echo " \n";

    if($expec_output1[0] == "\"") {
        $expec_output1 = substr($expec_output1, 1);
    }
    if($expec_output1[strlen($expec_output1) - 1] == "\"") {
        $expec_output1 = substr($expec_output1, 0, -1);
    }

    if($expec_output1 == $output1) {
        $expec_output1 . "<br/><br/>";
        $graded["testCase1Passed"] = $graded["testCase1Passed"] + $p*$points_per_testcase;
        $points_testcase1 = $p*$points_per_testcase;
    }else {
        $expec_output1 . "br/><br/>";
        $graded["testCase1Passed"] = 0;
        $graded["Grade"] = $graded["Grade"] - $p*$points_per_testcase;
        $points_testcase1 = 0;
    }

    fclose($open_file);
    exec("rm $testscript1.py");


//--------------------------------------------TEST CASE 2--------------------------------------------------------------
    // open file where python script will be saved
    $py_ans_file2 = "$testscript2.py";
    $open_file2 = fopen($py_ans_file2, 'w') or die("File " . $py_ans_file2 . "cannot be opened");

    //echo "file name \n";
    //echo $py_ans_file2, "\n";
    //echo " \n";


    // insert parameters for test case 2
    $params2 = get_string_between($Test_case2, '(', ')');

    //echo "parameters from testcase2 \n";
    //echo $params2, "\n";
    //echo " \n";

    $script_params2 = str_replace("params}}", $params2, $base_script);

    //echo "parameters for test case 2 \n";
    //echo $script_params2, "\n";
    //echo " \n";


    $tc2_std_ans = $script_params2;
    $final_script2 = $tc2_std_ans;

    fwrite($open_file2, $final_script2);

    // run script and store output into $output
    $cmd2 = "timeout 1 python $py_ans_file2 2>&1";
    $output2 = exec($cmd2);

    //echo "output from python \n";
    //echo $output2, "\n";
    //echo " \n";


    // check if the expected output from the test case has quotation marks
    // if it does remove them from the beginning and end of the output
    // this is needed because the output from the script will not have quotes
    // and we need to check if the test case output and script output match
    $expec_output2 = $Test_case2_output;

    //echo "expected output from testcase2 \n";
    //echo $expec_output2, "\n";
    //echo " \n";

    if($expec_output2[0] == "\"") {
        $expec_output2 = substr($expec_output2, 1);
    }
    if($expec_output2[strlen($expec_output2) - 1] == "\"") {
        $expec_output2 = substr($expec_output2, 0, -1);
    }

    if($expec_output2 == $output2) {
        $expec_output2 . "<br/><br/>";
        $graded["testCase2Passed"] = $graded["testCase2Passed"] + $p*$points_per_testcase;
        $points_testcase2 = $p*$points_per_testcase;
    }else {
        $expec_output2 . "br/><br/>";
        $graded["testCase2Passed"] = 0;
        $graded["Grade"] = $graded["Grade"] - $p*$points_per_testcase;
        $points_testcase2 = 0;
    }

    fclose($open_file2);
    exec("rm $testscript2.py");

//--------------------------------------------TEST CASE 3--------------------------------------------------------------

    if($Test_case3 != NULL) {

        // open file where python script will be saved
        $py_ans_file3 = "$testscript3.py";
        $open_file3 = fopen($py_ans_file3, 'w') or die("File " . $py_ans_file3 . "cannot be opened");

        //echo "file name \n";
        //echo $py_ans_file3, "\n";
        //echo " \n";


        // insert parameters for test case 3
        $params3 = get_string_between($Test_case3, '(', ')');

        //echo "parameters from testcase3 \n";
        //echo $params3, "\n";
        //echo " \n";

        $script_params3 = str_replace("params}}", $params3, $base_script);

        //echo "parameters for test case 3 \n";
        //echo $script_params3, "\n";
        //echo " \n";


        $tc3_std_ans = $script_params3;
        $final_script3 = $tc3_std_ans;

        fwrite($open_file3, $final_script3);

        // run script and store output into $output
        $cmd3 = "timeout 1 python $py_ans_file3 2>&1";
        $output3 = exec($cmd3);

        //echo "output from python \n";
        //echo $output3, "\n";
        //echo " \n";


        // check if the expected output from the test case has quotation marks
        // if it does remove them from the beginning and end of the output
        // this is needed because the output from the script will not have quotes
        // and we need to check if the test case output and script output match
        $expec_output3 = $Test_case3_output;

        //echo "expected output from testcase3 \n";
        //echo $expec_output3, "\n";
        //echo " \n";

        if($expec_output3[0] == "\"") {
            $expec_output3 = substr($expec_output3, 1);
        }
        if($expec_output3[strlen($expec_output3) - 1] == "\"") {
            $expec_output3 = substr($expec_output3, 0, -1);
        }

        if($expec_output3 == $output3) {
            $expec_output3 . "<br/><br/>";
            $graded["testCase3Passed"] = $graded["testCase3Passed"] + $p*$points_per_testcase;
            $points_testcase3 = $p*$points_per_testcase;
        }else {
            $expec_output3 . "br/><br/>";
            $graded["testCase3Passed"] = 0;
            $graded["Grade"] = $graded["Grade"] - $p*$points_per_testcase;
            $points_testcase3 = 0;
        }

        fclose($open_file3);
        exec("rm $testscript3.py");

    }else{
        $graded["testCase3Passed"] == NULL;
    }

//--------------------------------------------TEST CASE 4--------------------------------------------------------------

if($Test_case4 != NULL) {

    // open file where python script will be saved
    $py_ans_file4 = "$testscript4.py";
    $open_file4 = fopen($py_ans_file4, 'w') or die("File " . $py_ans_file4 . "cannot be opened");

    //echo "file name \n";
    //echo $py_ans_file4, "\n";
    //echo " \n";


    // insert parameters for test case 4
    $params4 = get_string_between($Test_case4, '(', ')');

    //echo "parameters from testcase4 \n";
    //echo $params4, "\n";
    //echo " \n";

    $script_params4 = str_replace("params}}", $params4, $base_script);

    //echo "parameters for test case 4 \n";
    //echo $script_params4, "\n";
    //echo " \n";


    $tc4_std_ans = $script_params4;
    $final_script4 = $tc4_std_ans;

    fwrite($open_file4, $final_script4);

    // run script and store output into $output
    $cmd4 = "timeout 1 python $py_ans_file4 2>&1";
    $output4 = exec($cmd4);

    //echo "output from python \n";
    //echo $output4, "\n";
    //echo " \n";


    // check if the expected output from the test case has quotation marks
    // if it does remove them from the beginning and end of the output
    // this is needed because the output from the script will not have quotes
    // and we need to check if the test case output and script output match
    $expec_output4 = $Test_case4_output;

    //echo "expected output from testcase4 \n";
    //echo $expec_output4, "\n";
    //echo " \n";

    if($expec_output4[0] == "\"") {
        $expec_output4 = substr($expec_output4, 1);
    }
    if($expec_output4[strlen($expec_output4) - 1] == "\"") {
        $expec_output4 = substr($expec_output4, 0, -1);
    }

    if($expec_output4 == $output4) {
        $expec_output4 . "<br/><br/>";
        $graded["testCase4Passed"] = $graded["testCase4Passed"] + $p*$points_per_testcase;
        $points_testcase4 = $p*$points_per_testcase;
    }else {
        $expec_output4 . "br/><br/>";
        $graded["testCase4Passed"] = 0;
        $graded["Grade"] = $graded["Grade"] - $p*$points_per_testcase;
        $points_testcase4 = 0;
    }

    fclose($open_file4);
    exec("rm $testscript4.py");

}else{
    $graded["testCase4Passed"] == NULL;
}

//--------------------------------------------TEST CASE 5--------------------------------------------------------------

if($Test_case5 != NULL) {

    // open file where python script will be saved
    $py_ans_file5 = "$testscript5.py";
    $open_file5 = fopen($py_ans_file5, 'w') or die("File " . $py_ans_file5 . "cannot be opened");

    //echo "file name \n";
    //echo $py_ans_file5, "\n";
    //echo " \n";


    // insert parameters for test case 5
    $params5 = get_string_between($Test_case5, '(', ')');

    //echo "parameters from testcase5 \n";
    //echo $params5, "\n";
    //echo " \n";

    $script_params5 = str_replace("params}}", $params5, $base_script);

    //echo "parameters for test case 5 \n";
    //echo $script_params5, "\n";
    //echo " \n";


    $tc5_std_ans = $script_params5;
    $final_script5 = $tc5_std_ans;

    fwrite($open_file5, $final_script5);

    // run script and store output into $output
    $cmd5 = "timeout 1 python $py_ans_file5 2>&1";
    $output5 = exec($cmd5);

    //echo "output from python \n";
    //echo $output5, "\n";
    //echo " \n";


    // check if the expected output from the test case has quotation marks
    // if it does remove them from the beginning and end of the output
    // this is needed because the output from the script will not have quotes
    // and we need to check if the test case output and script output match
    $expec_output5 = $Test_case5_output;

    //echo "expected output from testcase5 \n";
    //echo $expec_output5, "\n";
    //echo " \n";

    if($expec_output5[0] == "\"") {
        $expec_output5 = substr($expec_output5, 1);
    }
    if($expec_output5[strlen($expec_output5) - 1] == "\"") {
        $expec_output5 = substr($expec_output5, 0, -1);
    }

    if($expec_output5 == $output5) {
        $expec_output5 . "<br/><br/>";
        $graded["testCase5Passed"] = $graded["testCase5Passed"] + $p*$points_per_testcase;
        $points_testcase5 = $p*$points_per_testcase;
    }else {
        $expec_output5 . "br/><br/>";
        $graded["testCase5Passed"] = 0;
        $graded["Grade"] = $graded["Grade"] - $p*$points_per_testcase;
        $points_testcase5 = 0;
    }

    fclose($open_file5);
    exec("rm $testscript5.py");

}else{
    $graded["testCase5Passed"] == NULL;
}

//--------------------------------------------END OF TEST CASES--------------------------------------------------------
    //print_r($graded);

    array_push($data,$graded);
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$url = 'https://afsaccess4.njit.edu/~rs2264/addGrades.php';


$curl = curl_init($url);
$json_string = json_encode($data);
$send = array(
    'id' => $Exam_id,
    'Grade' => $json_string
);



$json_send = json_encode($send);
echo $json_string;

curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );
curl_setopt($curl, CURLOPT_POSTFIELDS, $json_send);

$result = curl_exec($curl);
// $response = json_encode($result);
// echo $response;

$status = array(
    'status'=> true,
    'error'=> $result
);

$encode_status = json_encode($status);
// echo $encode_status;

    // insert parameters
    //$get_params = get_string_between($std_ans, 'def ', ':');
    //$params = get_string_between($get_params, '(', ')');
    //$script_params = str_replace("{{params}}", $params, $base_script);

    // random string is generated for file name
    // so different questions don't try to access same file
    // $testscript = substr(md5(microtime(), rand(0, 26), 5));
?>

