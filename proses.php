<?php
session_start();
include('keneksi.php');

$nama = $_POST['nama'];
$kontak = $_POST['kontak'];
$pesan = $_POST['pesan'];

if (empty($nama) || empty($kontak) || empty($pesan)) {
    echo "<script>alert('Semua field harus diisi!');window.history.back();</script>";
    exit;
}

$sql = "INSERT INTO tb_kritik_saran (nama, kontak, pesan) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $nama, $kontak, $pesan);

if ($stmt->execute()) {
    $token   = "8411142706:AAEjyMt6ZLRSH3VBxo3t_rDp_GAYeN-f9vg";
    $chat_id = "1769041604";
    $text    = "📩 KRITIK & SARAN BARU\n\n👤 Nama: $nama\n📧 Email/HP: $kontak\n📝 Pesan: $pesan";

    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($text);

    file_get_contents($url);

    echo "<script>alert('Pesan berhasil dikirim!');window.location='index.php';</script>";
} else {
    echo "<script>alert('Error: Gagal menyimpan pesan!');window.history.back();</script>";
}

$stmt->close();

$conn->close();
?>
