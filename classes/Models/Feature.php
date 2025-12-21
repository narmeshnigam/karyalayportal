<?php

namespace Karyalay\Models;

use Karyalay\Database\Connection;
use PDO;
use PDOException;

/**
 * Feature Model Class
 * 
 * Handles CRUD operations for features and feature_styling tables
 */
class Feature
{
    private PDO $db;

    // Fields that belong to each table
    private array $coreFields = [
        'name', 'slug', 'description', 'icon_image', 'benefits', 
        'related_solutions', 'screenshots', 'display_order', 'status'
    ];

    private array $stylingFields = [
        // Hero Section
        'hero_bg_color', 'hero_bg_gradient_start', 'hero_bg_gradient_end',
        'hero_title_gradient_start', 'hero_title_gradient_middle', 'hero_title_gradient_end',
        'hero_subtitle_color',
        'hero_decor_line_color', 'hero_decor_circle_color',
        'hero_cta_primary_text', 'hero_cta_primary_link', 'hero_cta_primary_bg_color',
        'hero_cta_primary_text_color', 'hero_cta_primary_hover_bg_color',
        'hero_cta_secondary_text', 'hero_cta_secondary_link', 'hero_cta_secondary_bg_color',
        'hero_cta_secondary_text_color', 'hero_cta_secondary_border_color',
        'hero_stats_enabled', 'hero_stat1_value', 'hero_stat1_label',
        'hero_stat2_value', 'hero_stat2_label', 'hero_stat3_value', 'hero_stat3_label',
        'hero_stats_value_color', 'hero_stats_label_color',
        'hero_breadcrumb_link_color', 'hero_breadcrumb_active_color', 'hero_breadcrumb_separator_color',
        // Key Benefits Section
        'benefits_section_enabled', 'benefits_section_heading1', 'benefits_section_heading2',
        'benefits_section_subheading', 'benefits_section_bg_color',
        'benefits_section_heading_color', 'benefits_section_subheading_color',
        'benefits_card_bg_color', 'benefits_card_border_color', 'benefits_card_hover_bg_color',
        'benefits_card_title_color', 'benefits_card_text_color', 'benefits_card_icon_color',
        'benefits_card_hover_text_color', 'benefits_cards',
        // How It Works Section
        'how_it_works_enabled', 'how_it_works_badge', 'how_it_works_heading', 'how_it_works_subheading',
        'how_it_works_bg_color', 'how_it_works_badge_bg_color', 'how_it_works_badge_text_color',
        'how_it_works_heading_color', 'how_it_works_subheading_color',
        'how_it_works_card_bg_color', 'how_it_works_card_border_color', 'how_it_works_card_hover_border_color',
        'how_it_works_step_color', 'how_it_works_step_bg_color',
        'how_it_works_title_color', 'how_it_works_desc_color', 'how_it_works_connector_color',
        'how_it_works_steps',
        // Feature Highlights Section
        'highlights_enabled', 'highlights_badge', 'highlights_heading', 'highlights_subheading',
        'highlights_bg_color', 'highlights_badge_bg_color', 'highlights_badge_text_color',
        'highlights_heading_color', 'highlights_subheading_color',
        'highlights_card_bg_color', 'highlights_card_border_color', 'highlights_card_hover_border_color',
        'highlights_icon_bg_color', 'highlights_icon_color',
        'highlights_title_color', 'highlights_desc_color', 'highlights_cards',
        // Use Cases Section
        'use_cases_enabled', 'use_cases_badge', 'use_cases_heading', 'use_cases_subheading',
        'use_cases_bg_color', 'use_cases_badge_bg_color', 'use_cases_badge_text_color',
        'use_cases_heading_color', 'use_cases_subheading_color',
        'use_cases_card_bg_color', 'use_cases_card_border_color', 'use_cases_card_hover_border_color',
        'use_cases_title_color', 'use_cases_desc_color', 'use_cases_overlay_color', 'use_cases_cards',
        // FAQs Section
        'faqs_section_theme', 'faqs_section_heading', 'faqs_section_subheading', 'faqs_cards'
    ];

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }


    /**
     * Create a new feature with styling
     */
    public function create(array $data)
    {
        try {
            $this->db->beginTransaction();
            
            $featureId = $this->generateUuid();
            
            // Insert into features table
            $this->insertFeature($featureId, $data);
            
            // Insert into feature_styling table
            $this->insertStyling($featureId, $data);
            
            $this->db->commit();
            return $this->findById($featureId);
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Feature creation failed: ' . $e->getMessage());
            return false;
        }
    }

    private function insertFeature(string $id, array $data): void
    {
        $sql = "INSERT INTO features (
            id, name, slug, description, icon_image, benefits, related_solutions, screenshots, display_order, status
        ) VALUES (
            :id, :name, :slug, :description, :icon_image, :benefits, :related_solutions, :screenshots, :display_order, :status
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => !empty($data['description']) ? $data['description'] : null,
            ':icon_image' => !empty($data['icon_image']) ? $data['icon_image'] : null,
            ':benefits' => isset($data['benefits']) ? json_encode($data['benefits']) : null,
            ':related_solutions' => isset($data['related_solutions']) ? json_encode($data['related_solutions']) : null,
            ':screenshots' => isset($data['screenshots']) ? json_encode($data['screenshots']) : null,
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT'
        ]);
    }

    private function insertStyling(string $featureId, array $data): void
    {
        $sql = "INSERT INTO feature_styling (
            id, feature_id,
            hero_bg_color, hero_bg_gradient_start, hero_bg_gradient_end,
            hero_title_gradient_start, hero_title_gradient_middle, hero_title_gradient_end,
            hero_subtitle_color, hero_decor_line_color, hero_decor_circle_color,
            hero_cta_primary_text, hero_cta_primary_link, hero_cta_primary_bg_color,
            hero_cta_primary_text_color, hero_cta_primary_hover_bg_color,
            hero_cta_secondary_text, hero_cta_secondary_link, hero_cta_secondary_bg_color,
            hero_cta_secondary_text_color, hero_cta_secondary_border_color,
            hero_stats_enabled, hero_stat1_value, hero_stat1_label,
            hero_stat2_value, hero_stat2_label, hero_stat3_value, hero_stat3_label,
            hero_stats_value_color, hero_stats_label_color,
            hero_breadcrumb_link_color, hero_breadcrumb_active_color, hero_breadcrumb_separator_color,
            benefits_section_enabled, benefits_section_heading1, benefits_section_heading2,
            benefits_section_subheading, benefits_section_bg_color,
            benefits_section_heading_color, benefits_section_subheading_color,
            benefits_card_bg_color, benefits_card_border_color, benefits_card_hover_bg_color,
            benefits_card_title_color, benefits_card_text_color, benefits_card_icon_color,
            benefits_card_hover_text_color,
            how_it_works_enabled, how_it_works_badge, how_it_works_heading, how_it_works_subheading,
            how_it_works_bg_color, how_it_works_badge_bg_color, how_it_works_badge_text_color,
            how_it_works_heading_color, how_it_works_subheading_color,
            how_it_works_card_bg_color, how_it_works_card_border_color, how_it_works_card_hover_border_color,
            how_it_works_step_color, how_it_works_step_bg_color,
            how_it_works_title_color, how_it_works_desc_color, how_it_works_connector_color,
            how_it_works_steps
        ) VALUES (
            :id, :feature_id,
            :hero_bg_color, :hero_bg_gradient_start, :hero_bg_gradient_end,
            :hero_title_gradient_start, :hero_title_gradient_middle, :hero_title_gradient_end,
            :hero_subtitle_color, :hero_decor_line_color, :hero_decor_circle_color,
            :hero_cta_primary_text, :hero_cta_primary_link, :hero_cta_primary_bg_color,
            :hero_cta_primary_text_color, :hero_cta_primary_hover_bg_color,
            :hero_cta_secondary_text, :hero_cta_secondary_link, :hero_cta_secondary_bg_color,
            :hero_cta_secondary_text_color, :hero_cta_secondary_border_color,
            :hero_stats_enabled, :hero_stat1_value, :hero_stat1_label,
            :hero_stat2_value, :hero_stat2_label, :hero_stat3_value, :hero_stat3_label,
            :hero_stats_value_color, :hero_stats_label_color,
            :hero_breadcrumb_link_color, :hero_breadcrumb_active_color, :hero_breadcrumb_separator_color,
            :benefits_section_enabled, :benefits_section_heading1, :benefits_section_heading2,
            :benefits_section_subheading, :benefits_section_bg_color,
            :benefits_section_heading_color, :benefits_section_subheading_color,
            :benefits_card_bg_color, :benefits_card_border_color, :benefits_card_hover_bg_color,
            :benefits_card_title_color, :benefits_card_text_color, :benefits_card_icon_color,
            :benefits_card_hover_text_color,
            :how_it_works_enabled, :how_it_works_badge, :how_it_works_heading, :how_it_works_subheading,
            :how_it_works_bg_color, :how_it_works_badge_bg_color, :how_it_works_badge_text_color,
            :how_it_works_heading_color, :how_it_works_subheading_color,
            :how_it_works_card_bg_color, :how_it_works_card_border_color, :how_it_works_card_hover_border_color,
            :how_it_works_step_color, :how_it_works_step_bg_color,
            :how_it_works_title_color, :how_it_works_desc_color, :how_it_works_connector_color,
            :how_it_works_steps
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $this->generateUuid(),
            ':feature_id' => $featureId,
            ':hero_bg_color' => $data['hero_bg_color'] ?? '#fafafa',
            ':hero_bg_gradient_start' => $data['hero_bg_gradient_start'] ?? '#fafafa',
            ':hero_bg_gradient_end' => $data['hero_bg_gradient_end'] ?? '#f5f5f5',
            ':hero_title_gradient_start' => $data['hero_title_gradient_start'] ?? '#111827',
            ':hero_title_gradient_middle' => $data['hero_title_gradient_middle'] ?? '#667eea',
            ':hero_title_gradient_end' => $data['hero_title_gradient_end'] ?? '#764ba2',
            ':hero_subtitle_color' => $data['hero_subtitle_color'] ?? '#6b7280',
            ':hero_decor_line_color' => $data['hero_decor_line_color'] ?? 'rgba(102, 126, 234, 0.08)',
            ':hero_decor_circle_color' => $data['hero_decor_circle_color'] ?? 'rgba(102, 126, 234, 0.1)',
            ':hero_cta_primary_text' => $data['hero_cta_primary_text'] ?? 'Get Started',
            ':hero_cta_primary_link' => $data['hero_cta_primary_link'] ?? '#contact-form',
            ':hero_cta_primary_bg_color' => $data['hero_cta_primary_bg_color'] ?? '#667eea',
            ':hero_cta_primary_text_color' => $data['hero_cta_primary_text_color'] ?? '#FFFFFF',
            ':hero_cta_primary_hover_bg_color' => $data['hero_cta_primary_hover_bg_color'] ?? '#5a6fd6',
            ':hero_cta_secondary_text' => $data['hero_cta_secondary_text'] ?? 'Learn how it works',
            ':hero_cta_secondary_link' => $data['hero_cta_secondary_link'] ?? '#how-it-works',
            ':hero_cta_secondary_bg_color' => $data['hero_cta_secondary_bg_color'] ?? 'transparent',
            ':hero_cta_secondary_text_color' => $data['hero_cta_secondary_text_color'] ?? '#374151',
            ':hero_cta_secondary_border_color' => $data['hero_cta_secondary_border_color'] ?? '#e5e7eb',
            ':hero_stats_enabled' => $data['hero_stats_enabled'] ?? true,
            ':hero_stat1_value' => $data['hero_stat1_value'] ?? '30+',
            ':hero_stat1_label' => $data['hero_stat1_label'] ?? 'Modules',
            ':hero_stat2_value' => $data['hero_stat2_value'] ?? '500+',
            ':hero_stat2_label' => $data['hero_stat2_label'] ?? 'Businesses',
            ':hero_stat3_value' => $data['hero_stat3_value'] ?? '24/7',
            ':hero_stat3_label' => $data['hero_stat3_label'] ?? 'Support',
            ':hero_stats_value_color' => $data['hero_stats_value_color'] ?? '#111827',
            ':hero_stats_label_color' => $data['hero_stats_label_color'] ?? '#9ca3af',
            ':hero_breadcrumb_link_color' => $data['hero_breadcrumb_link_color'] ?? '#9ca3af',
            ':hero_breadcrumb_active_color' => $data['hero_breadcrumb_active_color'] ?? '#374151',
            ':hero_breadcrumb_separator_color' => $data['hero_breadcrumb_separator_color'] ?? '#d1d5db',
            ':benefits_section_enabled' => $data['benefits_section_enabled'] ?? true,
            ':benefits_section_heading1' => $data['benefits_section_heading1'] ?? 'Why Choose',
            ':benefits_section_heading2' => $data['benefits_section_heading2'] ?? '',
            ':benefits_section_subheading' => $data['benefits_section_subheading'] ?? 'Discover the key advantages that make this feature essential for your business operations.',
            ':benefits_section_bg_color' => $data['benefits_section_bg_color'] ?? '#0f172a',
            ':benefits_section_heading_color' => $data['benefits_section_heading_color'] ?? '#FFFFFF',
            ':benefits_section_subheading_color' => $data['benefits_section_subheading_color'] ?? 'rgba(255,255,255,0.6)',
            ':benefits_card_bg_color' => $data['benefits_card_bg_color'] ?? 'rgba(255,255,255,0.06)',
            ':benefits_card_border_color' => $data['benefits_card_border_color'] ?? 'rgba(255,255,255,0.1)',
            ':benefits_card_hover_bg_color' => $data['benefits_card_hover_bg_color'] ?? '#667eea',
            ':benefits_card_title_color' => $data['benefits_card_title_color'] ?? '#FFFFFF',
            ':benefits_card_text_color' => $data['benefits_card_text_color'] ?? 'rgba(255,255,255,0.5)',
            ':benefits_card_icon_color' => $data['benefits_card_icon_color'] ?? 'rgba(255,255,255,0.5)',
            ':benefits_card_hover_text_color' => $data['benefits_card_hover_text_color'] ?? '#FFFFFF',
            // How It Works Section
            ':how_it_works_enabled' => $data['how_it_works_enabled'] ?? true,
            ':how_it_works_badge' => $data['how_it_works_badge'] ?? 'Simple Process',
            ':how_it_works_heading' => $data['how_it_works_heading'] ?? 'How It Works',
            ':how_it_works_subheading' => $data['how_it_works_subheading'] ?? 'Get started in four simple steps and transform your business operations',
            ':how_it_works_bg_color' => $data['how_it_works_bg_color'] ?? '#f9fafb',
            ':how_it_works_badge_bg_color' => $data['how_it_works_badge_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
            ':how_it_works_badge_text_color' => $data['how_it_works_badge_text_color'] ?? '#667eea',
            ':how_it_works_heading_color' => $data['how_it_works_heading_color'] ?? '#111827',
            ':how_it_works_subheading_color' => $data['how_it_works_subheading_color'] ?? '#6b7280',
            ':how_it_works_card_bg_color' => $data['how_it_works_card_bg_color'] ?? '#ffffff',
            ':how_it_works_card_border_color' => $data['how_it_works_card_border_color'] ?? '#e5e7eb',
            ':how_it_works_card_hover_border_color' => $data['how_it_works_card_hover_border_color'] ?? '#667eea',
            ':how_it_works_step_color' => $data['how_it_works_step_color'] ?? '#667eea',
            ':how_it_works_step_bg_color' => $data['how_it_works_step_bg_color'] ?? 'rgba(102, 126, 234, 0.1)',
            ':how_it_works_title_color' => $data['how_it_works_title_color'] ?? '#111827',
            ':how_it_works_desc_color' => $data['how_it_works_desc_color'] ?? '#6b7280',
            ':how_it_works_connector_color' => $data['how_it_works_connector_color'] ?? '#d1d5db',
            ':how_it_works_steps' => isset($data['how_it_works_steps']) ? json_encode($data['how_it_works_steps']) : null
        ]);
    }


    /**
     * Find feature by ID with styling
     */
    public function findById(string $id)
    {
        try {
            $sql = "SELECT f.*, 
                    fs.hero_bg_color, fs.hero_bg_gradient_start, fs.hero_bg_gradient_end,
                    fs.hero_title_gradient_start, fs.hero_title_gradient_middle, fs.hero_title_gradient_end,
                    fs.hero_subtitle_color, fs.hero_decor_line_color, fs.hero_decor_circle_color,
                    fs.hero_cta_primary_text, fs.hero_cta_primary_link, fs.hero_cta_primary_bg_color,
                    fs.hero_cta_primary_text_color, fs.hero_cta_primary_hover_bg_color,
                    fs.hero_cta_secondary_text, fs.hero_cta_secondary_link, fs.hero_cta_secondary_bg_color,
                    fs.hero_cta_secondary_text_color, fs.hero_cta_secondary_border_color,
                    fs.hero_stats_enabled, fs.hero_stat1_value, fs.hero_stat1_label,
                    fs.hero_stat2_value, fs.hero_stat2_label, fs.hero_stat3_value, fs.hero_stat3_label,
                    fs.hero_stats_value_color, fs.hero_stats_label_color,
                    fs.hero_breadcrumb_link_color, fs.hero_breadcrumb_active_color, fs.hero_breadcrumb_separator_color,
                    fs.benefits_section_enabled, fs.benefits_section_heading1, fs.benefits_section_heading2,
                    fs.benefits_section_subheading, fs.benefits_section_bg_color,
                    fs.benefits_section_heading_color, fs.benefits_section_subheading_color,
                    fs.benefits_card_bg_color, fs.benefits_card_border_color, fs.benefits_card_hover_bg_color,
                    fs.benefits_card_title_color, fs.benefits_card_text_color, fs.benefits_card_icon_color,
                    fs.benefits_card_hover_text_color, fs.benefits_cards,
                    fs.how_it_works_enabled, fs.how_it_works_badge, fs.how_it_works_heading, fs.how_it_works_subheading,
                    fs.how_it_works_bg_color, fs.how_it_works_badge_bg_color, fs.how_it_works_badge_text_color,
                    fs.how_it_works_heading_color, fs.how_it_works_subheading_color,
                    fs.how_it_works_card_bg_color, fs.how_it_works_card_border_color, fs.how_it_works_card_hover_border_color,
                    fs.how_it_works_step_color, fs.how_it_works_step_bg_color,
                    fs.how_it_works_title_color, fs.how_it_works_desc_color, fs.how_it_works_connector_color,
                    fs.how_it_works_steps,
                    fs.highlights_enabled, fs.highlights_badge, fs.highlights_heading, fs.highlights_subheading,
                    fs.highlights_bg_color, fs.highlights_badge_bg_color, fs.highlights_badge_text_color,
                    fs.highlights_heading_color, fs.highlights_subheading_color,
                    fs.highlights_card_bg_color, fs.highlights_card_border_color, fs.highlights_card_hover_border_color,
                    fs.highlights_icon_bg_color, fs.highlights_icon_color,
                    fs.highlights_title_color, fs.highlights_desc_color, fs.highlights_cards,
                    fs.use_cases_enabled, fs.use_cases_badge, fs.use_cases_heading, fs.use_cases_subheading,
                    fs.use_cases_bg_color, fs.use_cases_badge_bg_color, fs.use_cases_badge_text_color,
                    fs.use_cases_heading_color, fs.use_cases_subheading_color,
                    fs.use_cases_card_bg_color, fs.use_cases_card_border_color, fs.use_cases_card_hover_border_color,
                    fs.use_cases_title_color, fs.use_cases_desc_color, fs.use_cases_overlay_color, fs.use_cases_cards,
                    fs.faqs_section_theme, fs.faqs_section_heading, fs.faqs_section_subheading, fs.faqs_cards
                FROM features f
                LEFT JOIN feature_styling fs ON f.id = fs.feature_id
                WHERE f.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $result = $this->decodeJsonFields($result);
            }
            
            return $result ?: false;
        } catch (PDOException $e) {
            error_log('Feature find by ID failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find feature by slug with styling
     */
    public function findBySlug(string $slug)
    {
        try {
            $sql = "SELECT f.*, 
                    fs.hero_bg_color, fs.hero_bg_gradient_start, fs.hero_bg_gradient_end,
                    fs.hero_title_gradient_start, fs.hero_title_gradient_middle, fs.hero_title_gradient_end,
                    fs.hero_subtitle_color, fs.hero_decor_line_color, fs.hero_decor_circle_color,
                    fs.hero_cta_primary_text, fs.hero_cta_primary_link, fs.hero_cta_primary_bg_color,
                    fs.hero_cta_primary_text_color, fs.hero_cta_primary_hover_bg_color,
                    fs.hero_cta_secondary_text, fs.hero_cta_secondary_link, fs.hero_cta_secondary_bg_color,
                    fs.hero_cta_secondary_text_color, fs.hero_cta_secondary_border_color,
                    fs.hero_stats_enabled, fs.hero_stat1_value, fs.hero_stat1_label,
                    fs.hero_stat2_value, fs.hero_stat2_label, fs.hero_stat3_value, fs.hero_stat3_label,
                    fs.hero_stats_value_color, fs.hero_stats_label_color,
                    fs.hero_breadcrumb_link_color, fs.hero_breadcrumb_active_color, fs.hero_breadcrumb_separator_color,
                    fs.benefits_section_enabled, fs.benefits_section_heading1, fs.benefits_section_heading2,
                    fs.benefits_section_subheading, fs.benefits_section_bg_color,
                    fs.benefits_section_heading_color, fs.benefits_section_subheading_color,
                    fs.benefits_card_bg_color, fs.benefits_card_border_color, fs.benefits_card_hover_bg_color,
                    fs.benefits_card_title_color, fs.benefits_card_text_color, fs.benefits_card_icon_color,
                    fs.benefits_card_hover_text_color, fs.benefits_cards,
                    fs.how_it_works_enabled, fs.how_it_works_badge, fs.how_it_works_heading, fs.how_it_works_subheading,
                    fs.how_it_works_bg_color, fs.how_it_works_badge_bg_color, fs.how_it_works_badge_text_color,
                    fs.how_it_works_heading_color, fs.how_it_works_subheading_color,
                    fs.how_it_works_card_bg_color, fs.how_it_works_card_border_color, fs.how_it_works_card_hover_border_color,
                    fs.how_it_works_step_color, fs.how_it_works_step_bg_color,
                    fs.how_it_works_title_color, fs.how_it_works_desc_color, fs.how_it_works_connector_color,
                    fs.how_it_works_steps,
                    fs.highlights_enabled, fs.highlights_badge, fs.highlights_heading, fs.highlights_subheading,
                    fs.highlights_bg_color, fs.highlights_badge_bg_color, fs.highlights_badge_text_color,
                    fs.highlights_heading_color, fs.highlights_subheading_color,
                    fs.highlights_card_bg_color, fs.highlights_card_border_color, fs.highlights_card_hover_border_color,
                    fs.highlights_icon_bg_color, fs.highlights_icon_color,
                    fs.highlights_title_color, fs.highlights_desc_color, fs.highlights_cards,
                    fs.use_cases_enabled, fs.use_cases_badge, fs.use_cases_heading, fs.use_cases_subheading,
                    fs.use_cases_bg_color, fs.use_cases_badge_bg_color, fs.use_cases_badge_text_color,
                    fs.use_cases_heading_color, fs.use_cases_subheading_color,
                    fs.use_cases_card_bg_color, fs.use_cases_card_border_color, fs.use_cases_card_hover_border_color,
                    fs.use_cases_title_color, fs.use_cases_desc_color, fs.use_cases_overlay_color, fs.use_cases_cards,
                    fs.faqs_section_theme, fs.faqs_section_heading, fs.faqs_section_subheading, fs.faqs_cards
                FROM features f
                LEFT JOIN feature_styling fs ON f.id = fs.feature_id
                WHERE f.slug = :slug";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':slug' => $slug]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $result = $this->decodeJsonFields($result);
            }
            
            return $result ?: false;
        } catch (PDOException $e) {
            error_log('Feature find by slug failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update feature data across both tables
     */
    public function update(string $id, array $data): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Separate data by table
            $coreData = [];
            $stylingData = [];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $this->coreFields)) {
                    $coreData[$key] = $value;
                } elseif (in_array($key, $this->stylingFields)) {
                    $stylingData[$key] = $value;
                }
            }
            
            // Update features table
            if (!empty($coreData)) {
                $this->updateTable('features', 'id', $id, $coreData);
            }
            
            // Update feature_styling table
            if (!empty($stylingData)) {
                // Check if styling record exists
                $checkSql = "SELECT COUNT(*) FROM feature_styling WHERE feature_id = :feature_id";
                $checkStmt = $this->db->prepare($checkSql);
                $checkStmt->execute([':feature_id' => $id]);
                
                if ($checkStmt->fetchColumn() > 0) {
                    $this->updateTable('feature_styling', 'feature_id', $id, $stylingData);
                } else {
                    // Insert new styling record with provided data merged with defaults
                    $this->insertStyling($id, $stylingData);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Feature update failed: ' . $e->getMessage() . ' - SQL State: ' . $e->getCode());
            return false;
        }
    }
    
    /**
     * Ensure styling record exists for a feature
     */
    public function ensureStylingExists(string $featureId): bool
    {
        try {
            $checkSql = "SELECT COUNT(*) FROM feature_styling WHERE feature_id = :feature_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([':feature_id' => $featureId]);
            
            if ($checkStmt->fetchColumn() == 0) {
                $this->insertStyling($featureId, []);
                return true;
            }
            return true;
        } catch (PDOException $e) {
            error_log('Failed to ensure styling exists: ' . $e->getMessage());
            return false;
        }
    }

    private function updateTable(string $table, string $idColumn, string $id, array $data): void
    {
        $updateFields = [];
        $params = [":$idColumn" => $id];

        foreach ($data as $key => $value) {
            if (in_array($key, ['benefits', 'related_solutions', 'screenshots', 'benefits_cards', 'how_it_works_steps', 'highlights_cards', 'use_cases_cards', 'faqs_cards'])) {
                $updateFields[] = "$key = :$key";
                $params[":$key"] = is_array($value) ? json_encode($value) : $value;
            } elseif (in_array($key, ['hero_stats_enabled', 'benefits_section_enabled', 'how_it_works_enabled', 'highlights_enabled', 'use_cases_enabled'])) {
                // Handle boolean fields
                $updateFields[] = "$key = :$key";
                $params[":$key"] = $value ? 1 : 0;
            } else {
                $updateFields[] = "$key = :$key";
                if (in_array($key, ['description', 'icon_image']) && $value === '') {
                    $params[":$key"] = null;
                } else {
                    $params[":$key"] = $value;
                }
            }
        }

        if (empty($updateFields)) {
            return;
        }

        $sql = "UPDATE $table SET " . implode(', ', $updateFields) . " WHERE $idColumn = :$idColumn";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }


    /**
     * Delete feature
     */
    public function delete(string $id): bool
    {
        try {
            // feature_styling will be deleted automatically due to CASCADE
            $sql = "DELETE FROM features WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Feature deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all features with optional filters
     */
    public function findAll(array $filters = [], int $limit = 100, int $offset = 0): array
    {
        try {
            $sql = "SELECT f.*, 
                    fs.hero_bg_color, fs.hero_bg_gradient_start, fs.hero_bg_gradient_end,
                    fs.hero_title_gradient_start, fs.hero_title_gradient_middle, fs.hero_title_gradient_end,
                    fs.hero_subtitle_color,
                    fs.hero_cta_primary_text, fs.hero_cta_primary_link, fs.hero_cta_primary_bg_color,
                    fs.hero_cta_primary_text_color, fs.hero_cta_primary_hover_bg_color,
                    fs.hero_cta_secondary_text, fs.hero_cta_secondary_link,
                    fs.hero_stats_enabled, fs.hero_stat1_value, fs.hero_stat1_label,
                    fs.hero_stat2_value, fs.hero_stat2_label, fs.hero_stat3_value, fs.hero_stat3_label
                FROM features f
                LEFT JOIN feature_styling fs ON f.id = fs.feature_id
                WHERE 1=1";
            $params = [];

            if (isset($filters['status'])) {
                $sql .= " AND f.status = :status";
                $params[':status'] = $filters['status'];
            }

            $sql .= " ORDER BY f.display_order ASC, f.created_at DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as &$result) {
                $result = $this->decodeJsonFields($result);
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log('Feature findAll failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug, ?string $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM features WHERE slug = :slug";
            $params = [':slug' => $slug];

            if ($excludeId !== null) {
                $sql .= " AND id != :id";
                $params[':id'] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log('Slug exists check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Decode JSON fields in feature data
     */
    private function decodeJsonFields(array $data): array
    {
        $jsonFields = ['benefits', 'related_solutions', 'screenshots', 'benefits_cards', 'how_it_works_steps', 'highlights_cards', 'use_cases_cards', 'faqs_cards'];
        
        foreach ($jsonFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }
        
        return $data;
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
