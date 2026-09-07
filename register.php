<?php
session_start();

require_once __DIR__ . "/config/db.php";

$errors = [];

$name = "";
$email = "";
$password = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    


    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $plainPassword = trim($_POST['password'] ?? '');


    if (empty($name)) {
        $errors['name'] = "Name Is Empty!";
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        $errors['name'] = "Only letters and white space allowed";
    }

    if (empty($email)) {
        $errors['email'] = "Email Is Empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid Email format";
    }

    if (empty($plainPassword)) {
        $errors['password'] = "Password Is Empty!";
    } elseif (strlen($plainPassword) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $plainPassword)) {
        $errors['password'] = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[a-z]/', $plainPassword)) {
        $errors['password'] = "Password must contain at least one lowercase letter.";
    } elseif (!preg_match('/[0-9]/', $plainPassword)) {
        $errors['password'] = "Password must contain at least one number.";
    } elseif (!preg_match('/[\W_]/', $plainPassword)) {
        $errors['password'] = "Password must contain at least one special character.";
    }

    if (empty($errors)) {

        $password = password_hash($plainPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
        );

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {

            $_SESSION['success_message'] =
                "Registration successful! Please login.";

            header('Location: login.php');
            exit();

        } else {

            die("Insert failed: " . $stmt->error);
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | | User</title>

      
    <!-- Bootstrap Icon CSS  -->
    
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">


    <!-- Bootstrap CSS  -->
    
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Main Css  -->

    <link rel="stylesheet" href="./Assets/CSS/register.css">

    <!-- Responsive Css  -->

    <link rel="stylesheet" href="/Assets/CSS/responsive.css">


</head>
<body>
    <div class="container-xxl d-flex align-items-center justify-content-center vh-100">

    <form action="" method="POST" class="w-50 m-auto bg-dark p-4 rounded text-white">

        <div class="mb-3 row align-items-center">
            <div class="col-3">
                <label for="name" class="form-label">Name :</label>
            </div>
            <div class="col-9">
                <input type="text" class="form-control" id="name" name="name"
                       placeholder="Enter Your Name" required>
            </div>
        </div>

        <div class="mb-3 row align-items-center">
            <div class="col-3">
                <label for="email" class="form-label">Email :</label>
            </div>
            <div class="col-9">
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="Enter Your Email" required>
            </div>
        </div>

        <div class="mb-3 row align-items-center">
            <div class="col-3">
                <label for="password" class="form-label">Password :</label>
            </div>
            <div class="col-9">
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Enter Your Password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit</button>

    </form>

</div>






<!-- Bootstrap Js  -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- Main Js  -->
<script src="/Assets/JS/script.js"></script>
    
    
</body>
</html>