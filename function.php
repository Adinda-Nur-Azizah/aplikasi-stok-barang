<?php
session_start();

// Membuat koneksi ke database
$conn = mysqli_connect("localhost", "root", "", "stockbarang");

// Memeriksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Menambah barang baru
if(isset($_POST['addnewbarang'])){
    $namabarang = $_POST['namabarang'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];

    // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt = $conn->prepare("INSERT INTO stock (namabarang, deskripsi, stock) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $namabarang, $deskripsi, $stock);

    if ($stmt->execute()) {
        header('location: index.php');
        exit();
    } else {
        echo 'Gagal menambah barang.';
        // Tindak lanjuti kesalahan dengan cara yang sesuai, misalnya dengan menampilkan pesan kesalahan kepada pengguna atau mencatat kesalahan
    }
}

// Menambah barang masuk
if(isset($_POST['barangmasuk'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt = $conn->prepare("INSERT INTO masuk (idbarang, keterangan, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $barangnya, $penerima, $quantity);

    // Update stock
    $cekstocksekarang = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);
    $stocksekarang = $ambildatanya['stock'];
    $tambahkanstocksekarangdenganquantity = $stocksekarang + $quantity;

    $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock='$tambahkanstocksekarangdenganquantity' WHERE idbarang='$barangnya'");

    // Execute both queries and redirect if successful
    if ($stmt->execute() && $updatestockmasuk) {
        header('location: masuk.php');
        exit();
    } else {
        echo 'Gagal menambah barang.';
        // Tindak lanjuti kesalahan dengan cara yang sesuai, misalnya dengan menampilkan pesan kesalahan kepada pengguna atau mencatat kesalahan
    }
}


// Menambah barang keluar
if(isset($_POST['addbarangkeluar'])){
    $barangnya = $_POST['barangnya'];
    $penerima = $_POST['penerima'];
    $quantity = $_POST['quantity'];

    // Mendapatkan stock sekarang
    $cekstocksekarang = mysqli_query($conn, "SELECT stock FROM stock WHERE idbarang='$barangnya'");
    $ambildatanya = mysqli_fetch_array($cekstocksekarang);
    $stocksekarang = $ambildatanya['stock'];

    // Menghitung sisa stock setelah barang keluar
    $tambahkanstocksekarangdenganquantity = $stocksekarang - $quantity;

    // Menambah data ke tabel keluar
    $addtokeluar = mysqli_query($conn, "INSERT INTO keluar (idbarang, penerima, quantity) VALUES ('$barangnya', '$penerima', '$quantity')");

    // Update stock di tabel stock
    $updatestockmasuk = mysqli_query($conn, "UPDATE stock SET stock='$tambahkanstocksekarangdenganquantity' WHERE idbarang='$barangnya'");

    // Redirect jika kedua query berhasil dieksekusi
    if($addtokeluar && $updatestockmasuk) {
        header('location: keluar.php');
        exit();
    } else {
        echo 'Gagal menambah barang keluar.';
        // Tindak lanjuti kesalahan dengan cara yang sesuai, misalnya dengan menampilkan pesan kesalahan kepada pengguna atau mencatat kesalahan
    }

}


?>