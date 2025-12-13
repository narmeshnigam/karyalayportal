<?php
/**
 * Admin Business Hub - Delete Category
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';

use Karyalay\Models\BusinessHubCategory;

startSecureSession();
require_admin();
require_permission('hero_slides.manage');

$hubModel = new BusinessHubCategory();

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: ' . get_app_base_url() . '/admin/business-hub.php');
    exit;
}

$category = $hubModel->getById($id);
if (!$category) {
    $_SESSION['admin_error'] = 'Category not found.';
    header('Location: ' . get_app_base_url() . '/admin/business-hub.php');
    exit;
}

if ($hubModel->delete($id)) {
    $_SESSION['admin_success'] = 'Category "' . $category['title'] . '" and all its nodes have been deleted.';
} else {
    $_SESSION['admin_error'] = 'Failed to delete category.';
}

header('Location: ' . get_app_base_url() . '/admin/business-hub.php');
exit;
