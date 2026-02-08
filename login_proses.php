<?php
session_start();
include('keneksi.php');

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek admin di database
    $query = "SELECT * FROM tb_admin WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        die("Error query: " . mysqli_error($conn));
    }
    
    $admin = mysqli_fetch_assoc($result);

    // Jika username ada dan password cocok
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: admin_dashboard.php?tab=dashboard");
        exit;
    } else {
        echo "<script>alert('Login gagal, cek kembali username atau password.'); window.location='login.php';</script>";
    }
} else {
    echo "<script>alert('Mohon masukkan username dan password.'); window.location='login.php';</script>";
}
?>
