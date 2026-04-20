<?php

class MarketplaceSellerAction extends Model
{
    protected $table = 'marketplace_seller_actions';

    public function addWarning(int $sellerId, ?int $reportId, int $adminId, string $reason): bool
    {
        try {
            if ($sellerId <= 0 || $adminId <= 0 || trim($reason) === '') {
                return false;
            }

            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (seller_id, report_id, admin_id, action_type, reason, created_at)
                 VALUES (?, ?, ?, 'warning', ?, NOW())"
            );

            $normalizedReportId = ($reportId !== null && $reportId > 0) ? $reportId : null;
            try {
                return $stmt->execute([$sellerId, $normalizedReportId, $adminId, trim($reason)]);
            } catch (Throwable $insertError) {
                if ($normalizedReportId !== null || !$this->isLegacyReportIdNotNullError($insertError)) {
                    throw $insertError;
                }

                $fallbackReportId = $this->getLatestSellerReportId($sellerId);
                if ($fallbackReportId === null) {
                    throw $insertError;
                }

                return $stmt->execute([$sellerId, $fallbackReportId, $adminId, trim($reason)]);
            }
        } catch (Throwable $e) {
            Logger::error('MarketplaceSellerAction addWarning error: ' . $e->getMessage());
            return false;
        }
    }

    public function isSellerBanned(int $sellerId): bool
    {
        try {
            if ($sellerId <= 0) {
                return false;
            }

            $stmt = $this->db->prepare(
                "SELECT action_type
                 FROM {$this->table}
                 WHERE seller_id = ? AND action_type IN ('ban', 'unban')
                 ORDER BY created_at DESC, id DESC
                 LIMIT 1"
            );
            $stmt->execute([$sellerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return ($row['action_type'] ?? null) === 'ban';
        } catch (Throwable $e) {
            Logger::error('MarketplaceSellerAction isSellerBanned error: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleBan(int $sellerId, ?int $reportId, int $adminId, string $reason): array
    {
        try {
            if ($sellerId <= 0 || $adminId <= 0 || trim($reason) === '') {
                return ['success' => false, 'message' => 'Invalid moderation data'];
            }

            $currentlyBanned = $this->isSellerBanned($sellerId);
            $actionType = $currentlyBanned ? 'unban' : 'ban';

            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (seller_id, report_id, admin_id, action_type, reason, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())"
            );

            $normalizedReportId = ($reportId !== null && $reportId > 0) ? $reportId : null;
            try {
                $ok = $stmt->execute([$sellerId, $normalizedReportId, $adminId, $actionType, trim($reason)]);
            } catch (Throwable $insertError) {
                if ($normalizedReportId !== null || !$this->isLegacyReportIdNotNullError($insertError)) {
                    throw $insertError;
                }

                $fallbackReportId = $this->getLatestSellerReportId($sellerId);
                if ($fallbackReportId === null) {
                    throw $insertError;
                }

                $ok = $stmt->execute([$sellerId, $fallbackReportId, $adminId, $actionType, trim($reason)]);
            }
            if (!$ok) {
                return ['success' => false, 'message' => 'Failed to update ban status'];
            }

            return [
                'success' => true,
                'isBanned' => $actionType === 'ban',
                'actionType' => $actionType,
            ];
        } catch (Throwable $e) {
            Logger::error('MarketplaceSellerAction toggleBan error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Server error'];
        }
    }

    public function getWarningCount(int $sellerId): int
    {
        try {
            if ($sellerId <= 0) {
                return 0;
            }

            $stmt = $this->db->prepare(
                "SELECT COUNT(*) AS warning_count
                 FROM {$this->table}
                 WHERE seller_id = ? AND action_type = 'warning'"
            );
            $stmt->execute([$sellerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)($row['warning_count'] ?? 0);
        } catch (Throwable $e) {
            Logger::error('MarketplaceSellerAction getWarningCount error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getSellerModerationHistory(int $sellerId, int $limit = 100): array
    {
        try {
            if ($sellerId <= 0) {
                return [];
            }

            $normalizedLimit = max(1, min(500, $limit));
            $sql = "SELECT
                        msa.id,
                        msa.seller_id,
                        msa.report_id,
                        msa.admin_id,
                        msa.action_type,
                        msa.reason,
                        msa.created_at,
                        a.email AS admin_email,
                        r.status AS report_status,
                        r.category AS report_category
                    FROM {$this->table} msa
                    LEFT JOIN admins a ON a.id = msa.admin_id
                    LEFT JOIN marketplace_reports r ON r.id = msa.report_id
                    WHERE msa.seller_id = ?
                    ORDER BY msa.created_at DESC, msa.id DESC
                    LIMIT {$normalizedLimit}";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sellerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            Logger::error('MarketplaceSellerAction getSellerModerationHistory error: ' . $e->getMessage());
            return [];
        }
    }

    private function getLatestSellerReportId(int $sellerId): ?int
    {
        try {
            if ($sellerId <= 0) {
                return null;
            }

            $stmt = $this->db->prepare(
                "SELECT id
                 FROM marketplace_reports
                 WHERE seller_id = ?
                 ORDER BY created_at DESC, id DESC
                 LIMIT 1"
            );
            $stmt->execute([$sellerId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || !isset($row['id'])) {
                return null;
            }

            $reportId = (int)$row['id'];
            return $reportId > 0 ? $reportId : null;
        } catch (Throwable $e) {
            Logger::error('MarketplaceSellerAction getLatestSellerReportId error: ' . $e->getMessage());
            return null;
        }
    }

    private function isLegacyReportIdNotNullError(Throwable $error): bool
    {
        $message = strtolower($error->getMessage());
        return str_contains($message, "column 'report_id' cannot be null")
            || str_contains($message, '1048');
    }
}
