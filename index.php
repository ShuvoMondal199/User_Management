<?php
session_start();


if (empty($_SESSION['name'])) {
    header("Location: ./Auth/login.php");
    exit();
}

$name = $_SESSION['name'] ?? 'User';
$initial = strtoupper(substr(trim($name), 0, 1));

require_once __DIR__ . "/config/db.php";


$stmt = $conn->prepare("SELECT * FROM users");
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);


?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>


    <!-- Bootstrap Icon CSS  -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <!-- Bootstrap CSS  -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Main Css  -->

    <link rel="stylesheet" href="./Assets/CSS/style.css">

    <!-- Responsive Css  -->

    <link rel="stylesheet" href="./Assets/CSS/responsive.css">


</head>

<body>

    <div class="dashboard-wrapper">


        <!-- ==========================================
         SIDEBAR
    =========================================== -->

        <aside class="sidebar show" id="sidebar">

            <!-- Logo -->

            <div class="sidebar-logo">

                <div class="logo-icon">
                    <i class="bi bi-people-fill"></i>
                </div>

                <span class="logo-text">
                    User Management
                </span>

            </div>


            <!-- Navigation -->

            <nav class="sidebar-nav">

                <div class="nav-item">

                    <a
                        href="./index.php"
                        class="nav-link-custom active">
                        <i class="bi bi-house-door"></i>
                        <span>Dashboard</span>
                    </a>

                </div>


                <div class="nav-item">

                    <a
                        href="./Pages/users.php"
                        class="nav-link-custom">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>

                </div>

                <?php if ($_SESSION['role'] === 'Admin') : ?>

                    <div class="nav-item">

                        <a
                            href="./Pages/add-user.php"
                            class="nav-link-custom">
                            <i class="bi bi-plus-lg"></i>
                            <span>Add User</span>
                        </a>

                    </div>

                <?php endif ?>


                <div class="nav-item">

                    <a
                        href="./Pages/users.php"
                        class="nav-link-custom">
                        <i class="bi bi-shield-check"></i>
                        <span>Roles</span>
                    </a>

                </div>


               

            </nav>


            <!-- Logout -->

            <div class="sidebar-bottom">

                <div class="logout-link">

                    <a
                        href="./Auth/logout.php"
                        class="nav-link-custom">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Logout</span>
                    </a>

                </div>

            </div>

        </aside>



        <!-- ==========================================
         MAIN CONTENT
    =========================================== -->

        <main class="main-content">


            <!-- ======================================
             TOPBAR
        ======================================= -->

            <header class="topbar">

                <button type="button" class="menu-btn" id="menuBtn">
                    <i class="bi bi-list"></i>
                </button>



                <!-- Right -->

                <div class="topbar-right">


                    <!-- Notification -->

                    <a
                        href="#"
                        class="notification"
                        aria-label="Notifications">

                        <i class="bi bi-bell"></i>

                        <span class="notification-dot"></span>

                    </a>


                    <!-- Profile -->

                    <div class="profile">

                        <div class="profile-avatar">
                            <?= htmlspecialchars($initial) ?>
                        </div>

                        <span class="profile-name">
                            <?= htmlspecialchars($name) ?>
                        </span>

                        <i class="bi bi-chevron-down"></i>

                    </div>

                </div>

            </header>



            <!-- ======================================
             PAGE CONTENT
        ======================================= -->

            <div class="page-content">


                <!-- ==================================
                 WELCOME
            =================================== -->

                <section class="welcome-section">

                    <div>

                        <h1 class="welcome-title text-capitalize">
                            Welcome Back, <?= htmlspecialchars($_SESSION['name']); ?>!
                        </h1>

                        <p class="welcome-text mb-0">
                            Manage your users and keep your system organized.
                        </p>

                    </div>


                    <div class="date-display">

                        <i class="bi bi-calendar3"></i>

                        <span>
                            <?= date("l, F d, Y"); ?>
                        </span>

                    </div>

                </section>



                <!-- ==================================
                 STATISTICS
            =================================== -->

                <div class="row g-4 stats-row">


                    <!-- Total Users -->

                    <div class="col-12 col-md-6 col-xl-3">

                        <div class="stat-card stat-blue">

                            <div class="stat-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>

                            <div>

                                <div class="stat-number">
                                    <?= count($users) ?>
                                </div>

                                <div class="stat-label">
                                    Total Users
                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- Active Users -->

                    <div class="col-12 col-md-6 col-xl-3">

                        <div class="stat-card stat-green">

                            <div class="stat-icon">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div>
                                <div class="stat-number">
                                    <?= count(array_filter($users, fn($u) => ($u["status"] ?? "") === "Active")) ?>
                                </div>

                                <div class="stat-label">
                                    Active Users
                                </div>
                            </div>

                        </div>

                    </div>



                    <!-- Admins -->

                    <div class="col-12 col-md-6 col-xl-3">

                        <div class="stat-card stat-yellow">

                            <div class="stat-icon">
                                <i class="bi bi-person-plus-fill"></i>
                            </div>

                            <div>

                                <div class="stat-number">
                                    <?= count(array_filter($users, fn($u) => $u["role"] === "Admin")) ?>
                                </div>

                                <div class="stat-label">
                                    Admins
                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- Inactive -->

                    <div class="col-12 col-md-6 col-xl-3">

                        <div class="stat-card stat-red">

                            <div class="stat-icon">
                                <i class="bi bi-person-slash"></i>
                            </div>

                            <div>

                                <div class="stat-number">
                                    <?= count(array_filter($users, fn($u) => ($u["status"] ?? "") === "Inactive")) ?>

                                </div>

                                <div class="stat-label">
                                    Inactive Users
                                </div>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ==================================
                 USERS
            =================================== -->

                <section class="users-card">


                    <!-- Users Header -->

                    <div class="users-header">

                        <div class="users-title-area">

                            <i class="bi bi-people-fill users-title-icon"></i>

                            <div>

                                <h2 class="users-title">
                                    Users
                                </h2>

                                <div class="users-subtitle">
                                    View and manage all registered users
                                </div>

                            </div>

                        </div>


                        <div class="users-actions">


                            <!-- Table Search -->

                            <div class="table-search">
                                <form action="Pages/users.php" method="GET" class="d-flex gap-2">

                                    <input
                                        type="text"
                                        name="search"
                                        placeholder="Search users..."
                                        id="tableSearch"
                                        class="form-control">

                                    <button type="submit" class="btn btn-primary">
                                        Search
                                    </button>

                                </form>
                            </div>



                        </div>

                    </div>



                    <!-- Table -->

                    <div class="table-container">

                        <div class="table-responsive">

                            <table
                                class="table user-table align-middle"
                                id="usersTable">

                                <thead>

                                    <tr>

                                        <th>ID</th>

                                        <th>Name</th>

                                        <th>Email</th>

                                        <th>Role</th>

                                        <th>Status</th>

                                        <th class="text-end">Joined At</th>

                                        <?php if ($_SESSION['role'] === 'Admin') { ?>

                                            <th class="text-end">
                                                Action
                                            </th>

                                        <?php } ?>

                                    </tr>

                                </thead>


                                <tbody>


                                    <?php foreach ($users as $u) : ?>


                                        <?php

                                        // Get initials

                                        $nameParts = explode(
                                            " ",
                                            trim($u["name"])
                                        );

                                        $initials = "";

                                        foreach ($nameParts as $part) {
                                            $initials .= strtoupper(
                                                substr($part, 0, 1)
                                            );
                                        }

                                        $initials = substr(
                                            $initials,
                                            0,
                                            2
                                        );


                                        // Role class

                                        $roleClass = "role-user";

                                        if ($u["role"] === "Admin") {
                                            $roleClass = "role-admin";
                                        }

                                        if ($u["role"] === "User") {
                                            $roleClass = "role-moderator";
                                        }


                                        // Status class

                                        $statusClass =
                                            $u["status"] === "Active"
                                            ? "status-active"
                                            : "status-inactive";

                                        ?>


                                        <tr>


                                            <!-- ID -->

                                            <td class="user-id">

                                                <?= $u["id"] ?>

                                            </td>



                                            <!-- Name -->

                                            <td>

                                                <div class="user-info">

                                                    <div class="user-avatar">

                                                        <?= htmlspecialchars($initials) ?>

                                                    </div>

                                                    <span class="user-name text-uppercase">

                                                        <?= htmlspecialchars($u["name"]) ?>

                                                    </span>

                                                </div>

                                            </td>



                                            <!-- Email -->

                                            <td>

                                                <span class="user-email">

                                                    <?= htmlspecialchars($u["email"]) ?>

                                                </span>

                                            </td>



                                            <!-- Role -->

                                            <td>

                                                <span
                                                    class="role-badge <?= $roleClass ?>">

                                                    <?php if ($u["role"] === "Admin") : ?>

                                                        <i class="bi bi-crown-fill"></i>

                                                    <?php elseif ($u["role"] === "User") : ?>

                                                        <i class="bi bi-shield-fill"></i>

                                                    <?php else : ?>

                                                        <i class="bi bi-circle-fill"></i>

                                                    <?php endif; ?>


                                                    <?= htmlspecialchars($u["role"]) ?>

                                                </span>

                                            </td>



                                            <!-- Status -->

                                            <td>

                                                <span
                                                    class="status-badge <?= $statusClass ?>">

                                                    <span class="status-dot"></span>

                                                    <?= htmlspecialchars($u["status"]) ?>

                                                </span>

                                            </td>



                                            <!-- Joined -->

                                            <td>

                                                <?= date("d-m-y", strtotime($u["created_at"])) ?>

                                            </td>



                                            <!-- Actions -->

                                            <td>

                                                <div class="action-buttons">


                                                    <?php if ($_SESSION['role'] === 'Admin') { ?>



                                                        <!-- View -->

                                                        <a
                                                            href="./Pages/user_info.php?id=<?= $u["id"] ?>"
                                                            class="action-btn action-view"
                                                            title="View User">

                                                            <i class="bi bi-eye-fill"></i>

                                                        </a>



                                                        <!-- Edit -->

                                                        <a
                                                            href="./Pages/edit.php?id=<?= $u["id"] ?>"
                                                            class="action-btn action-edit"
                                                            title="Edit User">

                                                            <i class="bi bi-pencil-fill"></i>

                                                        </a>



                                                        <!-- Delete -->

                                                        <a
                                                            href="./Pages/delete.php?id=<?= $u["id"] ?>"
                                                            class="action-btn action-delete"
                                                            title="Delete User"
                                                            onclick="
                                                    return confirm(
                                                        'Are you sure you want to delete this user?'
                                                    );
                                                ">

                                                            <i class="bi bi-trash3-fill"></i>

                                                        </a>

                                                    <?php } ?>


                                                </div>

                                            </td>


                                        </tr>


                                    <?php endforeach; ?>


                                </tbody>

                            </table>

                        </div>

                    </div>



                    <!-- ==================================
                     TABLE FOOTER
                =================================== -->

                    <div class="table-footer">

                        <div class="showing-text">

                            Showing 1 to 5 of
                            <?= count($users) ?>
                            users

                        </div>


                        <!-- <nav>

                            <ul class="pagination">


                                <li class="page-item disabled">

                                    <a
                                        class="page-link"
                                        href="#">

                                        <i class="bi bi-chevron-left"></i>

                                    </a>

                                </li>


                                <li class="page-item active">

                                    <a
                                        class="page-link"
                                        href="#">
                                        1
                                    </a>

                                </li>


                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="#">
                                        2
                                    </a>

                                </li>


                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="#">
                                        3
                                    </a>

                                </li>


                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="#">
                                        4
                                    </a>

                                </li>


                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="#">
                                        5
                                    </a>

                                </li>


                                <li class="page-item">

                                    <a
                                        class="page-link"
                                        href="#">

                                        <i class="bi bi-chevron-right"></i>

                                    </a>

                                </li>


                            </ul>

                        </nav> -->

                    </div>


                </section>



                <!-- ==================================
                 FOOTER
            =================================== -->

                <footer class="dashboard-footer">

                    <div>
                        © 2025 User Management. All rights reserved.
                    </div>

                    <div class="footer-right">
                        Simple. Secure. Powerful.
                    </div>

                </footer>


            </div>

        </main>

    </div>






    <!-- Main Js  -->
    <script src="./Assets/JS/script.js"></script>

    <!-- Bootstrap Js  -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>