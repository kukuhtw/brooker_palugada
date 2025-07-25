<?php
// public/dashboard.php
/*
 * 🤖 Aplikasi Brooker AI / Calo AI Palugada
 * (Apa elo mau, gw ada)
 * Dibuat oleh: Kukuh TW
 *
 * 📧 Email     : kukuhtw@gmail.com
 * 📱 WhatsApp  : 628129893706
 * 📷 Instagram : @kukuhtw
 * 🐦 X/Twitter : @kukuhtw
 * 👍 Facebook  : https://www.facebook.com/kukuhtw
 * 💼 LinkedIn  : https://id.linkedin.com/in/kukuhtw
*/
require __DIR__ . '/../bootstrap.php';
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    body { min-height: 100vh; display: flex; }
    #sidebar { width: 250px; }
    #content { flex: 1; padding: 2rem; }
  </style>
</head>
<body>

  <?php include("nav_admin.php"); ?>

  <div id="content">
    <div class="container-fluid">
  <h1 class="mb-4">📊 Dashboard Admin Palugada</h1>

  <div class="card mb-4">
    <div class="card-body">
      <p><strong>Login Sejak / Logged in since:</strong> <?= htmlspecialchars($_SESSION['login_time'] ?? '') ?></p>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header bg-success text-white">
      Tentang Aplikasi Brooker AI / Calo AI Palugada
    </div>
    <div class="card-body">
      <p>
        <strong>Brooker AI / Calo AI Palugada</strong> adalah aplikasi yang dirancang untuk mengumpulkan dan mengelola data 
        dari <strong>pemilik, penyedia, atau penjual barang dan jasa apa saja</strong>. Seperti slogannya: <em>"Apa elo mau, gw ada"</em>,
        aplikasi ini dapat digunakan untuk berbagai macam kebutuhan pencarian dan pencocokan antara permintaan dan penawaran.
      </p>

      <p>
        Anda sebagai <strong>pengguna dan pemilik aplikasi</strong> memegang kontrol penuh terhadap data penjual yang Anda kumpulkan — baik disimpan di laptop lokal maupun di hosting pribadi Anda. 
        Aplikasi ini bersifat <strong>open source</strong>, sehingga dapat dikembangkan, dimodifikasi, dan disesuaikan dengan kebutuhan bisnis Anda secara fleksibel.
      </p>

      <h5 class="mt-4">💼 Cara Kerja Aplikasi</h5>
      <ol>
        <li><strong>Kumpulkan data penjual</strong> sebanyak mungkin, seperti: foto produk, video, harga, dan deskripsi detail.</li>
        <li><strong>Tanyakan kepada calon pembeli</strong> barang atau jasa apa yang mereka butuhkan.</li>
        <li><strong>Lakukan pencarian di dashboard</strong>. Aplikasi ini menggunakan teknologi <strong>semantic search</strong> untuk mencocokkan kebutuhan dengan data yang tersedia.</li>
        <li><strong>Follow up manual</strong> setelah Anda menemukan data yang relevan dan cocok untuk pembeli.</li>
      </ol>

      <h5 class="mt-4">🔎 Apa itu Semantic Search?</h5>
      <p>
        <strong>Semantic search</strong> adalah metode pencarian berbasis <em>makna dan konteks</em>, bukan hanya mencocokkan kata-kata persis.
        Misalnya, jika calon pembeli menulis: <em>"saya cari rumah dekat tol dengan harga murah"</em>, sistem akan memahami maksud dari kalimat itu,
        dan menampilkan hasil yang relevan meskipun tidak mengandung kata-kata yang sama persis.
      </p>
      <p>
        Teknologi ini bekerja dengan mengubah teks menjadi angka (embedding), lalu mencocokkan berdasarkan <em>kemiripan makna</em>.
        Dengan begitu, pencarian menjadi lebih pintar, akurat, dan sesuai dengan bahasa manusia.
      </p>

      <h5 class="mt-4">🌟 Manfaat Aplikasi Ini</h5>
      <ul>
        <li>Mempermudah Anda menjadi <strong>broker digital</strong> tanpa perlu punya barang sendiri.</li>
        <li><strong>Meningkatkan peluang closing deal</strong> dengan pencarian pintar berbasis kebutuhan buyer.</li>
        <li><strong>Mengelola ribuan, ratusan ribu, bahkan jutaan listing penjual</strong> dengan efisien dan terstruktur.</li>
        <li>Bisa digunakan untuk semua jenis produk dan jasa — dari properti, kendaraan, sampai freelancer dan jasa lokal.</li>
        <li><strong>Kontrol data 100% milik Anda</strong>, tidak tergantung platform pihak ketiga.</li>
      </ul>

      <h5 class="mt-4">⚙️ Persyaratan Setup Aplikasi</h5>
      <ul>
        <li>✅ Memiliki <strong>API Key GPT dari OpenAI</strong> (untuk embedding & pencocokan semantic).</li>
        <li>✅ Memiliki <strong>API Key Pinecone</strong> (untuk menyimpan dan mencari vektor embedding).</li>
        <li>💻 Dapat dijalankan di <strong>laptop lokal</strong> atau <strong>VPS</strong> pribadi.</li>
        <li>🧰 <strong>Tech Stack:</strong> PHP 8.0 ke atas, MySQL 5.7 atau 8.0, Bootstrap 5.</li>
      </ul>

      <p class="mt-3">
        🚀 Aplikasi ini adalah alat bantu strategis untuk Anda yang ingin membangun usaha <strong>palugada digital</strong> yang modern, mandiri, dan fleksibel. Siap digunakan kapan saja dan dapat dikembangkan sesuai visi Anda.
      </p>
    </div>
  </div>
</div>

</div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
