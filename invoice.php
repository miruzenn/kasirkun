<?php
// Include library FPDF
require('./fpdf/fpdf.php');

// Fungsi untuk membuat invoice PDF
function createInvoicePDF($idp, $namapel, $details)
{
    // Instansiasi objek FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Set font untuk judul
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Data Pesanan', 0, 1, 'C');

    // Tampilkan ID Pesanan dan Nama Pelanggan
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'ID Pesanan: ' . $idp, 0, 1, 'L');
    $pdf->Cell(0, 10, 'Nama Pelanggan: ' . $namapel, 0, 1, 'L');
    $pdf->Ln(10); // Spasi antara judul dan tabel

        // Header tabel
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 10, 'No', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Nama Produk', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Jumlah', 1, 0, 'C'); // New cell for Quantity
    $pdf->Cell(30, 10, 'Harga Satuan', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Sub-total', 1, 1, 'C');

    // Isi tabel dengan detail pesanan
    $pdf->SetFont('Arial', '', 12);
    $no = 1;
    $total = 0; // Variabel untuk menyimpan total keseluruhan
    foreach ($details as $detail) {
    $namaProduk = $detail['namaproduk'];
    $deskripsiProduk = $detail['deskripsi'];
    $hargaSatuan = $detail['harga'];
    $qty = $detail['qty'];
    $subtotal = $hargaSatuan * $qty; // Hitung subtotal
    
    // Tampilkan baris produk dalam tabel
    $pdf->Cell(20, 10, $no, 1, 0, 'C');
    $pdf->Cell(60, 10, $namaProduk . ' (' . $deskripsiProduk . ')', 1, 0, 'L'); // Tambahkan deskripsi
    $pdf->Cell(30, 10, $qty, 1, 0, 'C'); // Display Quantity
    $pdf->Cell(30, 10, 'Rp ' . number_format($hargaSatuan), 1, 0, 'R');
    $pdf->Cell(40, 10, 'Rp ' . number_format($subtotal), 1, 1, 'R');
    
    // Tambahkan subtotal ke total keseluruhan
    $total += $subtotal;
    
    $no++;
    }
    


    // Output total di bawah tabel
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(140, 10, 'Total', 1, 0, 'R');
    $pdf->Cell(40, 10, 'Rp ' . number_format($total), 1, 1, 'R');

    // Output PDF
    $pdf->Output(); // Output sebagai file download dengan nama 'invoice.pdf'
}

// Sertakan file ceklogin.php
require 'ceklogin.php';

// Inisialisasi nilai default untuk $idp
$idp = null;

// Cek jika parameter id pesanan tersedia di URL
if (isset($_GET['idp'])) {
    $idp = $_GET['idp'];

    // Query untuk mendapatkan data pesanan dan nama pelanggan
    $ambilnamapelanggan = mysqli_query($c, "SELECT * FROM pesanan p, pelanggan pl WHERE p.idpelanggan = pl.idpelanggan AND p.idorder = '$idp'");
    $np = mysqli_fetch_array($ambilnamapelanggan);
    $namapel = $np['namapelanggan'];

    // Query untuk mendapatkan detail pesanan
    $query = "SELECT dp.iddetailpesanan, p.namaproduk, p.deskripsi, p.harga, dp.qty 
          FROM detailpesanan dp
          JOIN produk p ON dp.idproduk = p.idproduk
          WHERE dp.idpesanan = '$idp'";
    $result = mysqli_query($c, $query);
    $details = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $details[] = $row;
    }

    // Panggil fungsi untuk membuat invoice PDF
    createInvoicePDF($idp, $namapel, $details);
} else {
    echo 'ID Pesanan tidak valid.';
}
?>
