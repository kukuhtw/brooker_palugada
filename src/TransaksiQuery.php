<?php
// src/TransaksiQuery.php
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
class TransaksiQuery
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(Database $db)
    {
        // Ambil koneksi PDO dari Database wrapper
        $this->pdo = $db->getConnection();
    }

    /**
     * Simpan satu record query transaksi ke tabel transaksi_query
     *
     * @param string $buyerName
     * @param string $buyerEmail
     * @param string $buyerWa
     * @param string $buyerQuery
     * @param string $results
     * @return bool
     */
    public function add(string $buyerName, string $buyerEmail, string $buyerWa, string $buyerQuery, string $results): bool
    {
        $sql = "
            INSERT INTO transaksi_query
                (buyer_name, buyer_email, buyer_wa, buyer_query, results, trdate)
            VALUES
                (:buyer_name, :buyer_email, :buyer_wa, :buyer_query, :results, NOW())
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':buyer_name'  => $buyerName,
            ':buyer_email' => $buyerEmail,
            ':buyer_wa'    => $buyerWa,
            ':buyer_query' => $buyerQuery,
            ':results'     => $results,
        ]);
    }

    /**
     * Ambil semua record transaksi_query, terbaru dulu
     *
     * @return array
     */
    public function getAll(): array
    {
        $sql  = "SELECT * FROM transaksi_query ORDER BY trdate DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Ambil satu record berdasarkan ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $sql  = "SELECT * FROM transaksi_query WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }
}
