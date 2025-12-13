<?php
/**
 * Admin - Delete Client Logo
 */

require_once __DIR__ . '/../../config/bootstrap.php';
require_once __DIR__ . '/../../includes/auth_helpers.php';
require_once __DIR__ . '/../../includes/admin_helpers.php';

use Karyalay\Models\ClientLogo;

startSecureSession();
require_admin();
require_permission('client_logos.manage');

// Check if table exists
try {
    $clientLogoModel = new ClientLogo();
} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Client logos feature not set up. Please run migration 052.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . get_app_base_url() . '/admin/client-logos.php');
    exit;
}

// Get logo ID
$id = $_GET['id'] ?? '';
if (empty($id)) {
    header('Location: ' . get_app_base_url() . '/admin/client-logos.php');
    exit;
}

// Fetch existing logo
$logo = $clientLogoModel->getById($id);
if (!$logo) {
    $_SESSION['flash_message'] = 'Client logo not found.';
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . get_app_base_url() . '/admin/client-logos.php');
    exit;
}

// Delete the logo
$success = $clientLogoModel->delete($id);

if ($success) {
    $_SESSION['flash_message'] = 'Client logo deleted successfully.';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_message'] = 'Failed to delete client logo.';
    $_SESSION['flash_type'] = 'error';
}

header('Location: ' . get_app_base_url() . '/admin/client-logos.php');
exit;
