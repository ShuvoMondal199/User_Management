<?php

session_start();

require_once __DIR__ . "/../config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/login.php");
    exit();
}

$userId = $_GET['id'] ?? '';

if (!is_numeric($userId)) {
    header("Location: users.php");
    exit();
}

$userId = (int) $userId;

$errors = [];

$name = "";
$email = "";
$role = "";
$profile = "";


// Get current user information

$stmt = $conn->prepare(
    "SELECT id, name, email, role, profile
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


// User not found

if (!$user) {
    header("Location: users.php");
    exit();
}


// Fill existing values

$name = $user['name'];
$email = $user['email'];
$role = $user['role'];
$profile = $user['profile'];


// Update user

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");


    // Name validation

    if (empty($name)) {

        $errors["name"] = "Name is required.";
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {

        $errors["name"] = "Only letters and white space allowed.";
    }


    // Email validation

    if (empty($email)) {

        $errors["email"] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errors["email"] = "Invalid email format.";
    }


    // Only Admin can change role

    if ($_SESSION["role"] === "Admin") {

        $role = trim($_POST["role"] ?? "");

        if (!in_array($role, ["Admin", "User", "Editor"], true)) {

            $errors["role"] = "Invalid role selected.";
        }
    } else {

        // Keep existing role

        $role = $user["role"];
    }


    // Check duplicate email

    if (empty($errors)) {

        $check = $conn->prepare(
            "SELECT id
             FROM users
             WHERE email = ?
             AND id != ?"
        );

        $check->bind_param("si", $email, $userId);
        $check->execute();

        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $errors["email"] = "Email already exists.";
        }

        $check->close();
    }


    // Profile picture

    $newProfile = $user["profile"];

    if (
        isset($_FILES["profile"]) &&
        $_FILES["profile"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {

        if ($_FILES["profile"]["error"] !== UPLOAD_ERR_OK) {

            $errors["profile"] = "Profile picture upload failed.";
        } else {

            $file = $_FILES["profile"];

            $maxSize = 2 * 1024 * 1024; // 2 MB

            if ($file["size"] > $maxSize) {

                $errors["profile"] =
                    "Profile picture must be less than 2MB.";
            } else {

                $allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/gif",
                    "image/webp"
                ];

                $fileType = mime_content_type($file["tmp_name"]);

                if (!in_array($fileType, $allowedTypes, true)) {

                    $errors["profile"] =
                        "Only JPG, PNG, GIF and WEBP images are allowed.";
                } else {

                    $uploadDir = __DIR__ . "/../Assets/Uploads/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $extension = strtolower(
                        pathinfo(
                            $file["name"],
                            PATHINFO_EXTENSION
                        )
                    );

                    $newProfile =
                        "profile_" .
                        $userId .
                        "_" .
                        time() .
                        "." .
                        $extension;

                    $uploadPath = $uploadDir . $newProfile;

                    if (!move_uploaded_file(
                        $file["tmp_name"],
                        $uploadPath
                    )) {

                        $errors["profile"] =
                            "Unable to save profile picture.";
                    }
                }
            }
        }
    }


    // Update database

    if (empty($errors)) {

        $update = $conn->prepare(
            "UPDATE users
             SET name = ?, email = ?, role = ?, profile = ?
             WHERE id = ?"
        );

        $update->bind_param(
            "ssssi",
            $name,
            $email,
            $role,
            $newProfile,
            $userId
        );

        if ($update->execute()) {

            // Delete old profile picture

            if (
                !empty($user["profile"]) &&
                $newProfile !== $user["profile"]
            ) {

                $oldProfile =
                    __DIR__ .
                    "/../Assets/Uploads/" .
                    $user["profile"];

                if (file_exists($oldProfile)) {
                    unlink($oldProfile);
                }
            }


            // If logged-in user edited themselves,
            // update session information

            if ($_SESSION["user_id"] == $userId) {

                $_SESSION["name"] = $name;
                $_SESSION["email"] = $email;
                $_SESSION["role"] = $role;
            }


            $_SESSION["success_message"] =
                "User profile updated successfully.";

            $update->close();

            header(
                "Location: user_info.php?id=" . $userId
            );

            exit();
        } else {

            $errors["general"] =
                "Unable to update profile. Please try again.";
        }

        $update->close();
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

    <title>Edit Profile</title>


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


    <!-- Your CSS -->

    <link
        rel="stylesheet"
        href="../Assets/CSS/edit.css">

    <link
        rel="stylesheet"
        href="../Assets/CSS/responsive.css">

</head>

<body>


    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-12 col-md-9 col-lg-7">

                <div class="card border-0 shadow-sm">

                    <!-- Header -->

                    <div class="card-header bg-primary text-white py-3">

                        <h4 class="mb-1">
                            <i class="bi bi-person-gear me-2"></i>
                            Edit Profile
                        </h4>

                        <small>
                            Update user information
                        </small>

                    </div>


                    <!-- Body -->

                    <div class="card-body p-4">


                        <!-- General Error -->

                        <?php if (isset($errors["general"])): ?>

                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-circle me-2"></i>

                                <?= htmlspecialchars($errors["general"]) ?>
                            </div>

                        <?php endif; ?>


                        <form method="POST" action="" enctype="multipart/form-data">


                            <!-- User ID -->

                            <div class="mb-3">

                                <label class="form-label fw-semibold">
                                    User ID
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?= htmlspecialchars($userId) ?>"
                                    readonly>

                            </div>

                            <!-- User Profile -->

                            <div class="mb-3">

                                <label for="profile" class="form-label fw-semibold">
                                    Profile Picture
                                </label>

                                <?php if (!empty($user['profile'])): ?>

                                    <div class="mb-2">
                                        <img
                                            src="../Assets/Uploads/<?= htmlspecialchars($user['profile']) ?>"
                                            alt="Profile Picture"
                                            width="80"
                                            height="80"
                                            class="rounded-circle">
                                    </div>

                                <?php endif; ?>

                                <input
                                    type="file"
                                    class="form-control"
                                    id="profile"
                                    name="profile"
                                    accept="image/*">

                                <small class="text-muted">
                                    Leave empty to keep the current profile picture.
                                </small>

                                <?php if (isset($errors["profile"])): ?>
                                    <div class="text-danger small mt-1">
                                        <?= htmlspecialchars($errors["profile"]) ?>
                                    </div>
                                <?php endif; ?>

                            </div>


                            <!-- Name -->

                            <div class="mb-3">

                                <label
                                    for="name"
                                    class="form-label fw-semibold">
                                    Name
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="name"
                                        name="name"
                                        value="<?= htmlspecialchars($name) ?>"
                                        placeholder="Enter name"
                                        required>

                                </div>

                                <?php if (isset($errors["name"])): ?>

                                    <div class="text-danger small mt-1">
                                        <?= htmlspecialchars($errors["name"]) ?>
                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- Email -->

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label fw-semibold">
                                    Email
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="<?= htmlspecialchars($email) ?>"
                                        placeholder="Enter email"
                                        required>

                                </div>

                                <?php if (isset($errors["email"])): ?>

                                    <div class="text-danger small mt-1">
                                        <?= htmlspecialchars($errors["email"]) ?>
                                    </div>

                                <?php endif; ?>

                            </div>

                            <?php if ($_SESSION["role"] === 'Admin'): ?>

                                <!-- Role -->

                                <div class="mb-4">

                                    <label
                                        for="role"
                                        class="form-label fw-semibold">
                                        Role
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            <i class="bi bi-person-badge"></i>
                                        </span>

                                        <select
                                            name="role"
                                            id="role"
                                            class="form-select"
                                            required>

                                            <option
                                                value="Admin"
                                                <?= $role === "Admin" ? "selected" : "" ?>>
                                                Admin
                                            </option>

                                            <option
                                                value="User"
                                                <?= $role === "User" ? "selected" : "" ?>>
                                                User
                                            </option>

                                            <option
                                                value="Editor"
                                                <?= $role === "Editor" ? "selected" : "" ?>>
                                                Editor
                                            </option>

                                        </select>

                                    </div>

                                    <?php if (isset($errors["role"])): ?>

                                        <div class="text-danger small mt-1">
                                            <?= htmlspecialchars($errors["role"]) ?>
                                        </div>

                                    <?php endif; ?>

                                </div>

                            <?php endif; ?>


                            <!-- Buttons -->

                            <div class="d-flex gap-2">

                                <a
                                    href="user_info.php?id=<?= $userId ?>"
                                    class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Cancel
                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>
                                    Update Profile
                                </button>

                            </div>

                        </form>

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

</body>

</html>