<?php
// actions.php
include 'config.php';

$action = $_POST['action'] ?? '';

if($action == 'reserve'){
    checkLogin();
    $user_id = $_SESSION['user_id'];
    $lot_id = $_POST['lot_id'];
    
    // Captured Fields
    $contact = $_POST['contact_number'];
    $birth = $_POST['birth_date'];
    $address = $_POST['address'];
    
    // Helper function for uploads
    function uploadFile($fileInputName){
        $target_dir = "uploads/";
        if(!is_dir($target_dir)) mkdir($target_dir);
        $filename = time() . "_" . basename($_FILES[$fileInputName]["name"]);
        move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $target_dir . $filename);
        return $filename;
    }

    $valid_id_file = uploadFile('valid_id');
    $selfie_id_file = uploadFile('selfie_id');
    $proof_file = uploadFile('proof');

    $stmt = $conn->prepare("INSERT INTO reservations 
        (user_id, lot_id, contact_number, birth_date, buyer_address, payment_proof, valid_id_file, selfie_with_id, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'PENDING')");
    
    $stmt->bind_param("iissssss", $user_id, $lot_id, $contact, $birth, $address, $proof_file, $valid_id_file, $selfie_id_file);
    
    if($stmt->execute()){
        $conn->query("UPDATE lots SET status='RESERVED' WHERE id='$lot_id'");
        header("Location: index.php?msg=verification_submitted");
    } else {
        echo "Error: " . $conn->error;
    }
}

// --- ADMIN ACTIONS (Updated Redirects) ---

if(isset($_POST['action']) && $_POST['action'] == 'approve_res'){
    $res_id = $_POST['res_id'];
    $lot_id = $_POST['lot_id'];
    
    $conn->query("UPDATE reservations SET status='APPROVED' WHERE id='$res_id'");
    $conn->query("UPDATE lots SET status='SOLD' WHERE id='$lot_id'");
    
    // Redirect to reservation.php now
    header("Location: reservation.php?status=PENDING&msg=approved");
    exit();
}

if(isset($_POST['action']) && $_POST['action'] == 'reject_res'){
    $res_id = $_POST['res_id'];
    $row = $conn->query("SELECT lot_id FROM reservations WHERE id='$res_id'")->fetch_assoc();
    $lot_id = $row['lot_id'];
    
    $conn->query("UPDATE reservations SET status='REJECTED' WHERE id='$res_id'");
    $conn->query("UPDATE lots SET status='AVAILABLE' WHERE id='$lot_id'");
    
    // Redirect to reservation.php now
    header("Location: reservation.php?status=PENDING&msg=rejected");
    exit();
}
?>