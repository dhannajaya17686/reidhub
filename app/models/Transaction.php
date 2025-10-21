<?php

class Transaction extends Model
{
    protected $table = 'transactions';

    public function create(array $data): ?int
    {
        try {
            $sql = "INSERT INTO {$this->table} (buyer_id, item_count, total_amount, created_at)
                    VALUES (?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                (int)$data['buyer_id'],
                (int)$data['item_count'],
                number_format((float)$data['total_amount'], 2, '.', ''),
            ]);
            return $ok ? (int)$this->db->lastInsertId() : null;
        } catch (Throwable $e) {
            Logger::error('Transaction create error: ' . $e->getMessage());
            return null;
        }
    }
}