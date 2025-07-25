<?php
/**
 * bootstrap.php
 * Application bootstrap: load config, init logger & database
 
// include core classes


*/
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
// **1.** Load core class files
require __DIR__ . '/src/Logger.php';
require __DIR__ . '/src/Database.php';
require __DIR__ . '/src/AdminAuth.php';
require __DIR__ . '/src/DataInventory.php'; 
require __DIR__ . '/src/PromptSettings.php';
require __DIR__ . '/src/TransaksiQuery.php';
require __DIR__ . '/src/Settings.php';

// 1. Set zona waktu PHP ke GMT+7
date_default_timezone_set('Asia/Jakarta');

// **2.** Load config
$config = require __DIR__ . '/config.php';

// **3.** Inisialisasi logger
Logger::init($config['logger']);
Logger::info('Logger initialized.');


// initialize database
try {
    $db = new Database($config['db']);
    $pdo = $db->getConnection();

    // 6. Pastikan PDO throw exception on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 7. Aktifkan emulated prepares supaya placeholder berulang (:kw) bisa dipakai
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

    // 8. Set timezone MySQL ke +07:00 agar NOW() ikut Waktu Jakarta
    $pdo->exec("SET time_zone = '+07:00'");

    Logger::info('Database connection established with GMT+7.');
   
} catch (Exception $e) {
    die('Fatal error: ' . $e->getMessage());
}

// AdminAuth init
$adminAuth = new AdminAuth($db, $config['features']['admin_password']);
$settings  = new Settings($db);

?>
