<?php
// src/DataInventory.php
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
class DataInventory
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(Database $db)
    {
        // Sekarang getConnection() sudah ada
        $this->pdo = $db->getConnection();
    }



    /**
     * Simpan data mentah ke tabel data_inventory
     *
     * @param string $owner
     * @param string $phone
     * @param string|null $email
     * @param string $rawdata
     * @return bool
     */
    public function add(string $owner, string $phone, ?string $email, string $rawdata): bool
{
    // Set timezone to GMT+7 (Asia/Jakarta)
    $date       = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
    $timestamp  = $date->format('Y-m-d H:i:s');

    $sql = "INSERT INTO data_inventory
                (owner, phone, email, rawdata, description, metadata_pinecone, regdate, lastupdatedate)
            VALUES
                (:owner, :phone, :email, :rawdata, '', '', :regdate, :lastupdatedate)";

    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':owner'          => $owner,
        ':phone'          => $phone,
        ':email'          => $email,
        ':rawdata'        => $rawdata,
        ':regdate'        => $timestamp,
        ':lastupdatedate' => $timestamp,
    ]);
}

}
