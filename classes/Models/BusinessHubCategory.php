<?php
/**
 * BusinessHubCategory Model
 * Handles business hub categories and their nodes
 */

namespace Karyalay\Models;

use Karyalay\Database\Connection;
use PDO;

class BusinessHubCategory
{
    private PDO $db;
    private const MAX_CATEGORIES = 4;
    private const MAX_NODES_PER_CATEGORY = 6;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * Get all published categories with their nodes
     */
    public function getPublishedWithNodes(): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.*, 
                    GROUP_CONCAT(
                        CONCAT(n.id, '||', n.title, '||', COALESCE(n.link_url, ''), '||', n.slug)
                        ORDER BY n.display_order ASC
                        SEPARATOR ';;'
                    ) as nodes_data
             FROM business_hub_categories c
             LEFT JOIN business_hub_nodes n ON n.category_id = c.id AND n.status = 'PUBLISHED'
             WHERE c.status = 'PUBLISHED'
             GROUP BY c.id
             ORDER BY c.display_order ASC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', self::MAX_CATEGORIES, PDO::PARAM_INT);
        $stmt->execute();
        
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Parse nodes data
        foreach ($categories as &$category) {
            $category['nodes'] = [];
            if (!empty($category['nodes_data'])) {
                $nodesRaw = explode(';;', $category['nodes_data']);
                foreach ($nodesRaw as $nodeRaw) {
                    $parts = explode('||', $nodeRaw);
                    if (count($parts) >= 4) {
                        $category['nodes'][] = [
                            'id' => $parts[0],
                            'title' => $parts[1],
                            'link_url' => $parts[2] ?: null,
                            'slug' => $parts[3]
                        ];
                    }
                }
                // Limit to max nodes
                $category['nodes'] = array_slice($category['nodes'], 0, self::MAX_NODES_PER_CATEGORY);
            }
            unset($category['nodes_data']);
        }
        
        return $categories;
    }

    /**
     * Get all categories with optional filters
     */
    public function getAll(array $filters = [], int $limit = 20, int $offset = 0): array
    {
        $sql = "SELECT c.*, 
                       (SELECT COUNT(*) FROM business_hub_nodes WHERE category_id = c.id) as node_count
                FROM business_hub_categories c WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (c.title LIKE :search OR c.title_line2 LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $sql .= " ORDER BY c.display_order ASC, c.created_at DESC LIMIT :limit OFFSET :offset";

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
     * Count categories with optional filters
     */
    public function count(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM business_hub_categories WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (title LIKE :search OR title_line2 LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get a single category by ID with its nodes
     */
    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM business_hub_categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($category) {
            $category['nodes'] = $this->getNodesByCategory($id);
        }
        
        return $category ?: null;
    }

    /**
     * Get nodes for a category
     */
    public function getNodesByCategory(string $categoryId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM business_hub_nodes WHERE category_id = :category_id ORDER BY display_order ASC"
        );
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single node by ID
     */
    public function getNodeById(string $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM business_hub_nodes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Check if can add more categories
     */
    public function canAddCategory(): bool
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM business_hub_categories");
        return (int) $stmt->fetchColumn() < self::MAX_CATEGORIES;
    }

    /**
     * Check if can add more nodes to a category
     */
    public function canAddNode(string $categoryId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM business_hub_nodes WHERE category_id = :category_id");
        $stmt->execute([':category_id' => $categoryId]);
        return (int) $stmt->fetchColumn() < self::MAX_NODES_PER_CATEGORY;
    }

    /**
     * Get node count for a category
     */
    public function getNodeCount(string $categoryId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM business_hub_nodes WHERE category_id = :category_id");
        $stmt->execute([':category_id' => $categoryId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Create a new category
     */
    public function create(array $data): ?string
    {
        if (!$this->canAddCategory()) {
            return null;
        }

        $id = $this->generateUuid();
        
        $stmt = $this->db->prepare(
            "INSERT INTO business_hub_categories (id, title, title_line2, slug, link_url, color_class, position, display_order, status)
             VALUES (:id, :title, :title_line2, :slug, :link_url, :color_class, :position, :display_order, :status)"
        );

        $result = $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':title_line2' => $data['title_line2'] ?? null,
            ':slug' => $data['slug'] ?? $this->generateSlug($data['title']),
            ':link_url' => $data['link_url'] ?? null,
            ':color_class' => $data['color_class'] ?? 'people',
            ':position' => $data['position'] ?? 'top-left',
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT'
        ]);

        return $result ? $id : null;
    }

    /**
     * Update an existing category
     */
    public function update(string $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE business_hub_categories SET 
                title = :title,
                title_line2 = :title_line2,
                slug = :slug,
                link_url = :link_url,
                color_class = :color_class,
                position = :position,
                display_order = :display_order,
                status = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':title_line2' => $data['title_line2'] ?? null,
            ':slug' => $data['slug'] ?? $this->generateSlug($data['title']),
            ':link_url' => $data['link_url'] ?? null,
            ':color_class' => $data['color_class'] ?? 'people',
            ':position' => $data['position'] ?? 'top-left',
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT'
        ]);
    }

    /**
     * Delete a category (cascades to nodes)
     */
    public function delete(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM business_hub_categories WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Create a new node
     */
    public function createNode(array $data): ?string
    {
        if (!$this->canAddNode($data['category_id'])) {
            return null;
        }

        $id = $this->generateUuid();
        
        $stmt = $this->db->prepare(
            "INSERT INTO business_hub_nodes (id, category_id, title, slug, link_url, display_order, status)
             VALUES (:id, :category_id, :title, :slug, :link_url, :display_order, :status)"
        );

        $result = $stmt->execute([
            ':id' => $id,
            ':category_id' => $data['category_id'],
            ':title' => $data['title'],
            ':slug' => $data['slug'] ?? $this->generateSlug($data['title']),
            ':link_url' => $data['link_url'] ?? null,
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT'
        ]);

        return $result ? $id : null;
    }

    /**
     * Update an existing node
     */
    public function updateNode(string $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE business_hub_nodes SET 
                title = :title,
                slug = :slug,
                link_url = :link_url,
                display_order = :display_order,
                status = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            ':id' => $id,
            ':title' => $data['title'],
            ':slug' => $data['slug'] ?? $this->generateSlug($data['title']),
            ':link_url' => $data['link_url'] ?? null,
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT'
        ]);
    }

    /**
     * Delete a node
     */
    public function deleteNode(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM business_hub_nodes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Generate slug from text
     */
    private function generateSlug(string $text): string
    {
        $slug = strtolower(trim($text));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
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

    /**
     * Get max categories constant
     */
    public static function getMaxCategories(): int
    {
        return self::MAX_CATEGORIES;
    }

    /**
     * Get max nodes per category constant
     */
    public static function getMaxNodesPerCategory(): int
    {
        return self::MAX_NODES_PER_CATEGORY;
    }
}
