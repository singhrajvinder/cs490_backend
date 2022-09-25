<?php
   //database connection
   $server = 'sql1.njit.edu';
   $dbuser = '******';
   $pass = '*************';
   $dbname = 'rs2264';
   $database = mysqli_connect($server, $dbuser, $pass, $dbname);
   if(mysqli_connect_errno()){
   	die("connection error".mysqli_connect_error());
	}
?>
