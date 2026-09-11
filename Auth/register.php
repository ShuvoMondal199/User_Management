<?php
session_start();

require_once __DIR__ . "/../config/db.php";

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

    <title>Register | User Management</title>


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


    <!-- Register CSS -->

    <link
        rel="stylesheet"
        href="../Assets/CSS/register.css">


    <!-- Responsive CSS -->

    <link
        rel="stylesheet"
        href="../Assets/CSS/responsive.css">

</head>


<body class="register-body">


    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center py-4">

        <div class="row justify-content-center w-100">

            <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">


                <!-- Register Card -->

                <div class="card register-card border-0 shadow-lg">

                    <div class="card-body p-4 p-md-5">


                        <!-- Logo -->

                        <div class="text-center mb-4">

                            <div class="register-logo">
                                <i class="bi bi-person-plus"></i>
                            </div>

                            <h2 class="register-title mt-3 mb-1">
                                Create Account
                            </h2>

                            <p class="register-subtitle mb-0">
                                Register a new account
                            </p>

                        </div>


                        <!-- Validation Errors -->

                        <?php if (!empty($errors)): ?>

                            <div
                                class="alert alert-danger alert-dismissible fade show"
                                role="alert">

                                <i class="bi bi-exclamation-circle-fill me-2"></i>

                                <strong>Please fix the following:</strong>

                                <ul class="mb-0 mt-2">

                                    <?php foreach ($errors as $error): ?>

                                        <li>
                                            <?= htmlspecialchars($error) ?>
                                        </li>

                                    <?php endforeach; ?>

                                </ul>

                                <button
                                    type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert"
                                    aria-label="Close">
                                </button>

                            </div>

                        <?php endif; ?>


                        <!-- Register Form -->

                        <form action="" method="POST">


                            <!-- Name -->

                            <div class="mb-3">

                                <label
                                    for="name"
                                    class="form-label fw-semibold">
                                    Full Name
                                </label>

                                <div class="input-group input-group-lg">

                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="name"
                                        name="name"
                                        placeholder="Enter your name"
                                        value="<?= htmlspecialchars($name) ?>"
                                        required>

                                </div>

                            </div>


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

                            <div class="mb-4">

                                <label
                                    for="password"
                                    class="form-label fw-semibold">
                                    Password
                                </label>

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
                                        <i
                                            class="bi bi-eye"
                                            id="passwordIcon">
                                        </i>
                                    </button>

                                </div>

                                <div class="password-help mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Minimum 8 characters with uppercase, lowercase,
                                    number and special character.
                                </div>

                            </div>


                            <!-- Submit -->

                            <button
                                type="submit"
                                class="btn btn-primary btn-lg w-100 register-btn">

                                <i class="bi bi-person-plus me-2"></i>

                                Create Account

                            </button>


                        </form>


                        <!-- Login -->

                        <div class="text-center mt-4">

                            <p class="text-muted mb-1">
                                Already have an account?
                            </p>

                            <a
                                href="./login.php"
                                class="register-link fw-semibold">
                                Login to your account
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

            if (password.type === "password") {

                password.type = "text";

                passwordIcon.classList.remove("bi-eye");
                passwordIcon.classList.add("bi-eye-slash");

            } else {

                password.type = "password";

                passwordIcon.classList.remove("bi-eye-slash");
                passwordIcon.classList.add("bi-eye");

            }

        });
    </script>


    <!-- Main JS -->

    <script src="../Assets/JS/script.js"></script>

</body>

</html>