<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Loop through each key-value pair in the $_POST array
    foreach ($_POST as $key => $value) {
        // Print out the key and value
        echo $key . ": " . $value . "<br>";
    }
} else {
    // If the request method is not POST, display an error message
    echo "This page should only be accessed through a POST request.";
}
?>
