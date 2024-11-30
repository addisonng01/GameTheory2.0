<?php
    //Need to start the session whenever you get to a new page to use the variables
    session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="utf-8">
	<link href="/CSSGametheory/css/createProfile.css" rel="stylesheet" />
</head>
	<script src="/CSSGametheory/javascript/createProfile.js" defer></script>
	<script src="/CSSGametheory/javascript/studentSignOn.js" defer></script> <!--- I don't think this .js file is used on this page --->
<body>
    
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 

<div id="createSuccess">
	Profile Created!
</div>
<div id="userInfo">
	<p id="firstNameDisplay">First Name: <?php echo $_SESSION['firstName']; ?></p>
	<p id="lastNameDisplay">Last Name: <?php echo $_SESSION['lastName']; ?></p>
	<p id="emailDisplay">Email: <?php echo $_SESSION['email']; ?></p>
</div>
<p>
	<button id="studentSignOn" onclick="window.location.href='/CSSGametheory/HTML/TeacherSignInFolder/teacherSignOn.php';">Sign In</button> 
    <!--- should this be updated to say teacherSignOn? both here and in the createProfile.css file --->
</p>
</body>
<script language="javascript">
	window.onload = function(e){ 
		displayInfo(); // this uses different keys in localStorage than teacherCreateProfile.html had inserted, which is why it's not displaying anything
	}
</script>
</html>