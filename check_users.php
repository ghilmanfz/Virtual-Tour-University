<?php
include 'keneksi.php';

echo "Current admin users:\n";
$result = mysqli_query($conn, "SELECT id, username FROM tb_admin");
while($row = mysqli_fetch_assoc($result)) {
    echo "ID: " . $row['id'] . ", User: " . $row['username'] . "\n";
}
?>
