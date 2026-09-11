<?php

session_start();

require_once __DIR__ . "/../config/db.php";


// Only logged-in Admin can add users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: users.php");
    exit();
}


$name = "";
$email = "";
$role = "";
$errors = [];
$success = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $role = $_POST["role"] ?? "User";


    // Validation

    if ($name === "") {
        $errors[] = "Name is required.";
    }

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (!in_array($role, ["Admin", "User"], true)) {
        $errors[] = "Invalid role selected.";
    }


    // Check email already exists

    if (empty($errors)) {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $check->bind_param("s", $email);
        $check->execute();

        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $errors[] = "This email address is already registered.";
        }

        $check->close();
    }


    // Insert user

    if (empty($errors)) {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $conn->prepare(
            "INSERT INTO users (name, email, password, role)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $name,
            $email,
            $hashedPassword,
            $role
        );

        if ($stmt->execute()) {

            $success = "User added successfully.";

            $name = "";
            $email = "";
            $role = "User";
        } else {

            $errors[] = "Unable to add user. Please try again.";
        }

        $stmt->close();
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Add User</title>


    <!-- Bootstrap Icon CSS  -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <!-- Bootstrap CSS  -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Main Css  -->

    <link rel="stylesheet" href="./../Assets/CSS/add-user.css">

    <!-- Responsive Css  -->

    <link rel="stylesheet" href="./Assets/CSS/responsive.css">


</head>


<body>


    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-12 col-md-10 col-lg-7">


                <!-- Card -->

                <div class="card add-user-card border-0 shadow-lg">


                    <!-- Header -->

                    <div class="card-header add-user-header">

                        <div class="d-flex align-items-center">

                            <div class="add-user-icon">
                                <i class="bi bi-person-plus"></i>
                            </div>

                            <div>

                                <h4 class="mb-1">
                                    Add New User
                                </h4>

                                <small>
                                    Create a new user account
                                </small>

                            </div>

                        </div>

                    </div>


                    <!-- Body -->

                    <div class="card-body p-4">


                        <!-- Success -->

                        <?php if ($success): ?>

                            <div
                                class="alert alert-success d-flex align-items-center"
                                role="alert">

                                <i class="bi bi-check-circle-fill me-2"></i>

                                <?= htmlspecialchars($success) ?>

                            </div>

                        <?php endif; ?>


                        <!-- Errors -->

                        <?php if (!empty($errors)): ?>

                            <div class="alert alert-danger">

                                <div class="fw-semibold mb-2">
                                    Please fix the following:
                                </div>

                                <ul class="mb-0">

                                    <?php foreach ($errors as $error): ?>

                                        <li>
                                            <?= htmlspecialchars($error) ?>
                                        </li>

                                    <?php endforeach; ?>

                                </ul>

                            </div>

                        <?php endif; ?>


                        <form method="POST" action="">


                            <!-- Name -->

                            <div class="mb-3">

                                <label
                                    for="name"
                                    class="form-label">
                                    Full Name
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        class="form-control"
                                        placeholder="Enter full name"
                                        value="<?= htmlspecialchars($name) ?>"
                                        required>

                                </div>

                            </div>


                            <!-- Email -->

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label">
                                    Email Address
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="Enter email address"
                                        value="<?= htmlspecialchars($email) ?>"
                                        required>

                                </div>

                            </div>


                            <!-- Password -->

                            <div class="mb-3">

                                <label
                                    for="password"
                                    class="form-label">
                                    Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control"
                                        placeholder="Enter password"
                                        required>

                                </div>

                            </div>


                            <!-- Confirm Password -->

                            <div class="mb-3">

                                <label
                                    for="confirm_password"
                                    class="form-label">
                                    Confirm Password
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-shield-lock"></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="confirm_password"
                                        name="confirm_password"
                                        class="form-control"
                                        placeholder="Confirm password"
                                        required>

                                </div>

                            </div>


                            <!-- Role -->

                            <div class="mb-4">

                                <label
                                    for="role"
                                    class="form-label">
                                    Role
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person-badge"></i>
                                    </span>

                                    <select
                                        id="role"
                                        name="role"
                                        class="form-select">

                                        <option
                                            value="User"
                                            <?= $role === "User" ? "selected" : "" ?>>
                                            User
                                        </option>

                                        <option
                                            value="Admin"
                                            <?= $role === "Admin" ? "selected" : "" ?>>
                                            Admin
                                        </option>

                                    </select>

                                </div>

                            </div>


                            <!-- Buttons -->

                            <div class="d-flex flex-column flex-sm-row gap-2">

                                <a
                                    href="users.php"
                                    class="btn btn-outline-secondary flex-fill">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Back to Users
                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-primary flex-fill">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Add User
                                </button>

                            </div>


                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Main Js  -->
    <script src="./Assets/JS/script.js"></script>

    <!-- Bootstrap Js  -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


</body>

</html>