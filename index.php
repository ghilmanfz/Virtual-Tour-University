<?php
session_start();
include('keneksi.php');
include('track_visitor.php'); // Track visitor

// Ambil konten dari database
$content = [];
$query = "SELECT content_key, content_value FROM tb_content";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $content[$row['content_key']] = $row['content_value'];
    }
}

// Ambil data fasilitas dari database
$facilities = [];
$query = "SELECT * FROM tb_facilities ORDER BY id";
$result = mysqli_query($conn, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $facilities[] = $row;
    }
}

// Default content jika database kosong
$default_content = [
    'hero_title' => 'Selamat Datang di Virtual Tour Prodi Sistem Informasi',
    'hero_subtitle' => 'Universitas Pamulang',
    'hero_description' => 'Jelajahi fasilitas modern dan lingkungan akademik yang mendukung proses pembelajaran di Program Studi Sistem Informasi melalui pengalaman virtual tour 360°',
    'facilities_title' => 'FASILITAS UNPAM',
    'facilities_description' => 'Temukan berbagai fasilitas modern yang mendukung proses belajar mengajar di Program Studi Sistem Informasi'
];

// Gabungkan dengan default jika ada yang kosong
foreach ($default_content as $key => $value) {
    if (!isset($content[$key]) || empty($content[$key])) {
        $content[$key] = $value;
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
                <a href="login.php" class="hover:text-gray-200">Login Admin</a>
            </div>
            <button class="md:hidden focus:outline-none">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section h-screen flex items-center justify-center text-white">
        <div class="text-center px-4">
            <h1 class="text-4xl md:text-6xl font-bold mb-6"><?php echo htmlspecialchars($content['hero_title']); ?></h1>
            <h2 class="text-3xl md:text-5xl font-bold mb-8"><?php echo htmlspecialchars($content['hero_subtitle']); ?></h2>
            <p class="text-xl mb-10 max-w-3xl mx-auto"><?php echo htmlspecialchars($content['hero_description']); ?></p>
            <a href="virtual_tour.php" class="mt-8 bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-8 rounded-full transition duration-300 inline-flex items-center">
                <i class="fas fa-vr-cardboard mr-2"></i> Mulai Tour
            </a>
        </div>
    </section>

 <!-- Virtual Tour Section -->
    <section id="tour" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-16">VIRTUAL TOUR 360°</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <div class="bg-gray-200 rounded-lg overflow-hidden h-96 relative">
                        <!-- 360 Tour Placeholder -->
                        <img src="https://static.republika.co.id/uploads/member/images/news/2x4cu8nrv8.jpg" alt="Pratinjau virtual tour 360 derajat sistem informasi Universitas Pamulang" class="w-full h-full object-cover">
                    </div>
                </div>
                
                <div>
                    <h3 class="text-2xl font-semibold mb-4">Jelajahi Kampus Secara Virtual</h3>
                    <p class="mb-6 leading-relaxed">Dengan teknologi virtual tour 360°, Anda dapat menjelajahi berbagai fasilitas Prodi Sistem Informasi Universitas Pamulang dari mana saja dan kapan saja.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-full mr-4">
                                <i class="fas fa-check text-blue-600"></i>
                            </div>
                            <p>Pandangan 360° berbagai ruangan penting</p>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-full mr-4">
                                <i class="fas fa-check text-blue-600"></i>
                            </div>
                            <p>Navigasi intuitif dengan menu interaktif</p>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-blue-100 p-2 rounded-full mr-4">
                                <i class="fas fa-check text-blue-600"></i>
                            </div>
                            <p>Informasi detail setiap fasilitas</p>
                        </div>
                    </div>
                    
                    <a href="virtual_tour.php" class="mt-8 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-full transition duration-300 inline-flex items-center">
                        <i class="fas fa-vr-cardboard mr-2"></i> Mulai Virtual Tour
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section id="fasilitas.php" class="py-20 bg-gray-100">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-6"><?php echo htmlspecialchars($content['facilities_title']); ?></h2>
            <p class="text-center text-gray-700 mb-16 max-w-2xl mx-auto"><?php echo htmlspecialchars($content['facilities_description']); ?></p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (!empty($facilities)): ?>
                    <?php foreach ($facilities as $facility): ?>
                        <div class="facility-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                            <div class="h-48 overflow-hidden">
                                <img src="<?php echo htmlspecialchars(isset($facility['image']) ? $facility['image'] : 'asset/default.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars(isset($facility['name']) ? $facility['name'] : 'Fasilitas'); ?>" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars(isset($facility['name']) ? $facility['name'] : 'Fasilitas'); ?></h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars(isset($facility['description']) ? $facility['description'] : 'Deskripsi tidak tersedia'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default facilities if database is empty -->
                    <div class="facility-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                        <div class="h-48 overflow-hidden">
                            <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/c873bb31-8c0a-47d0-b7b9-5a92aeec00cb.png" alt="Laboratorium komputer modern dengan perangkat terbaru di Prodi Sistem Informasi" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Laboratorium Komputer</h3>
                            <p class="text-gray-600">Fasilitas komputer dengan spesifikasi tinggi untuk praktikum pemograman.</p>
                        </div>
                    </div>
                    
                    <div class="facility-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                        <div class="h-48 overflow-hidden">
                            <img src="asset/perpustakaan 2.webp" alt="Perpustakaan dengan koleksi buku teknologi informasi yang lengkap" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Perpustakaan</h3>
                            <p class="text-gray-600">Ribuan koleksi buku dan jurnal di Perpustakaan Universitas Pamulang.</p>
                        </div>
                    </div>
                    
                    <div class="facility-card bg-white rounded-lg overflow-hidden shadow-md transition duration-300">
                        <div class="h-48 overflow-hidden">
                            <img src="asset/kelas.jpg" alt="Ruangan kelas ber-AC dengan proyektor dan fasilitas belajar modern" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Kelas Ber-AC</h3>
                            <p class="text-gray-600">Ruangan belajar nyaman dengan fasilitas wifi untuk pembelajaran interaktif.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- New Section for Campus Map and Google Maps -->
    <section id="lokasi-kampus" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">
            Lokasi Kampus UNPAM Viktor
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Denah Kampus -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Denah Kampus</h3>
                <img src="asset/kampus2.B0WqicWG.jpg" alt="Foto Denah" class="rounded-lg shadow-xl">
            <p class="text-gray-600 mt-4">
            Lihat denah kampus untuk orientasi lebih lanjut.
        </p>
      </div>

        <!-- Google Maps -->
        <div>
            <h3 class="text-xl font-semibold mb-4">Lokasi di Google Maps</h3>
                <div class="w-full h-[400px]">
                    <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.155041658!2d106.6889577!3d-6.3462879!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69e5a6e26dc3cd%3A0xccd6344b8021119d!2sUniversitas%20Pamulang%20Kampus%202%20(UNPAM%20Viktor)!5e0!3m2!1sid!2sid!4v1723900000000!5m2!1sid!2sid"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    class="rounded-lg shadow-lg"
                ></iframe>
            </div>
                <p class="text-gray-600 mt-4">
                Temukan lokasi kami dengan mudah melalui Google Maps.
                </p>
            </div>
            </div>
        </div>
    </section>

     <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-16">TENTANG PRODI SISTEM INFORMASI</h2>
            <div class="flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-10 md:mb-0">
                    <img src="asset/Landscape HMSI.jpeg" alt="Foto Kegiatan" class="rounded-lg shadow-xl">
                </div>
                <div class="md:w-1/2 md:pl-12">
                    <h3 class="text-2xl font-semibold mb-4">Visi & Misi</h3>
                    <p class="mb-6 leading-relaxed">Menjadi program studi unggulan dalam bidang Sistem Informasi yang berdaya saing di tingkat nasional pada tahun 2030.</p>
                    <p class="mb-6 leading-relaxed">Untuk mencapai visi ini, kami berkomitmen untuk:</p>
                    <ul class="list-disc pl-6 mb-6 space-y-2">
                        <li>Menyelenggarakan pendidikan berbasis kompetensi</li>
                        <li>Mengembangkan penelitian inovatif</li>
                        <li>Melaksanakan pengabdian kepada masyarakat</li>
                        <li>Menjalin kerjasama dengan berbagai pihak</li>
                    </ul>
                    <div class="mt-8">
                        <a href="#" class="bg-blue-500 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-300 inline-block">Selengkapnya</a>
                    </div>
                </div>
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
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>