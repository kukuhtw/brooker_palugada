<?php
// ========== src/DataAttachment.php ==========
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
require_once __DIR__ . '/Database.php';

class DataAttachment {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    /**
     * Tambah record attachment
     *
     * @param int    $dataId       ID inventory terkait
     * @param string $fileName     Nama asli file
     * @param string $urlAttachment URL relatif berkas
     */
    public function add(int $dataId, string $fileName, string $urlAttachment): void {
        $sql = "INSERT INTO data_attachment (data_id, file_name, url_attachment, regdate) 
              VALUES (:data_id, :file_name, :url_attachment, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':data_id'       => $dataId,
            ':file_name'     => $fileName,
            ':url_attachment'=> $urlAttachment
        ]);
    }
}

?>
