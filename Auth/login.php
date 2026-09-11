<?php
session_start();

require_once __DIR__ . "/../config/db.php";

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

                header("Location: ../index.php");
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

    <title>Login | User Management</title>


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <!-- Bootstrap CSS -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous">


    <!-- Main CSS -->

    <link
        rel="stylesheet"
        href="../Assets/CSS/login.css">


    <!-- Responsive CSS -->

    <link
        rel="stylesheet"
        href="../Assets/CSS/responsive.css">

</head>


<body class="login-body">


    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-4">

        <div class="row justify-content-center w-100">

            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">


                <!-- Login Card -->

                <div class="card login-card border-0 shadow-lg">


                    <!-- Card Header -->

                    <div class="card-body p-4 p-md-5">


                        <!-- Logo -->

                        <div class="text-center mb-4">

                            <div class="login-logo">
                                <i class="bi bi-person-circle"></i>
                            </div>

                            <h2 class="login-title mt-3 mb-1">
                                Welcome Back
                            </h2>

                            <p class="login-subtitle mb-0">
                                Login to your account
                            </p>

                        </div>


                        <!-- Error Message -->

                        <?php if (!empty($error)): ?>

                            <div
                                class="alert alert-danger alert-dismissible fade show"
                                role="alert">

                                <i class="bi bi-exclamation-circle-fill me-2"></i>

                                <?= htmlspecialchars($error) ?>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close">
                                </button>

                            </div>

                        <?php endif; ?>


                        <!-- Success Message -->

                        <?php if (isset($_SESSION['success_message'])): ?>

                            <div
                                class="alert alert-success alert-dismissible fade show"
                                role="alert">

                                <i class="bi bi-check-circle-fill me-2"></i>

                                <?= htmlspecialchars($_SESSION['success_message']) ?>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close">
                                </button>

                            </div>

                            <?php unset($_SESSION['success_message']); ?>

                        <?php endif; ?>


                        <!-- Login Form -->

                        <form action="" method="POST">


                            <!-- Email -->

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label fw-semibold">
                                    Email Address
                                </label>

                                <div class="input-group input-group-lg">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        placeholder="Enter your email"
                                        value="<?= htmlspecialchars($email) ?>"
                                        required>

                                </div>

                            </div>


                            <!-- Password -->

                            <div class="mb-3">

                                <div class="d-flex justify-content-between">

                                    <label
                                        for="password"
                                        class="form-label fw-semibold">
                                        Password
                                    </label>

                                </div>


                                <div class="input-group input-group-lg">

                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>

                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        placeholder="Enter your password"
                                        required>

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary password-toggle"
                                        id="togglePassword"
                                        tabindex="-1">
                                        <i class="bi bi-eye" id="passwordIcon"></i>
                                    </button>

                                </div>

                            </div>


                            <!-- Role -->

                            <div class="mb-4">

                                <label
                                    for="role"
                                    class="form-label fw-semibold">
                                    Select Role
                                </label>

                                <div class="input-group input-group-lg">

                                    <span class="input-group-text">
                                        <i class="bi bi-person-badge"></i>
                                    </span>

                                    <select
                                        name="role"
                                        id="role"
                                        class="form-select"
                                        required>

                                        <option value="" disabled selected>
                                            Select your role
                                        </option>

                                        <option value="Admin">
                                            Admin
                                        </option>

                                        <option value="User">
                                            User
                                        </option>

                                        <option value="Editor">
                                            Editor
                                        </option>

                                    </select>

                                </div>

                            </div>


                            <!-- Submit -->

                            <button
                                type="submit"
                                class="btn btn-primary btn-lg w-100 login-btn">

                                <i class="bi bi-box-arrow-in-right me-2"></i>

                                Login

                            </button>


                        </form>


                        <!-- Register -->

                        <div class="text-center mt-4">

                            <p class="text-muted mb-1">
                                Don't have an account?
                            </p>

                            <a
                                href="./register.php"
                                class="register-link fw-semibold">
                                Create an account
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>

                        </div>


                        <!-- Back Home -->

                        <div class="text-center mt-3">

                            <a
                                href="../index.php"
                                class="home-link">
                                <i class="bi bi-house-door me-1"></i>
                                Back to Home
                            </a>

                        </div>


                    </div>

                </div>


            </div>

        </div>

    </div>


    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl-+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
    </script>


    <!-- Password Toggle -->

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const password = document.getElementById("password");
        const passwordIcon = document.getElementById("passwordIcon");

        togglePassword.addEventListener("click", function() {

            const type =
                password.getAttribute("type") === "password" ?
                "text" :
                "password";

            password.setAttribute("type", type);

            passwordIcon.classList.toggle("bi-eye");
            passwordIcon.classList.toggle("bi-eye-slash");

        });
    </script>


    <!-- Main JS -->

    <script src="../Assets/JS/script.js"></script>


</body>

</html>