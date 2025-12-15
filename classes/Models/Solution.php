<?php

namespace Karyalay\Models;

use Karyalay\Database\Connection;
use PDO;
use PDOException;

/**
 * Solution Model Class
 * 
 * Handles CRUD operations for solutions, solution_styling, and solution_content tables
 */
class Solution
{
    private PDO $db;

    // Fields that belong to each table
    private array $coreFields = [
        'name', 'slug', 'description', 'tagline', 'subtitle', 'icon_image',
        'video_url', 'demo_video_url', 'color_theme', 'testimonial_id', 'pricing_note',
        'meta_title', 'meta_description', 'meta_keywords',
        'display_order', 'status', 'featured_on_homepage'
    ];

    private array $stylingFields = [
        'hero_badge', 'hero_title_text', 'hero_title_color', 'hero_subtitle_color',
        'hero_media_url', 'hero_media_type', 'hero_bg_color', 'hero_bg_gradient_color',
        'hero_bg_gradient_opacity', 'hero_bg_pattern_opacity',
        'hero_cta_primary_text', 'hero_cta_primary_link',
        'hero_cta_secondary_text', 'hero_cta_secondary_link',
        'hero_primary_btn_bg_color', 'hero_primary_btn_text_color',
        'hero_primary_btn_text_hover_color', 'hero_primary_btn_border_color',
        'hero_secondary_btn_bg_color', 'hero_secondary_btn_text_color',
        'hero_secondary_btn_text_hover_color', 'hero_secondary_btn_border_color',
        'key_benefits_section_enabled', 'key_benefits_section_bg_color',
        'key_benefits_section_heading1', 'key_benefits_section_heading2', 'key_benefits_section_subheading',
        'key_benefits_section_heading_color', 'key_benefits_section_subheading_color',
        'key_benefits_section_card_bg_color', 'key_benefits_section_card_border_color',
        'key_benefits_section_card_hover_bg_color', 'key_benefits_section_card_text_color',
        'key_benefits_section_card_icon_color',
        'feature_showcase_section_enabled', 'feature_showcase_section_title', 'feature_showcase_section_subtitle',
        'feature_showcase_section_bg_color', 'feature_showcase_section_title_color', 'feature_showcase_section_subtitle_color',
        'feature_showcase_card_bg_color', 'feature_showcase_card_border_color',
        'feature_showcase_card_badge_bg_color', 'feature_showcase_card_badge_text_color',
        'feature_showcase_card_heading_color', 'feature_showcase_card_text_color', 'feature_showcase_card_icon_color',
        'cta_banner_enabled', 'cta_banner_image_url', 'cta_banner_overlay_color', 'cta_banner_overlay_intensity',
        'cta_banner_heading1', 'cta_banner_heading2', 'cta_banner_heading_color',
        'cta_banner_button_text', 'cta_banner_button_link', 'cta_banner_button_bg_color', 'cta_banner_button_text_color',
        // Industries Section Styling
        'industries_section_enabled', 'industries_section_title', 'industries_section_subtitle',
        'industries_section_bg_color', 'industries_section_title_color', 'industries_section_subtitle_color',
        'industries_section_card_overlay_color', 'industries_section_card_title_color', 'industries_section_card_desc_color',
        'industries_section_card_btn_bg_color', 'industries_section_card_btn_text_color',
        // Testimonials Section Styling
        'testimonials_section_theme', 'testimonials_section_heading', 'testimonials_section_subheading',
        // FAQs Section Styling
        'faqs_section_theme', 'faqs_section_heading', 'faqs_section_subheading'
    ];

    private array $contentFields = [
        'features', 'screenshots', 'faqs', 'key_benefits_cards', 'feature_showcase_cards', 'industries_cards'
    ];

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * Create a new solution with styling and content
     */
    public function create(array $data)
    {
        try {
            $this->db->beginTransaction();
            
            $solutionId = $this->generateUuid();
            
            // Insert into solutions table
            $this->insertSolution($solutionId, $data);
            
            // Insert into solution_styling table
            $this->insertStyling($solutionId, $data);
            
            // Insert into solution_content table
            $this->insertContent($solutionId, $data);
            
            $this->db->commit();
            return $this->findById($solutionId);
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Solution creation failed: ' . $e->getMessage());
            return false;
        }
    }

