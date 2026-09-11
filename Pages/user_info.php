<?php

session_start();

require_once "../config/db.php";

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

$stmt = $conn->prepare(
    "SELECT id, name, email, profile, created_at, role
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Information</title>


    <!-- Bootstrap Icon CSS  -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <!-- Bootstrap CSS  -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Main Css  -->

    <link rel="stylesheet" href="./../Assets/CSS/info.css">

    <!-- Responsive Css  -->

    <link rel="stylesheet" href="./Assets/CSS/responsive.css">


</head>

<body>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-12 col-md-10 col-lg-7">

                <div class="card user-info-card border-0 shadow">

                    <!-- Header -->
                    <div class="card-header user-info-header text-center py-4">


                        <div class="user-avatar">

                            <?php if (!empty($user['profile'])): ?>

                                <img
                                    src="../Assets/Uploads/<?= htmlspecialchars($user['profile']) ?>"
                                    alt="Profile Picture"
                                    class="profile-avatar">

                            <?php else: ?>

                                <?= strtoupper(substr($user['name'], 0, 1)) ?>

                            <?php endif; ?>

                        </div>

                        <h2 class="mt-3 mb-1">
                            <?= htmlspecialchars($user['name']) ?>
                        </h2>

                        <p class="mb-0">
                            User Profile
                        </p>

                    </div>


                    <!-- User Information -->
                    <div class="card-body p-4">

                        <h5 class="section-title mb-4">
                            Account Information
                        </h5>


                        <!-- User ID -->
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="bi bi-info-circle"></i>
                            </div>

                            <div>
                                <small>User ID</small>

                                <div class="info-value">
                                    <?= htmlspecialchars($user['id']) ?>
                                </div>
                            </div>

                        </div>


                        <!-- Name -->
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="bi bi-person"></i>
                            </div>

                            <div>
                                <small>Full Name</small>

                                <div class="info-value">
                                    <?= htmlspecialchars($user['email']) ?>
                                </div>
                            </div>

                        </div>


                        <!-- Email -->
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="bi bi-envelope"></i>
                            </div>

                            <div>
                                <small>Email Address</small>

                                <div class="info-value">
                                    <?= htmlspecialchars($user['email']) ?>
                                </div>
                            </div>

                        </div>





                        <!-- Created Date -->
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="bi bi-calendar-plus"></i>
                            </div>

                            <div>
                                <small>Registered Date</small>

                                <div class="info-value">
                                    <?= htmlspecialchars($user['created_at']) ?>
                                </div>
                            </div>

                        </div>



                    </div>


                    <!-- Footer Buttons -->
                    <div class="card-footer bg-white border-0 p-4">

                        <div class="d-flex flex-column flex-sm-row gap-2">

                            <a
                                href="users.php"
                                class="btn btn-outline-secondary flex-fill">
                                Back to Users
                            </a>
                            <a
                                href="./../index.php"
                                class="btn btn-warning flex-fill">
                                Back to Home
                            </a>

                            <?php if ($_SESSION['role'] === 'Admin' || $userId === $_SESSION['user_id']) : ?>
                                <a
                                    href="edit.php?id=<?= $userId ?>"
                                    class="btn btn-primary flex-fill">
                                    Edit Profile
                                </a>

                            <?php endif; ?>

                        </div>

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