<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="../../css/studentLoading.css">
</head>
<body>
  <!-- Heading -->
    <link href="https://cssgametheory.com/CSSGametheory/css/header.css" rel="stylesheet" />
<p style="margin: 0 auto; width: 200px"><img id="logo" src="https://cssgametheory.com/CSSGametheory/Img/logo.svg"></p> 
    <br><br><br><br><br><br>
  
    <!--- doesn't pass in without context --->
    <!---
    <span>
        game ID: <?php echo $game_id; ?><br>
        game title: <?php echo $game_title; ?><br>
    </span>
    --->

  <!-- Loading screen content -->
  <div class="loader-container">
    <div class="loader"><i class="fas fa-circle-notch fa-spin"></i></div>
  </div>

    <!-- This sets the time for the loading screen to last, could change to a seperate file but needs testing -->
  <script src="script.js"></script>
</body>
</html>