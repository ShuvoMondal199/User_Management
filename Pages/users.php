<?php
session_start();

require_once __DIR__ . "/../config/db.php";



$stmt = $conn->prepare("SELECT id, name, email, role FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USERS</title>

    <!-- Bootstrap Icon CSS  -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">


    <!-- Bootstrap CSS  -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Main Css  -->

    <link rel="stylesheet" href="./../Assets/CSS/users.css">

    <!-- Responsive Css  -->

    <link rel="stylesheet" href="/Assets/CSS/responsive.css">
</head>

<body>


    <div class="container-xxl py-4">

        <div class="card user-card border-0 shadow-lg">

            <div class="card-header user-card-header">
                <div>
                    <h5 class="mb-1 fw-semibold">Users</h5>
                    <small class="text-secondary text-capitalize">
                        Manage registered users
                    </small>
                </div>

                <span class="user-count">
                    <?= count($users) ?> Users
                </span>

                <a href="./../index.php" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Back To Home
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table user-table align-middle mb-0">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($users as $u) : ?>

                                <tr>

                                    <td class="user-id">
                                        #<?= $u["id"] ?>
                                    </td>

                                    <td>
                                        <div class="user-name">
                                            <?= htmlspecialchars($u["name"]) ?>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="user-email">
                                            <?= htmlspecialchars($u["email"]) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="role-badge">
                                            <?= htmlspecialchars($u["role"]) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if ($_SESSION['role'] === 'Admin') : ?>
                                            <div class="action-buttons">

                                                <a href="./edit.php?id=<?= $u["id"] ?>"
                                                    class="btn-action btn-edit">
                                                    Edit
                                                </a>

                                                <a href="./user_info.php?id=<?= $u["id"] ?>"
                                                    class="btn-action btn-view">
                                                    View
                                                </a>

                                                <a href="./delete.php?id=<?= $u["id"] ?>"
                                                    class="btn-action btn-delete"
                                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                                    Delete
                                                </a>

                                            </div>

                                        <?php else : ?>
                                            <div class="action-buttons">

                                                <a href="./user_info.php?id=<?= $u["id"] ?>"
                                                    class="btn-action btn-view">
                                                    View
                                                </a>

                                            </div>
                                        <?php endif ?>
                                    </td>

                                </tr>

                            <?php endforeach ?>

                        </tbody>

                    </table>

                </div>
            </div>

        </div>

    </div>






    <!-- Bootstrap Js  -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Main Js  -->
    <script src="/Assets/JS/script.js"></script>


</body>

</html>