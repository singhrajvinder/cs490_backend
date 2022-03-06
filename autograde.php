<?php



$url = 'https://afsaccess4.njit.edu/~rs2264/viewexams.php';


$curl = curl_init($url);

$json = file_get_contents('php://input');


curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

$result = curl_exec($curl);
$response = json_decode($result,true);
$Exam_id = $response['Exam_id'];
$Questions=$response['Question_ID']; // made change <-------
$Question_ID=json_decode($Questions, true); // made change <-------
$ques_ans=$response['Student_ans']; // made change <-------
$Student_ans=json_decode($ques_ans, true); // made change <-------

$arrLength = count($Question_ID); // made change <-------
$result = array(); // made change <-------
for($i = 0; $i < $arrLength; $i++) { // made change <-------
    $temp = array_merge($Question_ID[$i], $Student_ans[$i]); // made change <-------
    array_push($result, $temp); // made change <-------
}
$test_open = fopen('addGrades.php','w');
$Test_case1 = "";
$Test_case1_output = "";
$Test_case2 = "";
$Test_case2_output = "";
$p= 0;
$std_ans = "";

curl_close($curl);
$data=array();

foreach($result as $id) { // made change <-------
    $url = 'https://afsaccess4.njit.edu/~rs2264/Questionsearch.php';


    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $id['Question_ID']);// made change <-------
    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );

    $result = curl_exec($curl);
    $response = json_decode($result);

    $Test_case1_output = $response['Test_case1_output']; // made change the quuatations where wrong <-------
    $Test_case2_output = $response['Test_case2_output']; // made change the quuatations where wrong <------- 
    $est_case1 = $response['Test_case1']; // made change the quuatations where wrong  <-------
    $Test_case2 = $response['Test_case2'];// made change the quuatations where wrong  <-------
    $p = $id['points']; // made change <-------
    $std_ans = $id['solution']; // made change <-------

    // grade question here

    $graded=array(
        'Question_ID' => $id['Question_ID'],
        'doesFunctionNameMatch'=> 0,
        'testCase1Passed'=> 0,
        'testCase2Passed'=> 0,
        'testCaseCount'=> 0,
        'passedTestCases'=> 0, 
        'questionScore'=> 25
    );

    $func_name = get_string_between($std_ans, 'def ', '(');
    // create python script to execute
    $code_arch = "{{func_def}}\r\n\r\nprint({{func_name}}(params}}))\r\n\r\n";
    $base_script = str_replace("{{func_def}}", $std_ans, $code_arch);
    $base_script = str_replace("{{func_name}}", $func_name, $base_script);

    // insert parameters
    //$get_params = get_string_between($std_ans, 'def ', ':');
    //$params = get_string_between($get_params, '(', ')');
    //$script_params = str_replace("{{params}}", $params, $base_script);

    // random string is generated for file name
    // so different questions don't try to access same file
    // $testscript = substr(md5(microtime(), rand(0, 26), 5));

    $testscript1 = "testcase1";
    $testscript2 = "testcase2";

    // check if function name matches 
    // if it does change 'doesFunctionNameMatch' to true 
    // if it does not change function name throughout script in case of recurrsion
    // and set 'doesFunctionNameMatch to false, remember to deduct 5 points later
    $correct_func_name = explode("(", $test_case1, 2)[0];

    if($func_name == $correct_func_name) {
        $graded["doesFunctionNameMatch"] = 1;
    }else {
        $base_script = str_replace($func_name, $correct_func_name, $base_script);
        $graded["questionScore"] = $graded["questionScore"] - 5;
    }

    // test case 1 do the same for test case 2
    // open file where python script will be saved 
    $py_ans_file = "$testscript1.py";
    $open_file = fopen('testcase1.py', 'w');


    // insert parameters for test case 1
    $params = get_string_between($test_case1, '(', ')');
    $script_params = str_replace("params}}", $params, $base_script);

    $tc1_std_ans = $script_params;
    $final_script = $tc1_std_ans;

    fwrite($open_file, $final_script);

    // run script and store output into $output
    $cmd = "timeout 1 python $py_ans_file 2>&1";
    $output1 = exec($cmd);
    // check if the expected output from the test case has quotation marks
    // if it does remove them from the beginning and end of the output
    // this is needed because the output from the script will not have quotes 
    // and we need to check if the test case output and script output match
    $expec_output1 = $Test_case1_output;
    if($expec_output1[0] == "\"") {
        $expec_output1 = substr($expec_output1, 1);
    }
    if($expec_output1[strlen($expec_output1) - 1] == "\"") {
        $expec_output1 = substr($expec_output1, 0, -1);
    }

    if($expec_output1 == $output1) {
        $expec_output1 . "<br/><br/>";
        $graded["passedTestCases"]++;
        $graded["testCaseCount"]++;
        $graded["testCase1Passed"] = 1;
        $points_testcase1 = 10;
    }else {
        $expec_output1 . "br/><br/>";
        $graded["testCaseCount"]++;
        $graded["testCase1Passed"] = 0;
        $graded["questionScore"] = $graded["questionScore"] - 10;
        $points_testcase1 = 0;
    }

    fclose($open_file);
    exec("rm testcase1.py");

    // test case 2
    // open file where python script will be saved 
    $py_ans_file2 = "$testscript2.py";
    $open_file2 = fopen('testcase2.py', 'w');
    
    
    // insert parameters for test case 2
    $params2 = get_string_between($test_case2, '(', ')');
    $script_params2 = str_replace("params}}", $params2, $base_script);
    
    $tc2_std_ans = $script_params2;
    $final_script2 = $tc2_std_ans;
    
    fwrite($open_file2, $final_script2);
    
    // run script and store output into $output
    $cmd2 = "timeout 1 python $py_ans_file2 2>&1";
    $output2 = exec($cmd2);
    
    // check if the expected output from the test case has quotation marks
    // if it does remove them from the beginning and end of the output
    // this is needed because the output from the script will not have quotes 
    // and we need to check if the test case output and script output match
    $expec_output2 = $Test_case2_output;
    if($expec_output2[0] == "\"") {
        $expec_output2 = substr($expec_output2, 1);
    }
    if($expec_output2[strlen($expec_output2) - 1] == "\"") {
        $expec_output2 = substr($expec_output2, 0, -1);
    }
    
    if($expec_output2 == $output2) {
        $expec_output2 . "<br/><br/>";
        $graded["passedTestCases"]++;
        $graded["testCaseCount"]++;
        $graded["testCase2Passed"] = 1;
        $points_testcase2 = 10;
    }else {
        $expec_output2 . "br/><br/>";
        $graded["testCaseCount"]++;
        $graded["testCase2Passed"] = 0;
        $graded["questionScore"] = $graded["questionScore"] - 10;
        $points_testcase2 = 0;
    }

    fclose($open_file2);
    exec("rm testcase2.py");

    
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
?>
