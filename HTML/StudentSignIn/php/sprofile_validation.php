<?php

$email = $_POST["email"];

//Sample of validation
if (empty($_POST["email"])) {
    die("Email is required");
}

// References the connection information for the database
$mysqli = require __DIR__ ."/db.php";

$stmt = $mysqli->prepare("SELECT * FROM student_profile WHERE email=?");
$stmt->execute([$email]); 
$user = $stmt->fetch();
if ($user) {
    // email found
    echo"Email was found";
} else {
    // or not
    //echo "Email was not found";
    header("Location: /CSSGametheory/HTML/StudentSignIn/studentCreateProfile.html");
    exit();
} 


/*
//Check if email exists
$sql = "SELECT COUNT(email) AS email_num FROM student_profile WHERE email = cpolingo@css.edu";

$result = $mysqli->query($stmt);

if ($result->num_rows > 0) { 
    while($row = $result->fetch_assoc()) { 
      echo "email: " . $row["email_num"]; 
    } 
}  
else { 
    echo "No records has been found"; 
} 

/*
//Insert Student Profile Data
$sql = "INSERT INTO student_profile (first_nm, last_nm, email) VALUES (?,?,?)";

$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("sss", $_POST["firstName"], $_POST["lastName"], $_POST["email"]);

if ($stmt->execute()) {
    echo"Success";
} else {
    die($mysqli->error . " " . $mysqli->errno);
}
/*
if ($stmt->execute()) {
    header("Location: TEMP.html");
    exit;
} else {
    if ($mysqli->errno === 1062) {
        die("That email is already associated with a student");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}
*/