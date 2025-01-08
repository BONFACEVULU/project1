<!DOCTYPE html>
<html>
<head>
    <title>Welcome Home</title>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            min-width: 300px; /* Set a minimum width for better layout */
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        h1 {
            color: #333;
            font-size: 3em;
            margin-bottom: 20px;
        }

        p {
            color: #666;
            font-size: 1.2em;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <header>
        <h1>My Website</h1> 
        <a href="welcome.php" class="logout-btn">Logout</a> 
    </header>

    <div class="container">
        <h1>Welcome Home!</h1>
        <p>This is your homepage.</p>
    </div>

    <footer>
        &copy; 2023 My Website
    </footer>

</body>
</html>