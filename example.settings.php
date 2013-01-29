<?php
//$host = $_SERVER['HTTP_HOST'];
$host = "http://localhost/Webs/setlistr/";
//$host = "http://www.setlistr.co.uk/";

//Database Connection details
$db_host		= 'hostname';
$db_user		= 'username';
$db_pass		= 'password';
$db_database	= 'database name';

//Site Salt is used for increased password security of your users
$site_salt= 'put your ownlong random string here'; 

//Site email address
$site_email = "admin@setlistr.co.uk";

//Timezone
date_default_timezone_set('GMT');

//Default api list to show on api/show.php
$api_default_example_list_id = 292;

//Google analytics code
$google_analytics_code = "your code here";
?>
