<?php
    //Connect to database
        $mysqli = require __DIR__ ."/db.php";

    // Start the session if not already started
        session_start();
    
    //Get data from the database
        $sqlStudents = "SELECT * FROM student_profile p
                            JOIN session_instance s
                            ON p.student_id = s.student_id;";
        
        $resultStudents = $mysqli->query($sqlStudents);
        $mysqli->close();

?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8">
	<title>All Students</title>
	<link href="/CSSGametheory/css/admin.css" rel="stylesheet" /><!---<script src="/CSSGametheory/javascript/studentRetrieve.js" defer></script>---><script src="/CSSGametheory/javascript/createGame.js" defer></script><!--- makes sure the values are in localStorage --->
</head>
<body>
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 


<h1 id="studentHeader">All Students</h1>
<!--- TODO: make student names dynamic, pulling all student names from DB sorted in alphabetical order---><!--- TODO: make links dynamic with student names --->

<div id="studentNames">
<table>
            <th>Student Name</th>
            <th>Game Session ID</th>
	<tbody>
		<!-- PHP CODE TO FETCH DATA FROM ROWS -->
        <?php 
            // LOOP TILL END OF DATA
            while($rows = $resultStudents->fetch_assoc()) {
                // Get the 'create_dt' and 'game_session_id' values
                $first_nm = $rows['first_nm'];
                $last_nm = $rows['last_nm'];
                $student_id = $rows['student_id'];
                $session_id = $rows['session_id'];
        ?>
        
        <tr>
            <!-- Wrap the 'create_dt' value in a link to another page and add it to the session -->
            <td>
                <a href="studentView.php?session_id=<?php echo $session_id; ?>">
					<?php echo $first_nm; ?>
				</a>
            </td>
            <!-- Add the 'game_session_id' value to the session -->
            <td>
                <?php echo $session_id; ?>
            </td>
        </tr>
        <?php
        }
        ?>
	</tbody>
</table>
</div>

<p><br />
<!--- TODO: create footer with iframe, making sure to include target=_blank attribute ---></p>

<table id="links">
	<tbody>
		<tr>
			<td><a href="/CSSGametheory/HTML/admin/allStudents.php">All Students</a></td>
			<td><a href="https://cssgametheory.com/">Back to Home</a> <!--admin index--></td>
			<td><a href="/CSSGametheory/HTML/admin/allGames.php">All Games</a></td>
		</tr>
	</tbody>
</table>
</body>
<!---
<script language="javascript">
	window.onload = function(e){ 
		displayInfo();
	}
</script>
---></html>