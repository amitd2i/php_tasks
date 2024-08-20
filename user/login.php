<?php
session_start();

function handleLogin($email, $password) {
    $users = fetchUsers();

    foreach ($users as $user) {
        if (verifyCredentials($user, $email, $password)) {
            initiateSession($user);
            redirectToDashboard();
        }
    }

    return "Invalid email or password!";
}

function fetchUsers() {
    $fileContent = file_get_contents('db/users_data.json');
    return json_decode($fileContent, true);
}

function verifyCredentials($user, $email, $password) {
    return $user['email'] === $email && password_verify($password, $user['password']);
}

function initiateSession($user) {
    $_SESSION['user'] = $user;
}

function redirectToDashboard() {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $error = handleLogin($email, $password);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        input[type="email"],
        input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #4cae4c;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #5cb85c;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            text-align: center;
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <form method="post">
            <label for="email">Email:</label>
            <input type="email" name="email" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br>

            <input type="submit" value="Login">
        </form>
        <a href="/registration.php">Register</a>
        <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
    </div>
</body>
</html>
