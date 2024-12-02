<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
	<title></title>
	<link href="../../../css/landingPage.css" rel="stylesheet" />
    <script src="../../../JavaScript/createGame.js" defer></script><!---makes sure the values are in localStorage--->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        setTimeout(function(){
            //Redirect to the round result's page
            window.location.href = "teacherRoundResults.php";
        }, 6000);
    </script>
</head>
<body>
    
<link href="../../../css/header.css" rel="stylesheet" />
<div id="headerTop"></div>
<p><img id="logo" src="../../../Img/logo.svg"></p> 
<div id="headerLeft"></div>
<div id="headerBottom"></div>
<br><br><br><br><br><br>
<br>
<br>
<br>
<br>
<div class="loader"></div>
<h1 id="gameName">Please Wait</h1>
<br>
<span id="connectionStatus">The student's scores are being calculated</span></p>
</body>
</html>