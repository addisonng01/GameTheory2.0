<?php
	//Connect to database
    $mysqli = require __DIR__ ."/db.php";

	// Start the session if not already started
	session_start();

	//Get data from the database
	$sqlRedBlack = "SELECT * FROM game_session
						WHERE game_id = 1
    					ORDER BY create_dt DESC";
	
	$resultRedBlack = $mysqli->query($sqlRedBlack);
	$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>All Games</title>
	<meta charset="utf-8">
	<link href="/CSSGametheory/css/admin.css" rel="stylesheet" />
</head>
<body>
    
<!--- the issue with iframe is that it doesn't really work with our DB page reload strategy, so we have to hardcode the header in - compare content to header.html --->
<link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 

<!--- TODO: make game date links dynamic, pulling all game dates from DB sorted in chronological order--->
<!--- TODO: make links dynamic with game dates --->

<!-- Database reading version of Red/Black Card Session Dates -->
<div id="agame">
     <h1>All Games</h1>
</div>
<div id="redBlackGameDates">
    <table>
        <tr>
            <th>Black Card Red Card</th>
            <th>Game Session ID</th>
        </tr>
        <!-- PHP CODE TO FETCH DATA FROM ROWS -->
        <?php 
            // LOOP TILL END OF DATA
            while($rows = $resultRedBlack->fetch_assoc()) {
                // Get the 'create_dt' and 'game_session_id' values
                $create_dt = $rows['create_dt'];
                $game_session_id = $rows['game_session_id'];
        ?>
        <tr>
            <!-- Wrap the 'create_dt' value in a link to another page and add it to the session -->
            <td>
                <a href="redBlackView.php?game_session_id=<?php echo $game_session_id; ?>">
					<?php echo $create_dt; ?>
				</a>
            </td>
            <!-- Add the 'game_session_id' value to the session -->
            <td>
                <?php echo $game_session_id; ?>
            </td>
        </tr>
        <?php
        }
        ?>
    </table>
</div>

<div id="wheatSteelGameDates">
	<table>
		<caption style="font-size: 18px"><strong>Wheat &amp; Steel</strong></caption>
		<!--- make sure date in links matches DB format--->
		<tbody>
			<tr>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=3-13-23">3-13-23</a></td>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=5-1-23">5-1-23</a></td>
			</tr>
			<tr>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=3-20-23">3-20-23</a></td>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=9-11-23">9-11-23</a></td>
			</tr>
			<tr>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=3-27-23">3-27-23</a></td>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=9-18-23">9-18-23</a></td>
			</tr>
			<tr>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=4-3-23">4-3-23</a></td>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=9-25-23">9-25-23</a></td>
			</tr>
			<tr>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=4-10-23">4-10-23</a></td>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=10-2-23">10-2-23</a></td>
			</tr>
			<tr>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=4-17-23">4-17-23</a></td>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=10-9-23">10-9-23</a></td>
			</tr>
			<tr>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=4-24-23">4-24-23</a></td>
				<td class="gameDateCell"><a href="/CSSGametheory/HTML/admin/wheatSteelView.html?date=10-16-23">10-16-23</a></td>
			</tr>
		</tbody>
	</table>
</div>

<br />
<!--- TODO: create footer with iframe, making sure to include target=_blank attribute --->
<!--table w/ links-->

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

<script>
function loadHeader() {
    let header = document.getElementById("siteHeader");
    header.style.visibility="block";
    //header.src = "/CSSGametheory/HTML/header.html";
}
</script>
</html>