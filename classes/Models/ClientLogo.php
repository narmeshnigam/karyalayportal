<?php
/**
 * ClientLogo Model
 * Handles client logos for the hero slider marquee
 */

namespace Karyalay\Models;

use Karyalay\Database\Connection;
use PDO;

class ClientLogo
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * Get all published logos ordered by display_order
     */
    public function getPublishedLogos(): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM client_logos WHERE status = 'PUBLISHED' ORDER BY display_order ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all logos with optional filters
     */
    public function getAll(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT * FROM client_logos WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND client_name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY display_order ASC, created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count logos with optional filters
     */
    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM client_logos WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND client_name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get a single logo by ID
     */
    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM client_logos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Create a new logo
     */
    public function create(array $data): ?string
    {
        $id = $this->generateUuid();
        
        $stmt = $this->db->prepare(
            "INSERT INTO client_logos (id, client_name, logo_url, website_url, display_order, status)
             VALUES (:id, :client_name, :logo_url, :website_url, :display_order, :status)"
        );

        $result = $stmt->execute([
            ':id' => $id,
            ':client_name' => $data['client_name'],
            ':logo_url' => $data['logo_url'],
            ':website_url' => $data['website_url'] ?? null,
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT'
        ]);

        return $result ? $id : null;
    }

    /**
     * Update an existing logo
     */
    public function update(string $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE client_logos SET 
                client_name = :client_name,
                logo_url = :logo_url,
                website_url = :website_url,
                display_order = :display_order,
                status = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            ':id' => $id,
            ':client_name' => $data['client_name'],
            ':logo_url' => $data['logo_url'],
            ':website_url' => $data['website_url'] ?? null,
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT'
        ]);
    }

    /**
     * Delete a logo
     */
    public function delete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM client_logos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Generate UUID v4
     */
    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
