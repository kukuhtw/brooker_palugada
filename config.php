<?php
/**
 * config.php
 * Database configuration settings
 * 
 * 


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
// config.php
return [
    'db' => [
        'host'       => 'localhost',
        'dbname'     => 'palugada',
        'user'       => 'root',
        'password'   => '',
        'charset'    => 'utf8mb4',
    ],
    'logger' => [
        'path' => __DIR__ . '/logs/app.log',
        'level' => 'DEBUG',
    ],    
    'features' => [
        'admin_password' => true,  // <— pastikan ini ada!
    ],
];


