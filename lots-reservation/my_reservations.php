<?php
// my_reservations.php
include 'config.php';

// Only allow logged in users who are NOT admins
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT r.*, l.block_no, l.lot_no, l.property_type, l.total_price, l.lot_image, p.name as phase_name
          FROM reservations r
          JOIN lots l ON r.lot_id = l.id
          LEFT JOIN phases p ON l.phase_id = p.id
          WHERE r.user_id = ?
          ORDER BY r.reservation_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations | JEJ Surveying Services</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700;800;900&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="assets/modern.css">
</head>
<body style="background-color: #F7FAFC;">

    <nav class="nav">
        <div class="brand-wrapper">
            <a href="index.php" style="display: flex; align-items: center; gap: 10px;">
                <img src="assets/logo.png" alt="JEJ Logo" style="height: 45px; width: auto; border-radius: 6px;">
                <span class="nav-brand">JEJ Surveying Services</span>
            </a>
        </div>

        <div class="nav-links desktop-only">
            <a href="index.php">Properties</a>
            <a href="my_reservations.php" class="active">My Reservations</a>
        </div>

        <div class="user-menu">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="user-details">
                    <span class="user-name"><?= htmlspecialchars($_SESSION['fullname']) ?></span>
                    <span class="user-role"><?= $_SESSION['role'] ?></span>
                </div>
                <div class="avatar-circle">
                    <?= strtoupper(substr($_SESSION['fullname'], 0, 1)) ?>
                </div>
                <a href="logout.php" style="color: #E53E3E; margin-left:8px; font-size:16px;" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px; min-height: 60vh;">
        <h2 class="section-title"><i class="fa-solid fa-book-bookmark" style="color: var(--primary); margin-right: 10px;"></i> My Reservations</h2>

        <?php if($result->num_rows > 0): ?>
            <div class="table-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: var(--shadow-soft);">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; background: #EDF2F7; color: #4A5568; font-size: 13px; text-transform: uppercase;">
                            <th style="padding: 15px;">Property</th>
                            <th style="padding: 15px;">Date Reserved</th>
                            <th style="padding: 15px;">Total Price</th>
                            <th style="padding: 15px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr style="border-bottom: 1px solid #E2E8F0;">
                            <td style="padding: 15px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <img src="<?= $row['lot_image'] ? 'uploads/'.$row['lot_image'] : 'assets/default_lot.jpg' ?>" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover;">
                                    <div>
                                        <strong>Block <?= $row['block_no'] ?>, Lot <?= $row['lot_no'] ?></strong>
                                        <div style="font-size: 12px; color: #718096;"><?= $row['phase_name'] ?> - <?= $row['property_type'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 15px; font-size: 14px; color: #4A5568;">
                                <?= date('F d, Y h:i A', strtotime($row['reservation_date'])) ?>
                            </td>
                            <td style="padding: 15px; font-weight: 600; font-family: 'Open Sans', sans-serif;">
                                ₱<?= number_format($row['total_price']) ?>
                            </td>
                            <td style="padding: 15px;">
                                <?php
                                    $badges = [
                                        'PENDING'  => ['bg'=>'#FEFCBF', 'col'=>'#975A16'],
                                        'APPROVED' => ['bg'=>'#C6F6D5', 'col'=>'#22543D'],
                                        'CANCELLED'=> ['bg'=>'#FED7D7', 'col'=>'#822727']
                                    ];
                                    $b = $badges[$row['status']] ?? $badges['PENDING'];
                                ?>
                                <span style="background: <?= $b['bg'] ?>; color: <?= $b['col'] ?>; padding: 5px 12px; border-radius: 6px; font-size: 11px; font-weight: 800; display: inline-block;">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; color: #A0AEC0; background: #fff; border-radius: 16px; box-shadow: var(--shadow-soft); border: 1px solid #EDF2F7;">
                <i class="fa-solid fa-folder-open" style="font-size: 40px; margin-bottom: 20px; color: #CBD5E0;"></i>
                <h3 style="color:#4A5568; margin-bottom: 5px;">No reservations found</h3>
                <p style="font-size: 14px;">You haven't made any lot reservations yet.</p>
                <a href="index.php" style="color: white; background: var(--primary); padding: 10px 20px; border-radius: 8px; font-weight: 700; margin-top: 15px; display: inline-block; text-decoration: none; font-size: 14px;">Browse Properties</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div style="margin-bottom: 25px;">
            <img src="assets/logo.png" alt="JEJ Logo" style="height: 60px; width: auto; border-radius: 8px; background: white; padding: 5px;">
        </div>
        <p><strong>JEJ Surveying Services</strong></p>
        <p style="opacity:0.6; margin-top:10px; font-size: 14px;">Professional surveying and blueprint solutions.</p>
        <div style="margin-top: 40px; font-size: 12px; opacity: 0.4;">
            &copy; <?= date('Y') ?> All Rights Reserved.
        </div>
    </footer>

</body>
</html>
