-- Migration: Create Business Hub Tables
-- Creates tables for managing the dynamic business hub section

-- Business Hub Categories (max 4)
CREATE TABLE IF NOT EXISTS business_hub_categories (
    id VARCHAR(36) PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    title_line2 VARCHAR(100) DEFAULT NULL COMMENT 'Second line of title for display',
    slug VARCHAR(100) NOT NULL UNIQUE,
    link_url VARCHAR(500) DEFAULT NULL,
    color_class VARCHAR(50) NOT NULL DEFAULT 'people' COMMENT 'CSS color class: people, operations, finance, control',
    position VARCHAR(20) NOT NULL DEFAULT 'top-left' COMMENT 'Position: top-left, top-right, bottom-left, bottom-right',
    display_order INT NOT NULL DEFAULT 0,
    status ENUM('DRAFT', 'PUBLISHED', 'ARCHIVED') NOT NULL DEFAULT 'DRAFT',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Business Hub Nodes (max 6 per category)
CREATE TABLE IF NOT EXISTS business_hub_nodes (
    id VARCHAR(36) PRIMARY KEY,
    category_id VARCHAR(36) NOT NULL,
    title VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    link_url VARCHAR(500) DEFAULT NULL,
    display_order INT NOT NULL DEFAULT 0,
    status ENUM('DRAFT', 'PUBLISHED', 'ARCHIVED') NOT NULL DEFAULT 'DRAFT',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES business_hub_categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category_slug (category_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for performance
CREATE INDEX idx_hub_categories_status ON business_hub_categories(status);
CREATE INDEX idx_hub_categories_order ON business_hub_categories(display_order);
CREATE INDEX idx_hub_nodes_category ON business_hub_nodes(category_id);
CREATE INDEX idx_hub_nodes_status ON business_hub_nodes(status);
CREATE INDEX idx_hub_nodes_order ON business_hub_nodes(display_order);

-- Insert default data matching current static content
INSERT INTO business_hub_categories (id, title, title_line2, slug, color_class, position, display_order, status) VALUES
(UUID(), 'People', 'Management', 'people-management', 'people', 'top-left', 1, 'PUBLISHED'),
(UUID(), 'Operations', '& CRM', 'operations-crm', 'operations', 'top-right', 2, 'PUBLISHED'),
(UUID(), 'Finance &', 'Administration', 'finance-administration', 'finance', 'bottom-left', 3, 'PUBLISHED'),
(UUID(), 'Control &', 'Infrastructure', 'control-infrastructure', 'control', 'bottom-right', 4, 'PUBLISHED');

-- Insert default nodes for People Management
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Employees', 'employees', 1, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'people-management';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Attendance', 'attendance', 2, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'people-management';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Payroll', 'payroll', 3, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'people-management';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Reimbursement', 'reimbursement', 4, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'people-management';

-- Insert default nodes for Operations & CRM
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'CRM', 'crm', 1, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'operations-crm';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Clients', 'clients', 2, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'operations-crm';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Projects', 'projects', 3, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'operations-crm';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Catalog', 'catalog', 4, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'operations-crm';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Quotation', 'quotation', 5, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'operations-crm';

-- Insert default nodes for Finance & Administration
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Expenses', 'expenses', 1, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'finance-administration';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Invoices', 'invoices', 2, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'finance-administration';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Payments', 'payments', 3, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'finance-administration';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Data Transfer', 'datatransfer', 4, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'finance-administration';

-- Insert default nodes for Control & Infrastructure
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Documents', 'documents', 1, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'control-infrastructure';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Notebook', 'notebook', 2, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'control-infrastructure';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Assets', 'assets', 3, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'control-infrastructure';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Visitor Log', 'visitorlog', 4, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'control-infrastructure';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Contacts', 'contacts', 5, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'control-infrastructure';
INSERT INTO business_hub_nodes (id, category_id, title, slug, display_order, status)
SELECT UUID(), id, 'Roles & Permissions', 'roles', 6, 'PUBLISHED' FROM business_hub_categories WHERE slug = 'control-infrastructure';
