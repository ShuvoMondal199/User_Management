<?php
session_start();

require_once __DIR__ . "/config/db.php";

$errors = [];

$email = "";
$password = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    


    $email = trim($_POST['email'] ?? '');
    $plainPassword = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? '');


  

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




  if (empty($email) || empty($plainPassword)) {
        $error = "All fields are required.";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();




        $result = $stmt->get_result();

        if ($result->num_rows === 1) {


            $user = $result->fetch_assoc();

            if (
                password_verify($plainPassword, $user['password']) &&
                $role === $user['role']
            ) {
                $_SESSION['name'] = $user['name'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid Credencial!";
            }
        } else {
            $error = "User not found.";
        }
    }
 
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | | User</title>

      
    <!-- Bootstrap Icon CSS  -->
    
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">


    <!-- Bootstrap CSS  -->
    
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Main Css  -->

    <link rel="stylesheet" href="./Assets/CSS/login.css">

    <!-- Responsive Css  -->

    <link rel="stylesheet" href="/Assets/CSS/responsive.css">


</head>
<body>
<div class="container-xxl d-flex align-items-center justify-content-center vh-100">
     <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION['success_message']; ?>
                        </div>
                        <?php unset($_SESSION['success_message']); ?>
                    <?php endif; ?>

    <form action="" method="POST" class="w-50 m-auto bg-dark p-4 rounded text-white">


        

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


        <div class="mb-3 row align-items-center">
            <div class="col-3">
                <label for="role" class="form-label">Role :</label>
            </div>
            <div class="col-9">
                <select name="role" id="role" class="form-select">
                    <option value="" selected>Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                    <option value="Editor">Editor</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit</button>
        <a href="./register.php">Register</a>

    </form>

</div>







<!-- Bootstrap Js  -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- Main Js  -->
<script src="/Assets/JS/script.js"></script>
    
    
</body>
</html>