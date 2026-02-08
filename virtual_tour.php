<?php
session_start();
require_once 'keneksi.php';
require_once 'track_visitor.php'; // Track visitor

// Ambil konten dari database
$content = [];
$query = "SELECT content_key, content_value FROM tb_content";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $content[$row['content_key']] = $row['content_value'];
    }
}

// Default content untuk virtual tour
$default_content = [
    'vr_title' => 'SELAMAT DATANG DI',
    'vr_subtitle' => 'VIRTUAL TOUR PRODI SISTEM INFORMASI',
    'vr_description' => 'Jelajahi fasilitas dan lingkungan kampus Universitas Pamulang secara virtual'
];

// Gabungkan dengan default jika ada yang kosong
foreach ($default_content as $key => $value) {
    if (!isset($content[$key]) || empty($content[$key])) {
        $content[$key] = $value;
    }
}

// Get VR scenes from database
$scenes_query = "SELECT * FROM tb_vr_scenes ORDER BY id";
$scenes_result = mysqli_query($conn, $scenes_query);
$scenes = [];
if ($scenes_result) {
    while ($row = mysqli_fetch_assoc($scenes_result)) {
        $scenes[] = $row;
    }
}

// Get hotspots from database
$hotspots_query = "SELECT * FROM tb_vr_hotspots ORDER BY scene_id, id";
$hotspots_result = mysqli_query($conn, $hotspots_query);
$hotspots = [];
if ($hotspots_result) {
    while ($row = mysqli_fetch_assoc($hotspots_result)) {
        $hotspots[$row['scene_id']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Tour Prodi Sistem Informasi - Universitas Pamulang</title>
    <link rel="icon" type="image/png" href="asset/logo-unpam-300x291.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://aframe.io/releases/1.4.0/aframe.min.js"></script>
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://static.republika.co.id/uploads/member/images/news/2x4cu8nrv8.jpg') no-repeat center center;
            background-size: cover;
        }
        .navbar { transition: all 0.3s ease; }
        .navbar.scrolled { background-color: rgba(45, 6, 218, 0.88); }
        .facility-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(63, 23, 245, 0.87);
        }
    </style>
</head>

    <body class="font-sans bg-gray-100">
    <!-- Navbar -->
    <nav class="navbar fixed w-full z-50 text-white">
        <div class="container mx-auto px-6 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <img src="asset/logo-unpam-300x291.png" alt="Logo Universitas Pamulang" class="h-12">
                <div class="ml-4">
                    <h1 class="text-xl font-bold">UNIVERSITAS PAMULANG</h1>
                    <p class="text-sm">Prodi Sistem Informasi</p>
                </div>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="index.php" class="hover:text-yellow-300">Beranda</a>
                <a href="virtual_tour.php" class="hover:text-yellow-300">Virtual Tour</a>
                <a href="fasilitas.php" class="hover:text-yellow-300">Fasilitas</a>
                <a href="tentang.php" class="hover:text-yellow-300">Tentang</a>
            </div>
            <button class="md:hidden focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section h-screen flex items-center justify-center text-white">
        <div class="text-center px-4">
            <h1 class="text-4xl md:text-6xl font-bold mb-6"><?php echo htmlspecialchars($content['vr_title']); ?></h1>
            <h2 class="text-3xl md:text-5xl font-bold mb-8"><?php echo htmlspecialchars($content['vr_subtitle']); ?></h2>
            <p class="text-xl mb-10 max-w-3xl mx-auto"><?php echo htmlspecialchars($content['vr_description']); ?></p>
            <button onclick="scrollToTour()" class="mt-8 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-8 rounded-full transition duration-300 inline-flex items-center">
                <i class="fas fa-vr-cardboard mr-2"></i> Mulai Tour
            </button>
        </div>
    </section>

 <!-- Virtual Tour Section -->
    <section id="tour" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-16">VIRTUAL TOUR 360°</h2>
            
            <!-- A-Frame VR Scene -->
            <div class="mb-8">
                <a-scene id="vrScene" embedded style="height: 600px; width: 100%;" vr-mode-ui="enabled: true" background="color: #212121">
                    <!-- Assets -->
                    <a-assets>
                        <?php foreach($scenes as $scene): ?>
                        <img id="<?php echo $scene['scene_key']; ?>" src="<?php echo htmlspecialchars($scene['image_360']); ?>" />
                        <?php endforeach; ?>
                    </a-assets>

                    <!-- 360 Degree Image -->
                    <a-sky id="panorama" src="#<?php echo !empty($scenes) ? $scenes[0]['scene_key'] : 'entrance'; ?>" rotation="0 -130 0"></a-sky>

                    <!-- Navigation Hotspots -->
                    <a-entity id="hotspots">
                        <?php 
                        $current_scene_id = !empty($scenes) ? $scenes[0]['id'] : 1;
                        if (isset($hotspots[$current_scene_id])): 
                            foreach($hotspots[$current_scene_id] as $hotspot): 
                        ?>
                        <a-sphere 
                            class="hotspot" 
                            position="<?php echo $hotspot['position_x'] . ' ' . $hotspot['position_y'] . ' ' . $hotspot['position_z']; ?>" 
                            radius="0.3" 
                            color="#FFD700" 
                            opacity="0.8"
                            animation="property: scale; to: 1.2 1.2 1.2; dir: alternate; dur: 1000; loop: true"
                            data-target="<?php echo $hotspot['target_scene']; ?>"
                            data-name="<?php echo htmlspecialchars($hotspot['name']); ?>">
                            
                            <!-- Text label -->
                            <a-text 
                                value="<?php echo htmlspecialchars($hotspot['name']); ?>" 
                                position="0 0.5 0" 
                                align="center" 
                                color="#FFFFFF" 
                                background="color: #000000; opacity: 0.7"
                                scale="2 2 2">
                            </a-text>
                        </a-sphere>
                        <?php 
                            endforeach; 
                        endif; 
                        ?>
                    </a-entity>

                    <!-- Camera -->
                    <a-camera 
                        look-controls="enabled: true" 
                        wasd-controls="enabled: false"
                        cursor="rayOrigin: mouse">
                    </a-camera>
                </a-scene>
            </div>

            <!-- Scene Navigation -->
            <div class="bg-gray-100 p-6 rounded-lg">
                <h3 class="text-xl font-semibold mb-4">Pilih Lokasi:</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php foreach($scenes as $index => $scene): ?>
                    <button 
                        class="scene-btn p-4 bg-white rounded-lg shadow hover:shadow-lg transition-all duration-300 <?php echo $index === 0 ? 'ring-2 ring-blue-500' : ''; ?>"
                        onclick="changeScene('<?php echo $scene['scene_key']; ?>', <?php echo $scene['id']; ?>)"
                        data-scene-id="<?php echo $scene['id']; ?>">
                        <i class="<?php echo htmlspecialchars($scene['icon']); ?> text-2xl text-blue-600 mb-2"></i>
                        <div class="font-medium"><?php echo htmlspecialchars($scene['name']); ?></div>
                        <div class="text-sm text-gray-600"><?php echo htmlspecialchars($scene['description']); ?></div>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mt-8 bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold mb-2"><i class="fas fa-info-circle text-blue-600 mr-2"></i>Cara Menggunakan Virtual Tour</h3>
                <ul class="space-y-2 text-gray-700">
                    <li><i class="fas fa-mouse-pointer mr-2"></i>Gunakan mouse untuk melihat ke segala arah</li>
                    <li><i class="fas fa-hand-pointer mr-2"></i>Klik tombol kuning untuk berpindah lokasi</li>
                    <li><i class="fas fa-vr-cardboard mr-2"></i>Klik ikon VR untuk mode Virtual Reality</li>
                    <li><i class="fas fa-expand-arrows-alt mr-2"></i>Klik ikon fullscreen untuk tampilan penuh</li>
                </ul>
            </div>
        </div>
    </section>

     <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-16">HUBUNGI KAMI</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div>
                    <h3 class="text-2xl font-semibold mb-6">INFORMASI</h3>
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="bg-blue-200 p-3 rounded-full mr-4">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Alamat</h4>
                                <p>Jl. Raya Puspitek, Buaran, Kec. Pamulang, Kota Tangerang Selatan, Banten 15310</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-blue-200 p-3 rounded-full mr-4">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Telepon</h4>
                                <p>021 7412 566<br>Ext. 123 (Prodi Sistem Informasi)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-blue-200 p-3 rounded-full mr-4">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="font-medium">Email</h4>
                                <p>humas@unpam.ac.id</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-xl font-semibold mb-4">Jam Operasional</h3>
                        <div class="bg-white rounded-lg p-4">
                            <table class="w-full">
                                <tbody>
                                    <tr class="border-b border-gray-600">
                                        <td class="py-2 font-medium">Senin - Jumat</td>
                                        <td class="py-2 text-right">08:00 - 16:00</td>
                                    </tr>
                                    <tr class="border-b border-gray-600">
                                        <td class="py-2 font-medium">Sabtu</td>
                                        <td class="py-2 text-right">08:00 - 14:00</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 font-medium">Minggu</td>
                                        <td class="py-2 text-right">Tutup</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <!-- Tampilan pesan -->
                <div>
                    <h3 class="text-2xl font-semibold mb-6">KRITIK & SARAN</h3>
                    <form method="post" action="#contact" class="space-y-4"></form>
                    <form method="post" action="proses.php" class="space-y-4">
                <div>
                    <label for="nama" class="block mb-1">Nama</label>
                    <input type="text" name="nama" id="nama" required class="w-full bg-white border border-gray-700 rounded-md px-4 py-2">
                </div>
                <div>
                    <label for="kontak" class="block mb-1">Email/No. HP</label>
                    <input type="text" name="kontak" id="kontak" required class="w-full bg-white border border-gray-700 rounded-md px-4 py-2">
                </div>
                <div>
                    <label for="pesan" class="block mb-1">Pesan</label>
                    <textarea name="pesan" id="pesan" rows="4" required class="w-full bg-white border border-gray-700 rounded-md px-4 py-2"></textarea>
                </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md">Kirim Pesan</button>
                </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto px-4">
        <!-- Pakai grid, tapi center semua item -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-12 justify-items-center text-center">
            <!-- Kolom 1 -->
            <div>
                <img src="asset/logo-unpam-300x291.png" alt="Logo Universitas Pamulang versi putih" class="h-16 w-16 mb-4">
                <p class="text-gray-400 max-w-sm">Universitas Pamulang memberikan pendidikan berkualitas untuk masa depan yang lebih baik.</p>
            </div>

            <!-- Kolom 2 -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Tautan Cepat</h4>
                <ul class="space-y-2">
                    <li><a href="https://rl-pmb.unpam.ac.id/" class="text-gray-400 hover:text-white" target="_blank">Penerimaan Mahasiswa Baru</a></li>
                    <li><a href="https://unpam.ac.id/kampus-2-unpam-viktor" class="text-gray-400 hover:text-white" target="_blank">Informasi Universitas Pamulang</a></li>
                    <li><a href="https://unpam.ac.id/profil-unpam" class="text-gray-400 hover:text-white" target="_blank">Profil UNPAM</a></li>
                    <li><a href="https://unpam.ac.id/visi-misi" class="text-gray-400 hover:text-white" target="_blank">Visi dan Misi</a></li>
                    <li><a href="https://unpam.ac.id/sejarah" class="text-gray-400 hover:text-white" target="_blank">Histori</a></li>
                </ul>
            </div>

            <!-- Kolom 3 -->
            <div>
                <h4 class="text-lg font-semibold mb-4">Ikuti Kami</h4>
                <div class="flex justify-center space-x-4 mb-4">
                    <a href="https://www.instagram.com/sisteminformasi.unpam/" target="_blank" class="bg-gray-700 p-2 rounded-full hover:bg-blue-600">FB</a>
                    <a href="https://www.instagram.com/UNPAM/" target="_blank" class="bg-gray-700 p-2 rounded-full hover:bg-purple-600">IG</a>
                    <a href="https://www.youtube.com/@unpamofficial" target="_blank" class="bg-gray-700 p-2 rounded-full hover:bg-red-600">YT</a>
                    <a href="https://wa.me/6281395333634" target="_blank" class="bg-gray-700 p-2 rounded-full hover:bg-green-500">WA</a>
                </div>
                <div class="bg-gray-700 p-2 rounded-lg">
                        <h4 class="font-medium mb-2">Hubungi Kami Via WhatsApp</h4>
                        <p class="text-gray-300 text-sm mb-4">Butuh bantuan? Chat langsung dengan admin kami untuk informasi pendaftaran, jadwal kunjungan, atau pertanyaan lainnya.</p>
                        <div class="flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span class="text-sm">Akan kami Respon</span>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Bagian copyright -->
        <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-400">
            <p>&copy; 2025 Universitas Pamulang</p>
        </div>
    </div>
</footer>

    <script>
        // VR Scene data from PHP
        const scenes = <?php echo json_encode($scenes); ?>;
        const hotspots = <?php echo json_encode($hotspots); ?>;
        let currentSceneId = <?php echo !empty($scenes) ? $scenes[0]['id'] : 1; ?>;

        // Function to scroll to tour section
        function scrollToTour() {
            document.getElementById('tour').scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }

        // Function to change scene
        function changeScene(sceneKey, sceneId) {
            const panorama = document.getElementById('panorama');
            const hotspotsContainer = document.getElementById('hotspots');
            
            // Change the panorama image
            panorama.setAttribute('src', '#' + sceneKey);
            
            // Update scene buttons
            document.querySelectorAll('.scene-btn').forEach(btn => {
                btn.classList.remove('ring-2', 'ring-blue-500');
            });
            document.querySelector(`[data-scene-id="${sceneId}"]`).classList.add('ring-2', 'ring-blue-500');
            
            // Clear existing hotspots
            while (hotspotsContainer.firstChild) {
                hotspotsContainer.removeChild(hotspotsContainer.firstChild);
            }
            
            // Add new hotspots for this scene
            if (hotspots[sceneId]) {
                hotspots[sceneId].forEach(hotspot => {
                    const sphere = document.createElement('a-sphere');
                    sphere.setAttribute('class', 'hotspot');
                    sphere.setAttribute('position', `${hotspot.position_x} ${hotspot.position_y} ${hotspot.position_z}`);
                    sphere.setAttribute('radius', '0.3');
                    sphere.setAttribute('color', '#FFD700');
                    sphere.setAttribute('opacity', '0.8');
                    sphere.setAttribute('animation', 'property: scale; to: 1.2 1.2 1.2; dir: alternate; dur: 1000; loop: true');
                    sphere.setAttribute('data-target', hotspot.target_scene);
                    sphere.setAttribute('data-name', hotspot.name);
                    
                    // Add text label
                    const text = document.createElement('a-text');
                    text.setAttribute('value', hotspot.name);
                    text.setAttribute('position', '0 0.5 0');
                    text.setAttribute('align', 'center');
                    text.setAttribute('color', '#FFFFFF');
                    text.setAttribute('background', 'color: #000000; opacity: 0.7');
                    text.setAttribute('scale', '2 2 2');
                    
                    sphere.appendChild(text);
                    
                    // Add click event
                    sphere.addEventListener('click', function() {
                        const targetScene = hotspot.target_scene;
                        const targetSceneData = scenes.find(s => s.scene_key === targetScene);
                        if (targetSceneData) {
                            changeScene(targetScene, targetSceneData.id);
                        }
                    });
                    
                    hotspotsContainer.appendChild(sphere);
                });
            }
            
            currentSceneId = sceneId;
        }

        // Initialize hotspot click events when A-Frame is ready
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                // Add click events to initial hotspots
                document.querySelectorAll('.hotspot').forEach(hotspot => {
                    hotspot.addEventListener('click', function() {
                        const targetScene = this.getAttribute('data-target');
                        const targetSceneData = scenes.find(s => s.scene_key === targetScene);
                        if (targetSceneData) {
                            changeScene(targetScene, targetSceneData.id);
                        }
                    });
                });
            }, 1000);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) { navbar.classList.add('scrolled'); }
            else { navbar.classList.remove('scrolled'); }
        });

        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.md\\:hidden');
        const mobileMenu = document.querySelector('.md\\:flex');
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>