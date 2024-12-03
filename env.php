<?php
// Function to load environment variables from the .env file
function loadEnv($file) {
    if (!file_exists($file)) {
        throw new Exception("The .env file does not exist.");
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if ($line === null || strpos(trim($line), '#') === 0 || empty(trim($line))) {
            continue;
        }
    
        // Split each line into key-value pair
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
    
        // Set the environment variable
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Load the .env file (You can specify the file location here)
loadEnv(__DIR__ . '/.env'); // Adjust the path as needed

//EXAMPLE USAGE:
/*
    include('env.php');

    $dbHost = $_ENV['DB_HOST'] ?? 'default_host';
    $dbName = $_ENV['DB_NAME'] ?? 'default_name';
    $dbUser = $_ENV['DB_USER'] ?? 'default_user';
    $dbPass = $_ENV['DB_PASS'] ?? 'default_password';
*/

?>