    private function insertSolution(string $id, array $data): void
    {
        $sql = "INSERT INTO solutions (
            id, name, slug, description, tagline, subtitle, icon_image,
            video_url, demo_video_url, color_theme, testimonial_id, pricing_note,
            meta_title, meta_description, meta_keywords,
            display_order, status, featured_on_homepage
        ) VALUES (
            :id, :name, :slug, :description, :tagline, :subtitle, :icon_image,
            :video_url, :demo_video_url, :color_theme, :testimonial_id, :pricing_note,
            :meta_title, :meta_description, :meta_keywords,
            :display_order, :status, :featured_on_homepage
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?? null,
            ':tagline' => $data['tagline'] ?? null,
            ':subtitle' => $data['subtitle'] ?? null,
            ':icon_image' => $data['icon_image'] ?? null,
            ':video_url' => $data['video_url'] ?? null,
            ':demo_video_url' => $data['demo_video_url'] ?? null,
            ':color_theme' => $data['color_theme'] ?? '#667eea',
            ':testimonial_id' => $data['testimonial_id'] ?? null,
            ':pricing_note' => $data['pricing_note'] ?? null,
            ':meta_title' => $data['meta_title'] ?? null,
            ':meta_description' => $data['meta_description'] ?? null,
            ':meta_keywords' => $data['meta_keywords'] ?? null,
            ':display_order' => $data['display_order'] ?? 0,
            ':status' => $data['status'] ?? 'DRAFT',
            ':featured_on_homepage' => $data['featured_on_homepage'] ?? false
        ]);
    }


    private function insertStyling(string $solutionId, array $data): void
    {
        $sql = "INSERT INTO solution_styling (
            id, solution_id,
            hero_badge, hero_title_text, hero_title_color, hero_subtitle_color,
            hero_media_url, hero_media_type, hero_bg_color, hero_bg_gradient_color,
            hero_bg_gradient_opacity, hero_bg_pattern_opacity,
            hero_cta_primary_text, hero_cta_primary_link,
            hero_cta_secondary_text, hero_cta_secondary_link,
            hero_primary_btn_bg_color, hero_primary_btn_text_color, hero_primary_btn_text_hover_color, hero_primary_btn_border_color,
            hero_secondary_btn_bg_color, hero_secondary_btn_text_color, hero_secondary_btn_text_hover_color, hero_secondary_btn_border_color,
            key_benefits_section_enabled, key_benefits_section_bg_color,
            key_benefits_section_heading1, key_benefits_section_heading2, key_benefits_section_subheading,
            key_benefits_section_heading_color, key_benefits_section_subheading_color,
            key_benefits_section_card_bg_color, key_benefits_section_card_border_color,
            key_benefits_section_card_hover_bg_color, key_benefits_section_card_text_color, key_benefits_section_card_icon_color,
            feature_showcase_section_enabled, feature_showcase_section_title, feature_showcase_section_subtitle,
            feature_showcase_section_bg_color, feature_showcase_section_title_color, feature_showcase_section_subtitle_color,
            feature_showcase_card_bg_color, feature_showcase_card_border_color,
            feature_showcase_card_badge_bg_color, feature_showcase_card_badge_text_color,
            feature_showcase_card_heading_color, feature_showcase_card_text_color, feature_showcase_card_icon_color,
            cta_banner_enabled, cta_banner_image_url, cta_banner_overlay_color, cta_banner_overlay_intensity,
            cta_banner_heading1, cta_banner_heading2, cta_banner_heading_color,
            cta_banner_button_text, cta_banner_button_link, cta_banner_button_bg_color, cta_banner_button_text_color,
            industries_section_enabled, industries_section_title, industries_section_subtitle,
            industries_section_bg_color, industries_section_title_color, industries_section_subtitle_color,
            industries_section_card_overlay_color, industries_section_card_title_color, industries_section_card_desc_color,
            industries_section_card_btn_bg_color, industries_section_card_btn_text_color,
            testimonials_section_theme, testimonials_section_heading, testimonials_section_subheading,
            faqs_section_theme, faqs_section_heading, faqs_section_subheading
        ) VALUES (
            :id, :solution_id,
            :hero_badge, :hero_title_text, :hero_title_color, :hero_subtitle_color,
            :hero_media_url, :hero_media_type, :hero_bg_color, :hero_bg_gradient_color,
            :hero_bg_gradient_opacity, :hero_bg_pattern_opacity,
            :hero_cta_primary_text, :hero_cta_primary_link,
            :hero_cta_secondary_text, :hero_cta_secondary_link,
            :hero_primary_btn_bg_color, :hero_primary_btn_text_color, :hero_primary_btn_text_hover_color, :hero_primary_btn_border_color,
            :hero_secondary_btn_bg_color, :hero_secondary_btn_text_color, :hero_secondary_btn_text_hover_color, :hero_secondary_btn_border_color,
            :key_benefits_section_enabled, :key_benefits_section_bg_color,
            :key_benefits_section_heading1, :key_benefits_section_heading2, :key_benefits_section_subheading,
            :key_benefits_section_heading_color, :key_benefits_section_subheading_color,
            :key_benefits_section_card_bg_color, :key_benefits_section_card_border_color,
            :key_benefits_section_card_hover_bg_color, :key_benefits_section_card_text_color, :key_benefits_section_card_icon_color,
            :feature_showcase_section_enabled, :feature_showcase_section_title, :feature_showcase_section_subtitle,
            :feature_showcase_section_bg_color, :feature_showcase_section_title_color, :feature_showcase_section_subtitle_color,
            :feature_showcase_card_bg_color, :feature_showcase_card_border_color,
            :feature_showcase_card_badge_bg_color, :feature_showcase_card_badge_text_color,
            :feature_showcase_card_heading_color, :feature_showcase_card_text_color, :feature_showcase_card_icon_color,
            :cta_banner_enabled, :cta_banner_image_url, :cta_banner_overlay_color, :cta_banner_overlay_intensity,
            :cta_banner_heading1, :cta_banner_heading2, :cta_banner_heading_color,
            :cta_banner_button_text, :cta_banner_button_link, :cta_banner_button_bg_color, :cta_banner_button_text_color,
            :industries_section_enabled, :industries_section_title, :industries_section_subtitle,
            :industries_section_bg_color, :industries_section_title_color, :industries_section_subtitle_color,
            :industries_section_card_overlay_color, :industries_section_card_title_color, :industries_section_card_desc_color,
            :industries_section_card_btn_bg_color, :industries_section_card_btn_text_color,
            :testimonials_section_theme, :testimonials_section_heading, :testimonials_section_subheading,
            :faqs_section_theme, :faqs_section_heading, :faqs_section_subheading
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $this->generateUuid(),
            ':solution_id' => $solutionId,
            ':hero_badge' => $data['hero_badge'] ?? null,
            ':hero_title_text' => isset($data['hero_title_text']) ? substr($data['hero_title_text'], 0, 24) : null,
            ':hero_title_color' => $data['hero_title_color'] ?? '#FFFFFF',
            ':hero_subtitle_color' => $data['hero_subtitle_color'] ?? '#FFFFFF',
            ':hero_media_url' => $data['hero_media_url'] ?? null,
            ':hero_media_type' => $data['hero_media_type'] ?? 'image',
            ':hero_bg_color' => $data['hero_bg_color'] ?? '#0a1628',
            ':hero_bg_gradient_color' => $data['hero_bg_gradient_color'] ?? null,
            ':hero_bg_gradient_opacity' => $data['hero_bg_gradient_opacity'] ?? 0.60,
            ':hero_bg_pattern_opacity' => $data['hero_bg_pattern_opacity'] ?? 0.03,
            ':hero_cta_primary_text' => $data['hero_cta_primary_text'] ?? 'Get Started',
            ':hero_cta_primary_link' => $data['hero_cta_primary_link'] ?? null,
            ':hero_cta_secondary_text' => $data['hero_cta_secondary_text'] ?? 'Watch Demo',
            ':hero_cta_secondary_link' => $data['hero_cta_secondary_link'] ?? null,
            ':hero_primary_btn_bg_color' => $data['hero_primary_btn_bg_color'] ?? 'rgba(255,255,255,0.15)',
            ':hero_primary_btn_text_color' => $data['hero_primary_btn_text_color'] ?? '#FFFFFF',
            ':hero_primary_btn_text_hover_color' => $data['hero_primary_btn_text_hover_color'] ?? '#FFFFFF',
            ':hero_primary_btn_border_color' => $data['hero_primary_btn_border_color'] ?? 'rgba(255,255,255,0.3)',
            ':hero_secondary_btn_bg_color' => $data['hero_secondary_btn_bg_color'] ?? 'rgba(255,255,255,0.1)',
            ':hero_secondary_btn_text_color' => $data['hero_secondary_btn_text_color'] ?? '#FFFFFF',
            ':hero_secondary_btn_text_hover_color' => $data['hero_secondary_btn_text_hover_color'] ?? '#FFFFFF',
            ':hero_secondary_btn_border_color' => $data['hero_secondary_btn_border_color'] ?? 'rgba(255,255,255,0.2)',
            ':key_benefits_section_enabled' => $data['key_benefits_section_enabled'] ?? false,
            ':key_benefits_section_bg_color' => $data['key_benefits_section_bg_color'] ?? '#0a1628',
            ':key_benefits_section_heading1' => $data['key_benefits_section_heading1'] ?? '',
            ':key_benefits_section_heading2' => $data['key_benefits_section_heading2'] ?? '',
            ':key_benefits_section_subheading' => $data['key_benefits_section_subheading'] ?? null,
            ':key_benefits_section_heading_color' => $data['key_benefits_section_heading_color'] ?? '#FFFFFF',
            ':key_benefits_section_subheading_color' => $data['key_benefits_section_subheading_color'] ?? '#ffffffb3',
            ':key_benefits_section_card_bg_color' => $data['key_benefits_section_card_bg_color'] ?? '#ffffff14',
            ':key_benefits_section_card_border_color' => $data['key_benefits_section_card_border_color'] ?? '#ffffff1a',
            ':key_benefits_section_card_hover_bg_color' => $data['key_benefits_section_card_hover_bg_color'] ?? '#2563eb',
            ':key_benefits_section_card_text_color' => $data['key_benefits_section_card_text_color'] ?? '#FFFFFF',
            ':key_benefits_section_card_icon_color' => $data['key_benefits_section_card_icon_color'] ?? '#ffffff99',
            ':feature_showcase_section_enabled' => $data['feature_showcase_section_enabled'] ?? false,
            ':feature_showcase_section_title' => $data['feature_showcase_section_title'] ?? 'One solution. All business sizes.',
            ':feature_showcase_section_subtitle' => $data['feature_showcase_section_subtitle'] ?? null,
            ':feature_showcase_section_bg_color' => $data['feature_showcase_section_bg_color'] ?? '#ffffff',
            ':feature_showcase_section_title_color' => $data['feature_showcase_section_title_color'] ?? '#1a202c',
            ':feature_showcase_section_subtitle_color' => $data['feature_showcase_section_subtitle_color'] ?? '#718096',
            ':feature_showcase_card_bg_color' => $data['feature_showcase_card_bg_color'] ?? '#ffffff',
            ':feature_showcase_card_border_color' => $data['feature_showcase_card_border_color'] ?? '#e2e8f0',
            ':feature_showcase_card_badge_bg_color' => $data['feature_showcase_card_badge_bg_color'] ?? '#ebf8ff',
            ':feature_showcase_card_badge_text_color' => $data['feature_showcase_card_badge_text_color'] ?? '#2b6cb0',
            ':feature_showcase_card_heading_color' => $data['feature_showcase_card_heading_color'] ?? '#1a202c',
            ':feature_showcase_card_text_color' => $data['feature_showcase_card_text_color'] ?? '#4a5568',
            ':feature_showcase_card_icon_color' => $data['feature_showcase_card_icon_color'] ?? '#38a169',
            ':cta_banner_enabled' => $data['cta_banner_enabled'] ?? true,
            ':cta_banner_image_url' => $data['cta_banner_image_url'] ?? null,
            ':cta_banner_overlay_color' => $data['cta_banner_overlay_color'] ?? 'rgba(0,0,0,0.5)',
            ':cta_banner_overlay_intensity' => $data['cta_banner_overlay_intensity'] ?? 0.50,
            ':cta_banner_heading1' => $data['cta_banner_heading1'] ?? 'Streamline across 30+ modules.',
            ':cta_banner_heading2' => $data['cta_banner_heading2'] ?? 'Transform your business today!',
            ':cta_banner_heading_color' => $data['cta_banner_heading_color'] ?? '#FFFFFF',
            ':cta_banner_button_text' => $data['cta_banner_button_text'] ?? 'Explore ERP Solutions',
            ':cta_banner_button_link' => $data['cta_banner_button_link'] ?? '#contact-form',
            ':cta_banner_button_bg_color' => $data['cta_banner_button_bg_color'] ?? '#FFFFFF',
            ':cta_banner_button_text_color' => $data['cta_banner_button_text_color'] ?? '#2563eb',
            // Industries Section
            ':industries_section_enabled' => $data['industries_section_enabled'] ?? true,
            ':industries_section_title' => $data['industries_section_title'] ?? 'Industries We Serve',
            ':industries_section_subtitle' => $data['industries_section_subtitle'] ?? 'Trusted by leading organizations across diverse sectors',
            ':industries_section_bg_color' => $data['industries_section_bg_color'] ?? '#f8fafc',
            ':industries_section_title_color' => $data['industries_section_title_color'] ?? '#1a202c',
            ':industries_section_subtitle_color' => $data['industries_section_subtitle_color'] ?? '#718096',
            ':industries_section_card_overlay_color' => $data['industries_section_card_overlay_color'] ?? 'rgba(0,0,0,0.4)',
            ':industries_section_card_title_color' => $data['industries_section_card_title_color'] ?? '#FFFFFF',
            ':industries_section_card_desc_color' => $data['industries_section_card_desc_color'] ?? 'rgba(255,255,255,0.9)',
            ':industries_section_card_btn_bg_color' => $data['industries_section_card_btn_bg_color'] ?? 'rgba(255,255,255,0.2)',
            ':industries_section_card_btn_text_color' => $data['industries_section_card_btn_text_color'] ?? '#FFFFFF',
            // Testimonials Section
            ':testimonials_section_theme' => $data['testimonials_section_theme'] ?? 'light',
            ':testimonials_section_heading' => isset($data['testimonials_section_heading']) ? substr($data['testimonials_section_heading'], 0, 48) : 'What Our Customers Say',
            ':testimonials_section_subheading' => isset($data['testimonials_section_subheading']) ? substr($data['testimonials_section_subheading'], 0, 120) : 'Trusted by leading businesses who have transformed their operations with our solutions',
            // FAQs Section
            ':faqs_section_theme' => $data['faqs_section_theme'] ?? 'light',
            ':faqs_section_heading' => isset($data['faqs_section_heading']) ? substr($data['faqs_section_heading'], 0, 48) : 'Frequently Asked Questions',
            ':faqs_section_subheading' => isset($data['faqs_section_subheading']) ? substr($data['faqs_section_subheading'], 0, 120) : 'Everything you need to know about our solution. Can\'t find what you\'re looking for? Feel free to contact us.'
        ]);
    }

    private function insertContent(string $solutionId, array $data): void
    {
        $sql = "INSERT INTO solution_content (
            id, solution_id, features, screenshots, faqs, key_benefits_cards, feature_showcase_cards, industries_cards
        ) VALUES (
            :id, :solution_id, :features, :screenshots, :faqs, :key_benefits_cards, :feature_showcase_cards, :industries_cards
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $this->generateUuid(),
            ':solution_id' => $solutionId,
            ':features' => isset($data['features']) ? json_encode($data['features']) : null,
            ':screenshots' => isset($data['screenshots']) ? json_encode($data['screenshots']) : null,
            ':faqs' => isset($data['faqs']) ? json_encode($data['faqs']) : null,
            ':key_benefits_cards' => isset($data['key_benefits_cards']) ? json_encode($data['key_benefits_cards']) : null,
            ':feature_showcase_cards' => isset($data['feature_showcase_cards']) ? json_encode($data['feature_showcase_cards']) : null,
            ':industries_cards' => isset($data['industries_cards']) ? json_encode($data['industries_cards']) : null
        ]);
    }


    /**
     * Find solution by ID with styling and content
     */
    public function findById(string $id)
    {
        try {
            $sql = "SELECT s.*, 
                    st.hero_badge, st.hero_title_text, st.hero_title_color, st.hero_subtitle_color,
                    st.hero_media_url, st.hero_media_type, st.hero_bg_color, st.hero_bg_gradient_color,
                    st.hero_bg_gradient_opacity, st.hero_bg_pattern_opacity,
                    st.hero_cta_primary_text, st.hero_cta_primary_link,
                    st.hero_cta_secondary_text, st.hero_cta_secondary_link,
                    st.hero_primary_btn_bg_color, st.hero_primary_btn_text_color, st.hero_primary_btn_text_hover_color, st.hero_primary_btn_border_color,
                    st.hero_secondary_btn_bg_color, st.hero_secondary_btn_text_color, st.hero_secondary_btn_text_hover_color, st.hero_secondary_btn_border_color,
                    st.key_benefits_section_enabled, st.key_benefits_section_bg_color,
                    st.key_benefits_section_heading1, st.key_benefits_section_heading2, st.key_benefits_section_subheading,
                    st.key_benefits_section_heading_color, st.key_benefits_section_subheading_color,
                    st.key_benefits_section_card_bg_color, st.key_benefits_section_card_border_color,
                    st.key_benefits_section_card_hover_bg_color, st.key_benefits_section_card_text_color, st.key_benefits_section_card_icon_color,
                    st.feature_showcase_section_enabled, st.feature_showcase_section_title, st.feature_showcase_section_subtitle,
                    st.feature_showcase_section_bg_color, st.feature_showcase_section_title_color, st.feature_showcase_section_subtitle_color,
                    st.feature_showcase_card_bg_color, st.feature_showcase_card_border_color,
                    st.feature_showcase_card_badge_bg_color, st.feature_showcase_card_badge_text_color,
                    st.feature_showcase_card_heading_color, st.feature_showcase_card_text_color, st.feature_showcase_card_icon_color,
                    st.cta_banner_enabled, st.cta_banner_image_url, st.cta_banner_overlay_color, st.cta_banner_overlay_intensity,
                    st.cta_banner_heading1, st.cta_banner_heading2, st.cta_banner_heading_color,
                    st.cta_banner_button_text, st.cta_banner_button_link, st.cta_banner_button_bg_color, st.cta_banner_button_text_color,
                    st.industries_section_enabled, st.industries_section_title, st.industries_section_subtitle,
                    st.industries_section_bg_color, st.industries_section_title_color, st.industries_section_subtitle_color,
                    st.industries_section_card_overlay_color, st.industries_section_card_title_color, st.industries_section_card_desc_color,
                    st.industries_section_card_btn_bg_color, st.industries_section_card_btn_text_color,
                    st.testimonials_section_theme, st.testimonials_section_heading, st.testimonials_section_subheading,
                    st.faqs_section_theme, st.faqs_section_heading, st.faqs_section_subheading,
                    sc.features, sc.screenshots, sc.faqs, sc.key_benefits_cards, sc.feature_showcase_cards, sc.industries_cards
                FROM solutions s
                LEFT JOIN solution_styling st ON s.id = st.solution_id
                LEFT JOIN solution_content sc ON s.id = sc.solution_id
                WHERE s.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $result = $this->decodeJsonFields($result);
            }
            
            return $result ?: false;
        } catch (PDOException $e) {
            error_log('Solution find by ID failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Find solution by slug with styling and content
     */
    public function findBySlug(string $slug)
    {
        try {
            $sql = "SELECT s.*, 
                    st.hero_badge, st.hero_title_text, st.hero_title_color, st.hero_subtitle_color,
                    st.hero_media_url, st.hero_media_type, st.hero_bg_color, st.hero_bg_gradient_color,
                    st.hero_bg_gradient_opacity, st.hero_bg_pattern_opacity,
                    st.hero_cta_primary_text, st.hero_cta_primary_link,
                    st.hero_cta_secondary_text, st.hero_cta_secondary_link,
                    st.hero_primary_btn_bg_color, st.hero_primary_btn_text_color, st.hero_primary_btn_text_hover_color, st.hero_primary_btn_border_color,
                    st.hero_secondary_btn_bg_color, st.hero_secondary_btn_text_color, st.hero_secondary_btn_text_hover_color, st.hero_secondary_btn_border_color,
                    st.key_benefits_section_enabled, st.key_benefits_section_bg_color,
                    st.key_benefits_section_heading1, st.key_benefits_section_heading2, st.key_benefits_section_subheading,
                    st.key_benefits_section_heading_color, st.key_benefits_section_subheading_color,
                    st.key_benefits_section_card_bg_color, st.key_benefits_section_card_border_color,
                    st.key_benefits_section_card_hover_bg_color, st.key_benefits_section_card_text_color, st.key_benefits_section_card_icon_color,
                    st.feature_showcase_section_enabled, st.feature_showcase_section_title, st.feature_showcase_section_subtitle,
                    st.feature_showcase_section_bg_color, st.feature_showcase_section_title_color, st.feature_showcase_section_subtitle_color,
                    st.feature_showcase_card_bg_color, st.feature_showcase_card_border_color,
                    st.feature_showcase_card_badge_bg_color, st.feature_showcase_card_badge_text_color,
                    st.feature_showcase_card_heading_color, st.feature_showcase_card_text_color, st.feature_showcase_card_icon_color,
                    st.cta_banner_enabled, st.cta_banner_image_url, st.cta_banner_overlay_color, st.cta_banner_overlay_intensity,
                    st.cta_banner_heading1, st.cta_banner_heading2, st.cta_banner_heading_color,
                    st.cta_banner_button_text, st.cta_banner_button_link, st.cta_banner_button_bg_color, st.cta_banner_button_text_color,
                    st.industries_section_enabled, st.industries_section_title, st.industries_section_subtitle,
                    st.industries_section_bg_color, st.industries_section_title_color, st.industries_section_subtitle_color,
                    st.industries_section_card_overlay_color, st.industries_section_card_title_color, st.industries_section_card_desc_color,
                    st.industries_section_card_btn_bg_color, st.industries_section_card_btn_text_color,
                    st.testimonials_section_theme, st.testimonials_section_heading, st.testimonials_section_subheading,
                    st.faqs_section_theme, st.faqs_section_heading, st.faqs_section_subheading,
                    sc.features, sc.screenshots, sc.faqs, sc.key_benefits_cards, sc.feature_showcase_cards, sc.industries_cards
                FROM solutions s
                LEFT JOIN solution_styling st ON s.id = st.solution_id
                LEFT JOIN solution_content sc ON s.id = sc.solution_id
                WHERE s.slug = :slug";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':slug' => $slug]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $result = $this->decodeJsonFields($result);
            }
            
            return $result ?: false;
        } catch (PDOException $e) {
            error_log('Solution find by slug failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update solution data across all three tables
     */
    public function update(string $id, array $data): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Separate data by table
            $coreData = [];
            $stylingData = [];
            $contentData = [];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $this->coreFields)) {
                    $coreData[$key] = $value;
                } elseif (in_array($key, $this->stylingFields)) {
                    $stylingData[$key] = $value;
                } elseif (in_array($key, $this->contentFields)) {
                    $contentData[$key] = $value;
                }
            }
            
            // Update solutions table
            if (!empty($coreData)) {
                $this->updateTable('solutions', 'id', $id, $coreData);
            }
            
            // Update solution_styling table
            if (!empty($stylingData)) {
                $this->updateTable('solution_styling', 'solution_id', $id, $stylingData);
            }
            
            // Update solution_content table
            if (!empty($contentData)) {
                $this->updateContentTable($id, $contentData);
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('Solution update failed: ' . $e->getMessage());
            return false;
        }
    }

    private function updateTable(string $table, string $idColumn, string $id, array $data): void
    {
        $nullableFields = [
            'description', 'tagline', 'subtitle', 'hero_badge', 'hero_title_text',
            'icon_image', 'hero_media_url', 'video_url', 'demo_video_url',
            'testimonial_id', 'hero_cta_primary_link', 'hero_cta_secondary_link',
            'pricing_note', 'meta_title', 'meta_description', 'meta_keywords',
            'hero_bg_gradient_color', 'key_benefits_section_heading1', 'key_benefits_section_heading2',
            'key_benefits_section_subheading', 'feature_showcase_section_title', 'feature_showcase_section_subtitle',
            'cta_banner_image_url', 'cta_banner_heading1', 'cta_banner_heading2', 'cta_banner_button_text', 'cta_banner_button_link',
            'testimonials_section_heading', 'testimonials_section_subheading'
        ];
        
        $updateFields = [];
        $params = [":$idColumn" => $id];
        
        foreach ($data as $key => $value) {
            $updateFields[] = "$key = :$key";
            if (in_array($key, $nullableFields) && $value === '') {
                $params[":$key"] = null;
            } else {
                $params[":$key"] = $value;
            }
        }
        
        if (empty($updateFields)) {
            return;
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $updateFields) . " WHERE $idColumn = :$idColumn";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    private function updateContentTable(string $solutionId, array $data): void
    {
        $updateFields = [];
        $params = [':solution_id' => $solutionId];
        
        foreach ($data as $key => $value) {
            $updateFields[] = "$key = :$key";
            $params[":$key"] = is_array($value) ? json_encode($value) : $value;
        }
        
        if (empty($updateFields)) {
            return;
        }
        
        $sql = "UPDATE solution_content SET " . implode(', ', $updateFields) . " WHERE solution_id = :solution_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }


    /**
     * Delete solution (cascades to styling and content via FK)
     */
    public function delete(string $id): bool
    {
        try {
            $sql = "DELETE FROM solutions WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('Solution deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all solutions with optional filters
     */
    public function findAll(array $filters = [], int $limit = 100, int $offset = 0): array
    {
        try {
            $sql = "SELECT s.*, 
                    st.hero_badge, st.hero_title_text, st.hero_title_color, st.hero_subtitle_color,
                    st.hero_media_url, st.hero_media_type, st.hero_bg_color, st.hero_bg_gradient_color,
                    st.hero_bg_gradient_opacity, st.hero_bg_pattern_opacity,
                    st.hero_cta_primary_text, st.hero_cta_primary_link,
                    st.hero_cta_secondary_text, st.hero_cta_secondary_link,
                    st.hero_primary_btn_bg_color, st.hero_primary_btn_text_color, st.hero_primary_btn_text_hover_color, st.hero_primary_btn_border_color,
                    st.hero_secondary_btn_bg_color, st.hero_secondary_btn_text_color, st.hero_secondary_btn_text_hover_color, st.hero_secondary_btn_border_color,
                    st.key_benefits_section_enabled, st.key_benefits_section_bg_color,
                    st.key_benefits_section_heading1, st.key_benefits_section_heading2, st.key_benefits_section_subheading,
                    st.key_benefits_section_heading_color, st.key_benefits_section_subheading_color,
                    st.key_benefits_section_card_bg_color, st.key_benefits_section_card_border_color,
                    st.key_benefits_section_card_hover_bg_color, st.key_benefits_section_card_text_color, st.key_benefits_section_card_icon_color,
                    st.feature_showcase_section_enabled, st.feature_showcase_section_title, st.feature_showcase_section_subtitle,
                    st.feature_showcase_section_bg_color, st.feature_showcase_section_title_color, st.feature_showcase_section_subtitle_color,
                    st.feature_showcase_card_bg_color, st.feature_showcase_card_border_color,
                    st.feature_showcase_card_badge_bg_color, st.feature_showcase_card_badge_text_color,
                    st.feature_showcase_card_heading_color, st.feature_showcase_card_text_color, st.feature_showcase_card_icon_color,
                    st.cta_banner_enabled, st.cta_banner_image_url, st.cta_banner_overlay_color, st.cta_banner_overlay_intensity,
                    st.cta_banner_heading1, st.cta_banner_heading2, st.cta_banner_heading_color,
                    st.cta_banner_button_text, st.cta_banner_button_link, st.cta_banner_button_bg_color, st.cta_banner_button_text_color,
                    st.industries_section_enabled, st.industries_section_title, st.industries_section_subtitle,
                    st.industries_section_bg_color, st.industries_section_title_color, st.industries_section_subtitle_color,
                    st.industries_section_card_overlay_color, st.industries_section_card_title_color, st.industries_section_card_desc_color,
                    st.industries_section_card_btn_bg_color, st.industries_section_card_btn_text_color,
                    st.testimonials_section_theme, st.testimonials_section_heading, st.testimonials_section_subheading,
                    sc.features, sc.screenshots, sc.faqs, sc.key_benefits_cards, sc.feature_showcase_cards, sc.industries_cards
                FROM solutions s
                LEFT JOIN solution_styling st ON s.id = st.solution_id
                LEFT JOIN solution_content sc ON s.id = sc.solution_id
                WHERE 1=1";
            $params = [];

            if (isset($filters['status'])) {
                $sql .= " AND s.status = :status";
                $params[':status'] = $filters['status'];
            }

            $sql .= " ORDER BY s.display_order ASC, s.created_at DESC LIMIT :limit OFFSET :offset";

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
            error_log('Solution findAll failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get featured solutions for homepage
     */
    public function getFeaturedSolutions(int $limit = 6): array
    {
        try {
            $sql = "SELECT s.*, 
                    st.hero_badge, st.hero_media_url, st.hero_media_type,
                    sc.features
                FROM solutions s
                LEFT JOIN solution_styling st ON s.id = st.solution_id
                LEFT JOIN solution_content sc ON s.id = sc.solution_id
                WHERE s.status = 'PUBLISHED' AND s.featured_on_homepage = TRUE 
                ORDER BY s.display_order ASC, s.created_at DESC 
                LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as &$result) {
                $result = $this->decodeJsonFields($result);
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log('Featured solutions fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if slug exists
     */
    public function slugExists(string $slug, ?string $excludeId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM solutions WHERE slug = :slug";
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

    private function decodeJsonFields(array $data): array
    {
        $jsonFields = [
            'features', 'screenshots', 'faqs', 'key_benefits_cards', 'feature_showcase_cards', 'industries_cards'
        ];
        
        foreach ($jsonFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = json_decode($data[$field], true);
            }
        }
        
        return $data;
    }

    /**
     * Get linked features for a solution
     */
    public function getLinkedFeatures(string $solutionId): array
    {
        try {
            $sql = "SELECT f.*, sf.display_order as link_order, sf.is_highlighted
                    FROM features f
                    INNER JOIN solution_features sf ON f.id = sf.feature_id
                    WHERE sf.solution_id = :solution_id AND f.status = 'PUBLISHED'
                    ORDER BY sf.display_order ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':solution_id' => $solutionId]);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as &$result) {
                $jsonFields = ['benefits', 'related_solutions', 'screenshots'];
                foreach ($jsonFields as $field) {
                    if (isset($result[$field]) && is_string($result[$field])) {
                        $result[$field] = json_decode($result[$field], true);
                    }
                }
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log('Get linked features failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get related solutions
     */
    public function getRelatedSolutions(string $solutionId, int $limit = 3): array
    {
        try {
            $sql = "SELECT DISTINCT s.*, COUNT(sf2.feature_id) as shared_features
                    FROM solutions s
                    INNER JOIN solution_features sf2 ON s.id = sf2.solution_id
                    WHERE sf2.feature_id IN (
                        SELECT feature_id FROM solution_features WHERE solution_id = :solution_id
                    )
                    AND s.id != :solution_id2
                    AND s.status = 'PUBLISHED'
                    GROUP BY s.id
                    ORDER BY shared_features DESC, s.display_order ASC
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':solution_id', $solutionId);
            $stmt->bindValue(':solution_id2', $solutionId);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as &$result) {
                $result = $this->decodeJsonFields($result);
            }
            
            return $results;
        } catch (PDOException $e) {
            error_log('Get related solutions failed: ' . $e->getMessage());
            return [];
        }
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
