<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$file = file_get_contents('db/users_data.json');
$users = json_decode($file, true);

$loggedInUser = null;
foreach ($users as $user) {
    if ($user['email'] === $_SESSION['user']['email']) {
        $loggedInUser = $user;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($users as &$user) {
        if ($user['email'] === $_SESSION['user']['email']) {
            $user['name'] = $_POST['name'];
            $user['age'] = $_POST['age'];
            $user['email'] = $_POST['email'];
            if (!empty($_FILES['profile_photo']['name'])) {
                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($_FILES["profile_photo"]["name"]);
                move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFile);
                $user['profile_photo'] = $targetFile;
            }
            $_SESSION['user'] = $user;
            break;
        }
    }
    file_put_contents('db/users_data.json', json_encode($users, JSON_PRETTY_PRINT));
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 80%;
            max-width: 800px;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #5cb85c;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        img {
            border-radius: 50%;
        }

        a, form button {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }

        a:hover, form button:hover {
            background-color: #4cae4c;
        }

        form {
            display: inline;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="file"] {
            padding: 10px;
            margin: 10px 0;
            width: calc(100% - 22px);
            max-width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
    <h1>Dashboard</h1>
    <?php if ($loggedInUser): ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Age</th>
            <th>Email</th>
            <th>Profile Photo</th>
        </tr>
        <tr>
            <td><?php echo $loggedInUser['name']; ?></td>
            <td><?php echo $loggedInUser['age']; ?></td>
            <td><?php echo $loggedInUser['email']; ?></td>
            <td><img src="<?php echo $loggedInUser['profile_photo']; ?>" alt="Profile Photo" width="50" height="50"></td>
        </tr>
    </table>

    <form method="POST" enctype="multipart/form-data">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $loggedInUser['name']; ?>" required><br>
        
        <label for="age">Age:</label><br>
        <input type="number" id="age" name="age" value="<?php echo $loggedInUser['age']; ?>" required><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $loggedInUser['email']; ?>" required><br>
        
        <label for="profile_photo">Profile Photo:</label><br>
        <input type="file" id="profile_photo" name="profile_photo"><br>
        
        <button type="submit" name="update">Update</button>
    </form>
    <?php endif; ?>

    <form method="POST">
        <button type="submit" name="logout">Logout</button>
    </form>
</body>
</html>
