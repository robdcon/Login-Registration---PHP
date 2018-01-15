<?php
session_start();

// Database creation details are in query folder

include 'config.php';
	
// Variables for POST data from login.php
$usr = (isset($_POST['usr']) ? $_POST['usr'] : null);
$psw = (isset($_POST['psw']) ? $_POST['psw'] : null);
$first = (isset($_POST['first']) ? $_POST['first'] : null);
$last = (isset($_POST['last']) ? $_POST['last'] : null);
$email = (isset($_POST['email']) ? $_POST['email'] : null);

// Prepare statement with username placeholder
$regStmnt = $pdo->prepare("SELECT * FROM users WHERE user_username = :username");

// Bind a value to the placeholder 
$regStmnt->bindValue('username', $usr);

// Execute query with prepared statement
$regStmnt->execute();

// Get returned row count
$count = $regStmnt->rowCount();

if ($count > 0) // If any matching rows exist the username is taken
{ 
	$_SESSION['error_msg'] = "This username is already taken"; // Set the error message
	header("Location: ../register-ui.php");	// Refresh the page
}
else // Otherwise, continue to upload the details...
{
	//Enter values into the appropriate table in mysql

	$query = $pdo->prepare("INSERT INTO users (user_username, user_fname, user_lname, user_email, user_password) 
	VALUES (:username,:fname, :lname, :email, :password)");

	$query->execute(['username'=>$usr, 'fname'=>$first, 'lname'=>$last, 'email'=>$email, 'password'=>$psw]);

	$result=$query->fetch();

	if($query) // If the query attemp returns true, direct to login screen to login
	{
		header("Location: ../login-ui.php");
	}
	else // If unsuccessful dispaly an error message and stay on this page
	{			
		$_SESSION['error_msg'] = "Sorry, there was a problem with your registration";
		header("Location: ../register-ui.php");	
	}
}

?>
