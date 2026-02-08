<?php
// Simple visitor tracking function
function trackVisitor($page_name, $is_admin = false) {
    include('keneksi.php');
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $visit_date = date('Y-m-d');
    
    // Check if this IP already visited today for this page
    $check_query = "SELECT id FROM tb_visitor_stats WHERE ip_address = ? AND page_visited = ? AND visit_date = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "sss", $ip_address, $page_name, $visit_date);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    
    // Only insert if not already recorded today
    if (mysqli_num_rows($result) == 0) {
        $insert_query = "INSERT INTO tb_visitor_stats (ip_address, user_agent, page_visited, visit_date, is_admin) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ssssi", $ip_address, $user_agent, $page_name, $visit_date, $is_admin);
        mysqli_stmt_execute($insert_stmt);
    }
    
    mysqli_close($conn);
}

// Auto-detect page and track
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

trackVisitor($current_page, $is_admin);
?>
