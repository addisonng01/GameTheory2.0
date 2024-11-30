<?php
    //Start the session
    session_start();

    //Save required variables for the page
    $first_nm = $_SESSION["first_nm"];
    $last_nm = $_SESSION["last_nm"];
    $email = $_SESSION["Email"];
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../../css/createProfile.css">
        <script src="../../JavaScript/createProfile.js" defer></script>
        <script src="../../JavaScript/studentSignOn.js" defer></script>
    </head>
    <link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 

    <body>
        <div id="createSuccess">
            Profile Created!
        </div>
        <div id ="userInfo">
            <p id="firstNameDisplay">
                First Name: <?php echo $first_nm; ?>
            </p>
            <p id="lastNameDisplay">
                Last Name: <?php echo $last_nm; ?>
            </p>
            <p id="emailDisplay">
                Email: <?php echo $email; ?>
            </p>
        </div>
        <button id="studentSignOn"  onclick="window.location.href='studentSignOn.php';">
            Sign In
        </button>
    </body>
</html>