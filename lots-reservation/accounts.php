<?php
// accounts.php
include 'config.php';

// Security Check: Only Admin
if(!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['SUPER ADMIN', 'ADMIN', 'MANAGER'])){
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = "";

// --- ACTIONS ---

// 0. Create Account
if(isset($_POST['action']) && $_POST['action'] == 'create_account'){
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = md5($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($check->num_rows > 0){
        $msg = "Email is already registered.";
        $msg_type = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (fullname, phone, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $phone, $email, $password, $role);
        if($stmt->execute()){
            $msg = "New account created successfully.";
            $msg_type = "success";
        } else {
            $msg = "Failed to create account.";
            $msg_type = "error";
        }
    }
}

// 1. Delete User
if(isset($_POST['action']) && $_POST['action'] == 'delete'){
    $id = $_POST['user_id'];
    if($id != $_SESSION['user_id']){
        $conn->query("DELETE FROM users WHERE id='$id'");
        $msg = "User account deleted successfully.";
        $msg_type = "success";
    } else {
        $msg = "You cannot delete your own account.";
        $msg_type = "error";
    }
}

// 2. Change Role
if(isset($_POST['action']) && $_POST['action'] == 'change_role'){
    $id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    if($id != $_SESSION['user_id']){
        $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->bind_param("si", $new_role, $id);
        $stmt->execute();
        $msg = "User role updated to $new_role.";
        $msg_type = "success";
    } else {
        $msg = "You cannot change your own role.";
        $msg_type = "error";
    }
}

// Fetch Users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Management | JEJ Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800;900&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/modern.css">

    <style>
        body { background-color: #F7FAFC; display: flex; min-height: 100vh; overflow-x: hidden; }

        /* Layout & Sidebar */
        .sidebar { width: 260px; background: white; border-right: 1px solid #EDF2F7; display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; }
        .brand-box { padding: 25px; border-bottom: 1px solid #EDF2F7; display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { padding: 20px 10px; flex: 1; }
        .menu-link { display: flex; align-items: center; gap: 12px; padding: 14px 20px; color: #718096; text-decoration: none; font-weight: 600; border-radius: 12px; margin-bottom: 5px; transition: all 0.2s; }
        .menu-link:hover, .menu-link.active { background: #F0FFF4; color: var(--primary); }
        .menu-link i { width: 20px; text-align: center; }

        .main-panel { margin-left: 260px; flex: 1; padding: 30px 40px; width: calc(100% - 260px); }

        /* Table */
        .table-container { background: white; border-radius: 16px; border: 1px solid #EDF2F7; box-shadow: var(--shadow-soft); overflow: hidden; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 15px 25px; font-size: 11px; font-weight: 700; color: #718096; text-transform: uppercase; background: #F7FAFC; border-bottom: 1px solid #EDF2F7; }
        td { padding: 15px 25px; border-bottom: 1px solid #EDF2F7; color: #4A5568; font-size: 13px; vertical-align: middle; }
        tr:hover td { background: #FCFFFF; }

        /* Badges */
        .role-badge { padding: 4px 10px; border-radius: 50px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .role-ADMIN { background: #E9D8FD; color: #553C9A; }
        .role-BUYER { background: #BEE3F8; color: #2C5282; }

        /* Buttons */
        .btn-mini { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 11px; font-weight: 700; color: white; transition: 0.2s; }
        .btn-del { background: #FC8181; } .btn-del:hover { background: #C53030; }
        .btn-promote { background: #68D391; } .btn-promote:hover { background: #2F855A; }
        .btn-demote { background: #F6AD55; } .btn-demote:hover { background: #C05621; }

        /* Alert */
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .alert.success { background: #F0FFF4; color: #2F855A; border: 1px solid #C6F6D5; }
        .alert.error { background: #FFF5F5; color: #C53030; border: 1px solid #FED7D7; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand-box">
            <img src="assets/logo.png" style="height: 40px; width: auto; border-radius: 6px; margin-right: 10px;">
            <span style="font-size: 18px; font-weight: 800; color: var(--primary);">JEJ Admin</span>
        </div>

        <div class="sidebar-menu">
            <small style="padding: 0 20px; color: #A0AEC0; font-weight: 700; font-size: 11px; display: block; margin-bottom: 10px;">MAIN MENU</small>
            <a href="admin.php?view=dashboard" class="menu-link"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
            <a href="reservation.php" class="menu-link"><i class="fa-solid fa-file-signature"></i> Reservations</a>
            <a href="admin.php?view=inventory" class="menu-link"><i class="fa-solid fa-list-check"></i> Inventory</a>

            <small style="padding: 0 20px; color: #A0AEC0; font-weight: 700; font-size: 11px; display: block; margin-top: 20px; margin-bottom: 10px;">MANAGEMENT</small>
            <a href="accounts.php" class="menu-link active"><i class="fa-solid fa-users-gear"></i> Accounts</a>

            <small style="padding: 0 20px; color: #A0AEC0; font-weight: 700; font-size: 11px; display: block; margin-top: 20px; margin-bottom: 10px;">SYSTEM</small>
            <a href="index.php" class="menu-link" target="_blank"><i class="fa-solid fa-globe"></i> View Website</a>
            <a href="logout.php" class="menu-link" style="color: #E53E3E;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
        </div>
    </div>

    <div class="main-panel">
        <div style="margin-bottom: 20px;">
            <h1 style="font-size: 24px; font-weight: 800; color: var(--dark);">Account Management</h1>
            <p style="color: #718096;">Manage registered users and administrators.</p>
        </div>

        <?php if($msg): ?>
            <div class="alert <?= $msg_type ?>">
                <i class="fa-solid <?= $msg_type=='success'?'fa-circle-check':'fa-circle-exclamation' ?>"></i>
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <div class="table-container" style="padding: 20px; border-radius: 12px; margin-bottom: 20px; background: white;">
            <h3 style="margin-bottom: 15px; color: #4A5568; font-size: 16px;">Create New Account</h3>
            <form method="POST" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
                <input type="hidden" name="action" value="create_account">

                <div style="flex: 1; min-width: 150px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#718096; margin-bottom:5px;">Full Name</label>
                    <input type="text" name="fullname" required style="width:100%; padding:10px; border:1px solid #E2E8F0; border-radius:6px; font-size:14px;">
                </div>

                <div style="flex: 1; min-width: 150px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#718096; margin-bottom:5px;">Email</label>
                    <input type="email" name="email" required style="width:100%; padding:10px; border:1px solid #E2E8F0; border-radius:6px; font-size:14px;">
                </div>

                <div style="flex: 1; min-width: 120px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#718096; margin-bottom:5px;">Phone</label>
                    <input type="text" name="phone" style="width:100%; padding:10px; border:1px solid #E2E8F0; border-radius:6px; font-size:14px;">
                </div>

                <div style="flex: 1; min-width: 120px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#718096; margin-bottom:5px;">Password</label>
                    <input type="password" name="password" required style="width:100%; padding:10px; border:1px solid #E2E8F0; border-radius:6px; font-size:14px;">
                </div>

                <div style="flex: 1; min-width: 120px;">
                    <label style="display:block; font-size:12px; font-weight:700; color:#718096; margin-bottom:5px;">Role</label>
                    <select name="role" required style="width:100%; padding:10px; border:1px solid #E2E8F0; border-radius:6px; font-size:14px;">
                        <option value="BUYER">BUYER</option>
                        <option value="AGENT">AGENT</option>
                        <option value="MANAGER">MANAGER</option>
                        <option value="ADMIN">ADMIN</option>
                        <option value="SUPER ADMIN">SUPER ADMIN</option>
                    </select>
                </div>

                <div>
                    <button type="submit" style="background:var(--primary); color:white; border:none; padding:11px 20px; border-radius:6px; font-weight:700; font-size:13px; cursor:pointer;"><i class="fa-solid fa-plus"></i> Create</button>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Details</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td style="font-weight: 700; color: #A0AEC0;">#<?= $row['id'] ?></td>
                        <td>
                            <strong style="color: var(--dark); font-size: 14px;"><?= htmlspecialchars($row['fullname']) ?></strong><br>
                            <span style="color: #718096; font-size: 12px;"><?= htmlspecialchars($row['email']) ?></span>
                        </td>
                        <td>
                            <span class="role-badge role-<?= $row['role'] ?: 'BUYER' ?>">
                                <?= $row['role'] ?: 'BUYER' ?>
                            </span>
                        </td>
                        <td>
                            <?php if($row['id'] != $_SESSION['user_id']): ?>
                                <div style="display: flex; gap: 5px;">
                                    <form method="POST" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                        <select name="new_role" style="padding: 5px; border: 1px solid #E2E8F0; border-radius: 4px; font-size: 11px; font-weight: 700; color: #4A5568;">
                                            <option value="BUYER" <?= $row['role'] == 'BUYER' ? 'selected' : '' ?>>BUYER</option>
                                            <option value="AGENT" <?= $row['role'] == 'AGENT' ? 'selected' : '' ?>>AGENT</option>
                                            <option value="MANAGER" <?= $row['role'] == 'MANAGER' ? 'selected' : '' ?>>MANAGER</option>
                                            <option value="ADMIN" <?= $row['role'] == 'ADMIN' ? 'selected' : '' ?>>ADMIN</option>
                                            <option value="SUPER ADMIN" <?= $row['role'] == 'SUPER ADMIN' ? 'selected' : '' ?>>SUPER ADMIN</option>
                                        </select>
                                        <button class="btn-mini btn-promote" title="Update Role" style="padding: 5px 10px;"><i class="fa-solid fa-check"></i></button>
                                    </form>

                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                        <button class="btn-mini btn-del" title="Delete User"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span style="font-size: 11px; color: #A0AEC0; font-style: italic;">(Current User)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>