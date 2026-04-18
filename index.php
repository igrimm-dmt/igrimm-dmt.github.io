<?php
// Main entry point - redirect to host or participant page
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzzer App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .btn-group {
            margin-top: 30px;
        }
        a {
            display: inline-block;
            padding: 20px 40px;
            margin: 10px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
        }
        a:hover {
            background-color: #45a049;
        }
        .participant-btn {
            background-color: #2196F3;
        }
        .participant-btn:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>?? Buzzer App</h1>
        <p>Choose your role:</p>
        <div class="btn-group">
            <a href="host.php">Host</a>
            <a href="participant.php" class="participant-btn">Participant</a>
        </div>
    </div>
</body>
</html>
