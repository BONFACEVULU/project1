<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .welcome-container {
            text-align: center;
            margin-top: 100px;
        }

        .welcome-container h1 {
            font-size: 4em;
            color: #4CAF50;
            font-weight: bold;
        }

        .btn-custom {
            background-color: #4CAF50;
            color: white;
            font-size: 1.2em;
            padding: 15px 30px;
            border-radius: 5px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #45a049;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

    <div class="container welcome-container">
        <h1>Welcome to the Hub</h1>
        <a href="logForm.php" class="btn btn-custom">Login</a>
        <a href="usersignup.php" class="btn btn-custom">Sign Up</a>
    </div>

</body>
</html>
