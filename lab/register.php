 <?php 

 #allow registration
 $reg_closed = true;

#check if we are running on AppFog with a MySQL service
$services_json = json_decode(getenv("VCAP_SERVICES"),true);
if(!$services_json) {
	#connect to local Database
	mysql_connect("127.0.0.1", "root", "toor") or die(mysql_error());
	mysql_select_db("securelab") or die(mysql_error());
} else {
	#connect to AppFog MYSQL DB
	$mysql_config = $services_json["mysql-5.1"][0]["credentials"];
	$username = $mysql_config["username"];
	$password = $mysql_config["password"];
	$hostname = $mysql_config["hostname"];
	$port = $mysql_config["port"];
	$db = $mysql_config["name"];
	$link = mysql_connect("$hostname:$port", $username, $password);
	$db_selected = mysql_select_db($db, $link);
}

$result=mysql_query("SELECT * FROM users");
if (!$result) {
	mysql_query("CREATE TABLE users (ID MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY, username VARCHAR(60), password VARCHAR(60))");
}

 #mysql_connect("127.0.0.1", "root", "toor") or die(mysql_error()); 
 #mysql_select_db("securelab") or die(mysql_error()); 
 
 //This code runs if the form has been submitted
 if (isset($_POST['submit'])) 
 { 
  //This makes sure they did not leave any fields blank
  if (!$_POST['username'] | !$_POST['pass'] | !$_POST['pass2'] ) 
  {
 	die('You did not complete all of the required fields');
  }
  // checks if the username is in use
  if (!get_magic_quotes_gpc()) 
  {
   $_POST['username'] = addslashes($_POST['username']);
  }

  $usercheck = $_POST['username'];
  $check = mysql_query("SELECT username FROM users WHERE username = '$usercheck'") or die(mysql_error());
  $check2 = mysql_num_rows($check);

  //if the name exists it gives an error

  if ($check2 != 0) 
  {
   die('Sorry, the username '.$_POST['username'].' is already in use.');
  }
  // this makes sure both passwords entered match
  if ($_POST['pass'] != $_POST['pass2']) 
  {
   die('Your passwords did not match. ');
  }
  // here we encrypt the password and add slashes if needed
  $_POST['pass'] =(md5($_POST['pass']));
  if (!get_magic_quotes_gpc()) 
  {
   $_POST['pass'] = addslashes($_POST['pass']);
   $_POST['username'] = addslashes($_POST['username']);
  }

  // now we insert it into the database
  if(!$reg_closed)
  {
    $insert = "INSERT INTO users (username, password) VALUES ('".$_POST['username']."', '".$_POST['pass']."')";
    $add_member = mysql_query($insert);
  }
  else
  {
    die("Sorry Registration is now closed... that's too bad. this site is really really cool!");
  }
?>
 <h1>Registered</h1>

 <p>Thank you, you have registered - you may now <a href=login.php>login</a>.</p>
<?php 
 } 

 else 
 {	
 ?>
 <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
 <table border="0">
 <tr><td>Username:</td><td>
 <input type="text" name="username" maxlength="60">
 </td></tr>
 <tr><td>Password:</td><td>
 <input type="password" name="pass" maxlength="100">
 </td></tr>
 <tr><td>Confirm Password:</td><td>
 <input type="password" name="pass2" maxlength="100">
 </td></tr>
 <tr><th colspan=2><input type="submit" name="submit" 
value="Register"></th></tr> </table>
 </form>
 <a href=login.php>LOGIN</a>
 <?php
 }
 ?> 
