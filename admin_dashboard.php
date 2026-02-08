<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include('keneksi.php');

// Check if tables exist
$check_tables = [
    "SHOW TABLES LIKE 'tb_content'",
    "SHOW TABLES LIKE 'tb_facilities'"
];

$tables_exist = true;
$missing_tables = [];

foreach ($check_tables as $i => $query) {
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 0) {
        $tables_exist = false;
        $missing_tables[] = ($i == 0) ? 'tb_content' : 'tb_facilities';
    }
}

if (!$tables_exist) {
    ?>
    <div class="container mx-auto p-6">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>Database belum diinisialisasi. Tabel berikut hilang: <?php echo implode(', ', $missing_tables); ?>. Silakan jalankan <a href="initialize_database.php" class="underline font-bold">initialize_database.php</a> untuk membuat tabel yang diperlukan.</span>
            </div>
        </div>
    <?php
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_content'])) {
        $content_id = $_POST['content_id'];
        $content_value = $_POST['content_value'];
        
        $query = "UPDATE tb_content SET content_value = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $content_value, $content_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Konten berhasil diupdate!";
            header("Location: admin_dashboard.php?tab=content&success=update");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['update_facility'])) {
        $facility_id = $_POST['facility_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $image = $_POST['image'];
        
        $query = "UPDATE tb_facilities SET name = ?, description = ?, image = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $description, $image, $facility_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Fasilitas berhasil diupdate!";
            header("Location: admin_dashboard.php?tab=facilities&success=2");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['add_facility'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $image = $_POST['image'];
        
        $query = "INSERT INTO tb_facilities (name, description, image) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $name, $description, $image);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Fasilitas berhasil ditambahkan!";
            header("Location: admin_dashboard.php?tab=facilities&success=1");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['delete_facility'])) {
        $facility_id = $_POST['facility_id'];
        
        $query = "DELETE FROM tb_facilities WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $facility_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Fasilitas berhasil dihapus!";
            header("Location: admin_dashboard.php?tab=facilities&success=delete");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    // Handle user management
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $query = "INSERT INTO tb_admin (username, password) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "User berhasil ditambahkan!";
            header("Location: admin_dashboard.php?tab=users&success=add");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "UPDATE tb_admin SET username = ?, password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $username, $password, $user_id);
        } else {
            $query = "UPDATE tb_admin SET username = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $username, $user_id);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "User berhasil diupdate!";
            header("Location: admin_dashboard.php?tab=users&success=update");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        
        // Prevent deleting current logged-in user
        if (isset($_SESSION['admin_id']) && $user_id == $_SESSION['admin_id']) {
            $error_message = "Tidak dapat menghapus akun yang sedang digunakan!";
        } else {
            $query = "DELETE FROM tb_admin WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "User berhasil dihapus!";
                header("Location: admin_dashboard.php?tab=users&success=delete");
                exit;
            } else {
                $error_message = "Error: " . mysqli_error($conn);
            }
        }
    }
    
    // Handle file upload
    if (isset($_POST['upload_image'])) {
        $target_dir = "asset/";
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        
        // Generate unique filename
        $unique_name = uniqid() . '_' . time() . '.' . $imageFileType;
        $target_file = $target_dir . $unique_name;
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            $uploadOk = 1;
        } else {
            $error_message = "File bukan gambar yang valid.";
            $uploadOk = 0;
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES["image"]["size"] > 5000000) {
            $error_message = "File terlalu besar. Maksimal 5MB.";
            $uploadOk = 0;
        }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "webp") {
            $error_message = "Hanya file JPG, JPEG, PNG, GIF & WEBP yang diizinkan.";
            $uploadOk = 0;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            if (!isset($error_message)) {
                $error_message = "Upload gagal.";
            }
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $success_message = "File " . htmlspecialchars(basename($_FILES["image"]["name"])) . " berhasil diupload sebagai " . $unique_name;
                header("Location: admin_dashboard.php?tab=upload&success=upload");
                exit;
            } else {
                $error_message = "Terjadi error saat upload file.";
            }
        }
    }

    // Handle file deletion
    if (isset($_POST['delete_image'])) {
        $image_path = $_POST['image_path'];
        $full_path = $image_path;
        
        // Security check - only allow deletion from asset/ folder
        if (strpos($image_path, 'asset/') === 0 && file_exists($full_path)) {
            if (unlink($full_path)) {
                $success_message = "File berhasil dihapus.";
                header("Location: admin_dashboard.php?tab=upload&success=delete");
                exit;
            } else {
                $error_message = "Gagal menghapus file.";
            }
        } else {
            $error_message = "File tidak ditemukan atau tidak diizinkan untuk dihapus.";
        }
    }
    
    // Handle kritik & saran deletion
    if (isset($_POST['delete_kritik_saran'])) {
        $kritik_id = $_POST['kritik_id'];
        
        $query = "DELETE FROM tb_kritik_saran WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $kritik_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Kritik & Saran berhasil dihapus!";
            header("Location: admin_dashboard.php?tab=kritik-saran&success=delete");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    // Handle Virtual Tour management
    if (isset($_POST['add_scene'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $scene_key = $_POST['scene_key'];
        $image_360 = $_POST['image_360'];
        $icon = $_POST['icon'];
        
        $query = "INSERT INTO tb_vr_scenes (name, description, scene_key, image_360, icon) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssss", $name, $description, $scene_key, $image_360, $icon);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Scene VR berhasil ditambahkan!";
            header("Location: admin_dashboard.php?tab=virtual-tour&success=add_scene");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['update_scene'])) {
        $scene_id = $_POST['scene_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $scene_key = $_POST['scene_key'];
        $image_360 = $_POST['image_360'];
        $icon = $_POST['icon'];
        
        $query = "UPDATE tb_vr_scenes SET name = ?, description = ?, scene_key = ?, image_360 = ?, icon = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $description, $scene_key, $image_360, $icon, $scene_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Scene VR berhasil diupdate!";
            header("Location: admin_dashboard.php?tab=virtual-tour&success=update_scene");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['delete_scene'])) {
        $scene_id = $_POST['scene_id'];
        
        // Delete related hotspots first
        $delete_hotspots = "DELETE FROM tb_vr_hotspots WHERE scene_id = ?";
        $stmt1 = mysqli_prepare($conn, $delete_hotspots);
        mysqli_stmt_bind_param($stmt1, "i", $scene_id);
        mysqli_stmt_execute($stmt1);
        
        // Delete scene
        $query = "DELETE FROM tb_vr_scenes WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $scene_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Scene VR berhasil dihapus!";
            header("Location: admin_dashboard.php?tab=virtual-tour&success=delete_scene");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['add_hotspot'])) {
        $scene_id = $_POST['scene_id'];
        $name = $_POST['name'];
        $target_scene = $_POST['target_scene'];
        $position_x = $_POST['position_x'];
        $position_y = $_POST['position_y'];
        $position_z = $_POST['position_z'];
        
        $query = "INSERT INTO tb_vr_hotspots (scene_id, name, target_scene, position_x, position_y, position_z) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "issddd", $scene_id, $name, $target_scene, $position_x, $position_y, $position_z);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Hotspot berhasil ditambahkan!";
            header("Location: admin_dashboard.php?tab=virtual-tour&success=add_hotspot");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['delete_hotspot'])) {
        $hotspot_id = $_POST['hotspot_id'];
        
        $query = "DELETE FROM tb_vr_hotspots WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $hotspot_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Hotspot berhasil dihapus!";
            header("Location: admin_dashboard.php?tab=virtual-tour&success=delete_hotspot");
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

// Get all content
$content_query = "SELECT * FROM tb_content ORDER BY section, content_key";
$content_result = mysqli_query($conn, $content_query);

// Get all facilities
$facilities_query = "SELECT * FROM tb_facilities ORDER BY created_at DESC";
$facilities_result = mysqli_query($conn, $facilities_query);

// Get all admin users
$users_query = "SELECT * FROM tb_admin ORDER BY id DESC";
$users_result = mysqli_query($conn, $users_query);

// Get VR scenes
$vr_scenes_query = "SELECT * FROM tb_vr_scenes ORDER BY id";
$vr_scenes_result = mysqli_query($conn, $vr_scenes_query);

// Get VR hotspots with scene names
$vr_hotspots_query = "SELECT h.*, s.name as scene_name FROM tb_vr_hotspots h LEFT JOIN tb_vr_scenes s ON h.scene_id = s.id ORDER BY h.scene_id, h.id";
$vr_hotspots_result = mysqli_query($conn, $vr_hotspots_query);

// Get kritik & saran
$kritik_saran_query = "SELECT * FROM tb_kritik_saran ORDER BY created_at DESC";
$kritik_saran_result = mysqli_query($conn, $kritik_saran_query);

// Get dashboard statistics
$dashboard_stats = [];

// Total unique visitors (non-admin)
$visitor_query = "SELECT COUNT(DISTINCT ip_address) as total_visitors FROM tb_visitor_stats WHERE is_admin = 0";
$visitor_result = mysqli_query($conn, $visitor_query);
$dashboard_stats['total_visitors'] = $visitor_result ? mysqli_fetch_assoc($visitor_result)['total_visitors'] : 0;

// Virtual tour visitors
$vr_visitor_query = "SELECT COUNT(DISTINCT ip_address) as vr_visitors FROM tb_visitor_stats WHERE page_visited = 'virtual_tour.php' AND is_admin = 0";
$vr_visitor_result = mysqli_query($conn, $vr_visitor_query);
$dashboard_stats['vr_visitors'] = $vr_visitor_result ? mysqli_fetch_assoc($vr_visitor_result)['vr_visitors'] : 0;

// Total kritik & saran
$kritik_count_query = "SELECT COUNT(*) as kritik_count FROM tb_kritik_saran";
$kritik_count_result = mysqli_query($conn, $kritik_count_query);
$dashboard_stats['kritik_count'] = $kritik_count_result ? mysqli_fetch_assoc($kritik_count_result)['kritik_count'] : 0;

// Today's visitors
$today = date('Y-m-d');
$today_visitor_query = "SELECT COUNT(DISTINCT ip_address) as today_visitors FROM tb_visitor_stats WHERE visit_date = '{$today}' AND is_admin = 0";
$today_visitor_result = mysqli_query($conn, $today_visitor_query);
$dashboard_stats['today_visitors'] = $today_visitor_result ? mysqli_fetch_assoc($today_visitor_result)['today_visitors'] : 0;

// Recent visitors
$recent_visitors_query = "SELECT DISTINCT ip_address, page_visited, visit_time FROM tb_visitor_stats WHERE is_admin = 0 ORDER BY visit_time DESC LIMIT 10";
$recent_visitors_result = mysqli_query($conn, $recent_visitors_query);

// Check if created_at column exists
$check_created_at = "SHOW COLUMNS FROM tb_admin LIKE 'created_at'";
$created_at_exists = mysqli_num_rows(mysqli_query($conn, $check_created_at)) > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Virtual Tour UNPAM</title>
    <link rel="icon" type="image/png" href="asset/logo-unpam-300x291.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="asset/logo-unpam-300x291.png" alt="Logo UNPAM" class="h-12 w-auto">
                <h1 class="text-2xl font-bold">Admin Dashboard - Virtual Tour UNPAM</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span>Selamat datang, Admin!</span>
                <a href="index.php" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded" target="_blank">
                    <i class="fas fa-external-link-alt mr-2"></i>Lihat Website
                </a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-700 px-4 py-2 rounded">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container mx-auto p-6">
        <?php if (isset($success_message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (!$created_at_exists): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>Database perlu diupdate untuk menampilkan tanggal pembuatan user. </span>
                    <a href="update_database.php" class="ml-2 bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm">
                        Update Sekarang
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="border-b">
                <nav class="flex space-x-8">
                    <button onclick="showTab('dashboard')" id="dashboard-tab" class="tab-button py-4 px-6 text-blue-600 border-b-2 border-blue-600 font-medium">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </button>
                    <button onclick="showTab('content')" id="content-tab" class="tab-button py-4 px-6 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-edit mr-2"></i>Kelola Konten
                    </button>
                    <button onclick="showTab('facilities')" id="facilities-tab" class="tab-button py-4 px-6 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-building mr-2"></i>Kelola Fasilitas
                    </button>
                    <button onclick="showTab('users')" id="users-tab" class="tab-button py-4 px-6 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-users mr-2"></i>Kelola User
                    </button>
                    <button onclick="showTab('virtual-tour')" id="virtual-tour-tab" class="tab-button py-4 px-6 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-vr-cardboard mr-2"></i>Virtual Tour
                    </button>
                    <button onclick="showTab('kritik-saran')" id="kritik-saran-tab" class="tab-button py-4 px-6 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-comments mr-2"></i>Kritik & Saran
                    </button>
                    <button onclick="showTab('upload')" id="upload-tab" class="tab-button py-4 px-6 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-upload mr-2"></i>Upload Gambar
                    </button>
                </nav>
            </div>

            <!-- Dashboard Tab -->
            <div id="dashboard-section" class="tab-content p-6">
                <h2 class="text-xl font-bold mb-6">Dashboard - Statistik Virtual Tour UNPAM</h2>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Visitors -->
                    <div class="bg-blue-500 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium opacity-90">Total Pengunjung</h3>
                                <p class="text-2xl font-bold"><?php echo number_format($dashboard_stats['total_visitors']); ?></p>
                                <p class="text-xs opacity-75 mt-1">Unique visitors (non-admin)</p>
                            </div>
                            <div class="text-3xl opacity-75">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Virtual Tour Visitors -->
                    <div class="bg-green-500 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium opacity-90">Akses Virtual Tour</h3>
                                <p class="text-2xl font-bold"><?php echo number_format($dashboard_stats['vr_visitors']); ?></p>
                                <p class="text-xs opacity-75 mt-1">Pengunjung VR</p>
                            </div>
                            <div class="text-3xl opacity-75">
                                <i class="fas fa-vr-cardboard"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kritik & Saran -->
                    <div class="bg-purple-500 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium opacity-90">Kritik & Saran</h3>
                                <p class="text-2xl font-bold"><?php echo number_format($dashboard_stats['kritik_count']); ?></p>
                                <p class="text-xs opacity-75 mt-1">Total feedback</p>
                            </div>
                            <div class="text-3xl opacity-75">
                                <i class="fas fa-comments"></i>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Today's Visitors -->
                    <div class="bg-orange-500 text-white p-6 rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium opacity-90">Hari Ini</h3>
                                <p class="text-2xl font-bold"><?php echo number_format($dashboard_stats['today_visitors']); ?></p>
                                <p class="text-xs opacity-75 mt-1">Pengunjung hari ini</p>
                            </div>
                            <div class="text-3xl opacity-75">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Conversion Rate -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-lg border">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">
                            <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                            Tingkat Konversi
                        </h3>
                        <div class="space-y-4">
                            <?php 
                            $vr_rate = $dashboard_stats['total_visitors'] > 0 ? ($dashboard_stats['vr_visitors'] / $dashboard_stats['total_visitors']) * 100 : 0;
                            $feedback_rate = $dashboard_stats['vr_visitors'] > 0 ? ($dashboard_stats['kritik_count'] / $dashboard_stats['vr_visitors']) * 100 : 0;
                            ?>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm text-gray-600">Akses Virtual Tour</span>
                                    <span class="text-sm font-semibold text-green-600"><?php echo number_format($vr_rate, 1); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo min($vr_rate, 100); ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Dari total pengunjung yang mengakses VR</p>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-sm text-gray-600">Memberikan Feedback</span>
                                    <span class="text-sm font-semibold text-purple-600"><?php echo number_format($feedback_rate, 1); ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-500 h-2 rounded-full" style="width: <?php echo min($feedback_rate, 100); ?>%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Dari pengunjung VR yang memberikan kritik/saran</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="bg-white p-6 rounded-lg shadow-lg border">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">
                            <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                            Quick Actions
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="showTab('kritik-saran')" class="bg-purple-500 hover:bg-purple-600 text-white p-3 rounded-lg text-sm transition-colors">
                                <i class="fas fa-comments mb-1"></i><br>
                                Lihat Feedback
                            </button>
                            <button onclick="showTab('virtual-tour')" class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg text-sm transition-colors">
                                <i class="fas fa-vr-cardboard mb-1"></i><br>
                                Kelola VR
                            </button>
                            <button onclick="showTab('upload')" class="bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-lg text-sm transition-colors">
                                <i class="fas fa-upload mb-1"></i><br>
                                Upload Gambar
                            </button>
                            <button onclick="showTab('content')" class="bg-orange-500 hover:bg-orange-600 text-white p-3 rounded-lg text-sm transition-colors">
                                <i class="fas fa-edit mb-1"></i><br>
                                Edit Konten
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="bg-white p-6 rounded-lg shadow-lg border">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        <i class="fas fa-history mr-2 text-gray-600"></i>
                        Aktivitas Pengunjung Terbaru
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left">Halaman</th>
                                    <th class="px-4 py-2 text-left">Waktu Akses</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php 
                                if ($recent_visitors_result && mysqli_num_rows($recent_visitors_result) > 0) {
                                    while ($visitor = mysqli_fetch_assoc($recent_visitors_result)): 
                                        $page_name = str_replace('.php', '', $visitor['page_visited']);
                                        $page_icon = '';
                                        $page_color = '';
                                        
                                        switch($page_name) {
                                            case 'virtual_tour':
                                                $page_icon = 'fas fa-vr-cardboard';
                                                $page_color = 'text-green-600';
                                                $page_name = 'Virtual Tour';
                                                break;
                                            case 'index':
                                                $page_icon = 'fas fa-home';
                                                $page_color = 'text-blue-600';
                                                $page_name = 'Homepage';
                                                break;
                                            case 'fasilitas':
                                                $page_icon = 'fas fa-building';
                                                $page_color = 'text-purple-600';
                                                $page_name = 'Fasilitas';
                                                break;
                                            default:
                                                $page_icon = 'fas fa-file';
                                                $page_color = 'text-gray-600';
                                        }
                                ?>
                                <tr class="hover:bg-gray-50">
                                    
                                    <td class="px-4 py-3">
                                        <span class="<?php echo $page_color; ?>">
                                            <i class="<?php echo $page_icon; ?> mr-1"></i>
                                            <?php echo $page_name; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        <?php echo date('d/m/Y H:i', strtotime($visitor['visit_time'])); ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                            <i class="fas fa-check-circle mr-1"></i>Visitor
                                        </span>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                } else {
                                    echo "<tr><td colspan='4' class='px-4 py-8 text-center text-gray-500'>Belum ada data pengunjung</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Content Management Tab -->
            <div id="content-section" class="tab-content p-6 hidden">
                <h2 class="text-xl font-bold mb-4">Kelola Konten Website</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php while ($content = mysqli_fetch_assoc($content_result)): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <form method="POST" class="space-y-3">
                                <input type="hidden" name="content_id" value="<?php echo $content['id']; ?>">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        <?php echo ucfirst($content['section']); ?> - <?php echo ucfirst(str_replace('_', ' ', $content['content_key'])); ?>
                                    </label>
                                    
                                    <?php if ($content['content_type'] == 'text' && strlen($content['content_value']) > 100): ?>
                                        <textarea name="content_value" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2"><?php echo htmlspecialchars($content['content_value']); ?></textarea>
                                    <?php else: ?>
                                        <input type="text" name="content_value" value="<?php echo htmlspecialchars($content['content_value']); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <?php endif; ?>
                                    
                                    <small class="text-gray-500">Tipe: <?php echo $content['content_type']; ?></small>
                                </div>
                                
                                <button type="submit" name="update_content" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                    <i class="fas fa-save mr-1"></i>Update
                                </button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Facilities Management Tab -->
            <div id="facilities-section" class="tab-content p-6 hidden">
                <h2 class="text-xl font-bold mb-4">Kelola Fasilitas</h2>
                
                <!-- Add New Facility Form -->
                <div class="bg-green-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-3">Tambah Fasilitas Baru</h3>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Fasilitas</label>
                            <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Contoh: Laboratorium Komputer">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="description" required class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Deskripsi fasilitas..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Gambar</label>
                            <input type="text" name="image" required class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="asset/gambar.jpg atau http://...">
                            <button type="submit" name="add_facility" class="mt-2 bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded w-full">
                                <i class="fas fa-plus mr-1"></i>Tambah
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Facilities -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php 
                    // Reset result pointer for displaying existing facilities
                    mysqli_data_seek($facilities_result, 0);
                    while ($facility = mysqli_fetch_assoc($facilities_result)): 
                    ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="mb-3">
                                <img src="<?php echo htmlspecialchars(isset($facility['image']) ? $facility['image'] : (isset($facility['image_path']) ? $facility['image_path'] : 'asset/default.jpg')); ?>" 
                                     alt="<?php echo htmlspecialchars(isset($facility['name']) ? $facility['name'] : (isset($facility['title']) ? $facility['title'] : 'Fasilitas')); ?>" 
                                     class="w-full h-32 object-cover rounded">
                            </div>
                            
                            <form method="POST" class="space-y-3">
                                <input type="hidden" name="facility_id" value="<?php echo $facility['id']; ?>">
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Fasilitas</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars(isset($facility['name']) ? $facility['name'] : (isset($facility['title']) ? $facility['title'] : '')); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2"><?php echo htmlspecialchars($facility['description']); ?></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Gambar</label>
                                    <input type="text" name="image" value="<?php echo htmlspecialchars(isset($facility['image']) ? $facility['image'] : (isset($facility['image_path']) ? $facility['image_path'] : '')); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button type="submit" name="update_facility" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded flex-1">
                                        <i class="fas fa-save mr-1"></i>Update
                                    </button>
                                    <button type="submit" name="delete_facility" onclick="return confirm('Yakin ingin menghapus fasilitas ini?')" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">
                                        <i class="fas fa-trash mr-1"></i>Hapus
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Users Management Tab -->
            <div id="users-section" class="tab-content p-6 hidden">
                <h2 class="text-xl font-bold mb-4">Kelola User Admin</h2>
                
                <!-- Add New User Form -->
                <div class="bg-green-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-3">Tambah User Admin Baru</h3>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" required class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Masukkan username">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Masukkan password">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" name="add_user" class="w-full bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                                <i class="fas fa-user-plus mr-1"></i>Tambah User
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Existing Users -->
                <div class="bg-white rounded-lg">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Daftar User Admin</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                            <div class="p-4">
                                <form method="POST" class="flex flex-col md:flex-row md:items-end gap-4">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    </div>
                                    
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru (kosongkan jika tidak diubah)</label>
                                        <input type="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Password baru (opsional)">
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <button type="submit" name="update_user" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                            <i class="fas fa-save mr-1"></i>Update
                                        </button>
                                        <button type="submit" name="delete_user" onclick="return confirm('Yakin ingin menghapus user ini?')" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">
                                            <i class="fas fa-trash mr-1"></i>Hapus
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="mt-2 text-sm text-gray-500">
                                    <?php if ($created_at_exists && isset($user['created_at'])): ?>
                                        <i class="fas fa-calendar mr-1"></i>
                                        Dibuat: <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                    <?php else: ?>
                                        <i class="fas fa-user mr-1"></i>
                                        ID: <?php echo $user['id']; ?>
                                    <?php endif; ?>
                                    <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                        <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">
                                            <i class="fas fa-user mr-1"></i>Anda
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Virtual Tour Management Tab -->
            <div id="virtual-tour-section" class="tab-content p-6 hidden">
                <h2 class="text-xl font-bold mb-4">Kelola Virtual Tour</h2>
                
                <!-- VR Scenes Management -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-3">Kelola Scene VR</h3>
                    
                    <!-- Add Scene Form -->
                    <div class="bg-white p-4 rounded-lg mb-4">
                        <h4 class="font-medium mb-3">Tambah Scene Baru</h4>
                        <form method="post" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Scene</label>
                                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Key Scene</label>
                                    <input type="text" name="scene_key" required placeholder="contoh: entrance" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">URL Gambar 360°</label>
                                    <input type="url" name="image_360" required placeholder="https://..." class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome)</label>
                                    <input type="text" name="icon" required placeholder="fas fa-door-open" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" required rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                            </div>
                            <button type="submit" name="add_scene" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                                <i class="fas fa-plus mr-1"></i>Tambah Scene
                            </button>
                        </form>
                    </div>
                    
                    <!-- Existing Scenes -->
                    <div class="space-y-4">
                        <?php 
                        if ($vr_scenes_result) {
                            mysqli_data_seek($vr_scenes_result, 0);
                            while ($scene = mysqli_fetch_assoc($vr_scenes_result)): 
                        ?>
                        <div class="bg-white p-4 rounded-lg border">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-lg"><?php echo htmlspecialchars($scene['name']); ?></h4>
                                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($scene['description']); ?></p>
                                    <div class="text-sm text-gray-500">
                                        <span class="mr-4">Key: <?php echo htmlspecialchars($scene['scene_key']); ?></span>
                                        <span class="mr-4">Icon: <i class="<?php echo htmlspecialchars($scene['icon']); ?>"></i></span>
                                        <span>ID: <?php echo $scene['id']; ?></span>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editScene(<?php echo $scene['id']; ?>, '<?php echo htmlspecialchars($scene['name']); ?>', '<?php echo htmlspecialchars($scene['description']); ?>', '<?php echo htmlspecialchars($scene['scene_key']); ?>', '<?php echo htmlspecialchars($scene['image_360']); ?>', '<?php echo htmlspecialchars($scene['icon']); ?>')" class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="post" class="inline" onsubmit="return confirm('Yakin ingin menghapus scene ini?')">
                                        <input type="hidden" name="scene_id" value="<?php echo $scene['id']; ?>">
                                        <button type="submit" name="delete_scene" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; } ?>
                    </div>
                </div>
                
                <!-- VR Hotspots Management -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Kelola Hotspots</h3>
                    
                    <!-- Add Hotspot Form -->
                    <div class="bg-white p-4 rounded-lg mb-4">
                        <h4 class="font-medium mb-3">Tambah Hotspot Baru</h4>
                        <form method="post" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Scene</label>
                                    <select name="scene_id" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">Pilih Scene</option>
                                        <?php 
                                        if ($vr_scenes_result) {
                                            mysqli_data_seek($vr_scenes_result, 0);
                                            while ($scene = mysqli_fetch_assoc($vr_scenes_result)): 
                                        ?>
                                        <option value="<?php echo $scene['id']; ?>"><?php echo htmlspecialchars($scene['name']); ?></option>
                                        <?php endwhile; } ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Hotspot</label>
                                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Scene</label>
                                    <select name="target_scene" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">Pilih Target Scene</option>
                                        <?php 
                                        if ($vr_scenes_result) {
                                            mysqli_data_seek($vr_scenes_result, 0);
                                            while ($scene = mysqli_fetch_assoc($vr_scenes_result)): 
                                        ?>
                                        <option value="<?php echo htmlspecialchars($scene['scene_key']); ?>"><?php echo htmlspecialchars($scene['name']); ?></option>
                                        <?php endwhile; } ?>
                                    </select>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Posisi X</label>
                                        <input type="number" step="0.1" name="position_x" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Posisi Y</label>
                                        <input type="number" step="0.1" name="position_y" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Posisi Z</label>
                                        <input type="number" step="0.1" name="position_z" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="add_hotspot" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">
                                <i class="fas fa-plus mr-1"></i>Tambah Hotspot
                            </button>
                        </form>
                    </div>
                    
                    <!-- Existing Hotspots -->
                    <div class="space-y-4">
                        <?php 
                        if ($vr_hotspots_result) {
                            while ($hotspot = mysqli_fetch_assoc($vr_hotspots_result)): 
                        ?>
                        <div class="bg-white p-4 rounded-lg border">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium"><?php echo htmlspecialchars($hotspot['name']); ?></h4>
                                    <p class="text-gray-600 text-sm">Scene: <?php echo htmlspecialchars($hotspot['scene_name']); ?></p>
                                    <p class="text-gray-600 text-sm">Target: <?php echo htmlspecialchars($hotspot['target_scene']); ?></p>
                                    <p class="text-gray-500 text-sm">
                                        Posisi: (<?php echo $hotspot['position_x']; ?>, <?php echo $hotspot['position_y']; ?>, <?php echo $hotspot['position_z']; ?>)
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <form method="post" class="inline" onsubmit="return confirm('Yakin ingin menghapus hotspot ini?')">
                                        <input type="hidden" name="hotspot_id" value="<?php echo $hotspot['id']; ?>">
                                        <button type="submit" name="delete_hotspot" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; } ?>
                    </div>
                </div>
            </div>

            <!-- Upload Management Tab -->
            <div id="upload-section" class="tab-content p-6 hidden">
                <h2 class="text-xl font-bold mb-4">Upload dan Kelola Gambar</h2>
                
                <!-- Upload Form -->
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-3">Upload Gambar Baru</h3>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Gambar</label>
                            <input type="file" name="image" accept="image/*" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, GIF, WEBP. Maksimal 5MB.</p>
                        </div>
                        <button type="submit" name="upload_image" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-upload mr-1"></i>Upload Gambar
                        </button>
                    </form>
                </div>

                <!-- Image Gallery -->
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-3">Gambar yang Tersedia</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <?php
                        $image_dir = 'asset/';
                        if (is_dir($image_dir)) {
                            $images = glob($image_dir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
                            foreach ($images as $image) {
                                $image_name = basename($image);
                                echo "<div class='bg-white p-2 rounded border hover:shadow-md transition-shadow'>";
                                echo "<img src='$image' alt='$image_name' class='w-full h-20 object-cover rounded mb-2' onclick='previewImage(\"$image\")'>";
                                echo "<p class='text-xs text-center mb-2'><code>$image_name</code></p>";
                                echo "<div class='flex space-x-1'>";
                                echo "<button onclick='copyToClipboard(\"$image\")' class='text-xs bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded flex-1' title='Copy URL'>Copy</button>";
                                echo "<form method='POST' class='inline-block flex-1'>";
                                echo "<input type='hidden' name='image_path' value='$image'>";
                                echo "<button type='submit' name='delete_image' class='text-xs bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded w-full' onclick='return confirm(\"Hapus gambar ini?\")' title='Hapus'>Del</button>";
                                echo "</form>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p class='text-gray-500'>Folder asset/ tidak ditemukan.</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Image Preview Modal -->
                <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center" onclick="closeModal()">
                    <div class="max-w-4xl max-h-4xl p-4">
                        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
                        <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">&times;</button>
                    </div>
                </div>
            </div>

            <!-- Kritik & Saran Management Tab -->
            <div id="kritik-saran-section" class="tab-content p-6 hidden">
                <h2 class="text-xl font-bold mb-4">Kelola Kritik & Saran</h2>
                
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-800">Daftar Kritik & Saran dari Pengunjung</h3>
                            <p class="text-blue-600 text-sm">Data kritik dan saran yang dikirim melalui halaman virtual tour</p>
                        </div>
                        <div class="text-blue-800">
                            <i class="fas fa-comments text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Kritik & Saran List -->
                <div class="space-y-4">
                    <?php 
                    if ($kritik_saran_result && mysqli_num_rows($kritik_saran_result) > 0) {
                        while ($kritik = mysqli_fetch_assoc($kritik_saran_result)): 
                    ?>
                    <div class="bg-white p-6 rounded-lg border hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-3">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($kritik['nama']); ?></h4>
                                        <p class="text-gray-600 text-sm">
                                            <i class="fas fa-envelope mr-1"></i>
                                            <?php echo htmlspecialchars($kritik['kontak']); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="bg-gray-50 p-4 rounded-lg mb-3">
                                    <p class="text-gray-800 leading-relaxed"><?php echo nl2br(htmlspecialchars($kritik['pesan'])); ?></p>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-1"></i>
                                    <span>Dikirim pada: <?php echo date('d F Y, H:i', strtotime($kritik['created_at'])); ?> WIB</span>
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-tag mr-1"></i>
                                    <span>ID: #<?php echo $kritik['id']; ?></span>
                                </div>
                            </div>
                            
                            <div class="ml-4 flex flex-col space-y-2">
                                <form method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kritik & saran ini?\n\nDari: <?php echo htmlspecialchars($kritik['nama']); ?>\nTanggal: <?php echo date('d/m/Y H:i', strtotime($kritik['created_at'])); ?>')">
                                    <input type="hidden" name="kritik_id" value="<?php echo $kritik['id']; ?>">
                                    <button type="submit" name="delete_kritik_saran" class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center transition-colors">
                                        <i class="fas fa-trash mr-2"></i>Hapus
                                    </button>
                                </form>
                                
                                <button onclick="copyToClipboard('<?php echo str_replace("'", "\\'", htmlspecialchars($kritik['pesan'])); ?>')" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded flex items-center transition-colors">
                                    <i class="fas fa-copy mr-2"></i>Copy
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile; 
                    } else {
                        echo "<div class='bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center'>";
                        echo "<div class='text-yellow-600 mb-2'><i class='fas fa-inbox text-3xl'></i></div>";
                        echo "<h3 class='text-lg font-semibold text-yellow-800 mb-2'>Belum Ada Kritik & Saran</h3>";
                        echo "<p class='text-yellow-700'>Belum ada kritik dan saran yang dikirim dari pengunjung virtual tour.</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
                
                <?php if ($kritik_saran_result && mysqli_num_rows($kritik_saran_result) > 0): ?>
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>
                            <i class="fas fa-info-circle mr-1"></i>
                            Total: <?php echo mysqli_num_rows($kritik_saran_result); ?> kritik & saran
                        </span>
                        <span>
                            <i class="fas fa-clock mr-1"></i>
                            Data diurutkan berdasarkan tanggal terbaru
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(function(content) {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(function(tab) {
                tab.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
                tab.classList.add('text-gray-500');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-section').classList.remove('hidden');
            
            // Add active class to selected tab
            document.getElementById(tabName + '-tab').classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
            document.getElementById(tabName + '-tab').classList.remove('text-gray-500');
        }
        
        // Initialize tabs and check URL parameters
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            const success = urlParams.get('success');
            
            if (tab) {
                showTab(tab);
            } else {
                showTab('dashboard'); // Default to dashboard
            }
            
            // Show success notification
            if (success) {
                showSuccessNotification(success);
            }
            
            // Clear URL parameters to prevent alert on page refresh
            if (urlParams.has('success')) {
                const url = new URL(window.location);
                url.searchParams.delete('success');
                window.history.replaceState({}, document.title, url.pathname + (url.searchParams.toString() ? '?' + url.searchParams.toString() : ''));
            }
        });

        function showSuccessNotification(type) {
            const messages = {
                'update': 'Konten berhasil diperbarui!',
                '1': 'Fasilitas berhasil ditambahkan!',
                '2': 'Fasilitas berhasil diperbarui!',
                'delete': 'Data berhasil dihapus!',
                'add': 'Data berhasil ditambahkan!',
                'upload': 'File berhasil diupload!',
                'add_scene': 'Scene VR berhasil ditambahkan!',
                'update_scene': 'Scene VR berhasil diperbarui!',
                'delete_scene': 'Scene VR berhasil dihapus!',
                'add_hotspot': 'Hotspot berhasil ditambahkan!',
                'delete_hotspot': 'Hotspot berhasil dihapus!'
            };
            
            const message = messages[type] || 'Operasi berhasil!';
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Animate out and remove
            setTimeout(() => {
                notification.style.transform = 'translateX(full)';
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('URL berhasil disalin: ' + text);
            });
        }

        // Image preview functions
        function previewImage(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside image
        document.addEventListener('click', function(e) {
            if (e.target.id === 'imageModal') {
                closeModal();
            }
        });

        // ESC key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        function editScene(id, name, description, sceneKey, image360, icon) {
            const editForm = `
                <div id="editSceneModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white p-6 rounded-lg max-w-md w-full mx-4">
                        <h3 class="text-lg font-bold mb-4">Edit Scene</h3>
                        <form method="post" class="space-y-4">
                            <input type="hidden" name="scene_id" value="${id}">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Scene</label>
                                <input type="text" name="name" value="${name}" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Key Scene</label>
                                <input type="text" name="scene_key" value="${sceneKey}" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">URL Gambar 360°</label>
                                <input type="url" name="image_360" value="${image360}" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Font Awesome)</label>
                                <input type="text" name="icon" value="${icon}" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea name="description" required rows="2" class="w-full border border-gray-300 rounded-md px-3 py-2">${description}</textarea>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" name="update_scene" class="bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                    <i class="fas fa-save mr-1"></i>Update
                                </button>
                                <button type="button" onclick="closeEditModal()" class="bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', editForm);
        }

        function closeEditModal() {
            const modal = document.getElementById('editSceneModal');
            if (modal) {
                modal.remove();
            }
        }

    </script>
</body>
</html>
