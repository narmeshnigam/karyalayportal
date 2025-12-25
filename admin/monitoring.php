<?php
/**
 * Admin Monitoring Dashboard
 * Displays system health, logs, and performance metrics
 */

require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/admin_helpers.php';
require_once __DIR__ . '/../includes/template_helpers.php';

// Start session and check admin authentication
startSecureSession();
require_admin();
require_permission('settings.general');

$page_title = 'System Monitoring';

// Get log files
$logDir = __DIR__ . '/../storage/logs';
$logFiles = [];
if (is_dir($logDir)) {
    $files = scandir($logDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && $file !== '.gitignore') {
            $logFiles[] = [
                'name' => $file,
                'size' => filesize($logDir . '/' . $file),
                'modified' => filemtime($logDir . '/' . $file),
            ];
        }
    }
    usort($logFiles, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}

// Get recent errors from today's error log
$recentErrors = [];
$errorLogFile = $logDir . '/errors-' . date('Y-m-d') . '.log';
if (file_exists($errorLogFile)) {
    $lines = file($errorLogFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recentErrors = array_slice(array_reverse($lines), 0, 10);
    $recentErrors = array_map(function($line) {
        return json_decode($line, true);
    }, $recentErrors);
}

// Get performance metrics from today
$perfMetrics = [];
$perfLogFile = $logDir . '/performance-' . date('Y-m-d') . '.log';
if (file_exists($perfLogFile)) {
    $lines = file($perfLogFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recentMetrics = array_slice(array_reverse($lines), 0, 5);
    foreach ($recentMetrics as $line) {
        $data = json_decode($line, true);
        if ($data) {
            $perfMetrics[] = $data;
        }
    }
}

// System information
$systemInfo = [
    'php_version' => PHP_VERSION,
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'environment' => getenv('APP_ENV') ?: 'development',
];

// Handle actions
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'download' && isset($_GET['file'])) {
        $filename = basename($_GET['file']);
        $filepath = $logDir . '/' . $filename;
        
        if (file_exists($filepath)) {
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        }
    } elseif ($_GET['action'] === 'clear_logs') {
        $cutoffTime = time() - (30 * 24 * 60 * 60);
        foreach ($logFiles as $file) {
            if ($file['modified'] < $cutoffTime) {
                unlink($logDir . '/' . $file['name']);
            }
        }
        header('Location: ' . get_app_base_url() . '/admin/monitoring.php?cleared=1');
        exit;
    }
}

include __DIR__ . '/../templates/admin-header.php';
?>

<div class="admin-page-header">
    <div class="admin-page-header-content">
        <h1 class="admin-page-title">System Monitoring</h1>
        <p class="admin-page-description">Monitor system health, view logs, and track performance metrics</p>
    </div>
    <div class="admin-page-header-actions">
        <a href="<?php echo get_app_base_url(); ?>/admin/monitoring.php?action=clear_logs" 
           class="btn btn-secondary"
           onclick="return confirm('Are you sure you want to clear logs older than 30 days?')">
            Clear Old Logs
        </a>
        <a href="<?php echo get_app_base_url(); ?>/admin/dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>

<?php if (isset($_GET['cleared'])): ?>
    <div class="alert alert-success">
        <svg class="alert-icon" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Old log files have been cleared successfully.</span>
    </div>
<?php endif; ?>

<!-- System Health Overview -->
<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">System Overview</h2>
    </div>
    <div class="card-body">
        <div class="stats-grid">
            <!-- System Health -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-success">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">System Status</div>
                    <div class="stat-value text-success">Operational</div>
                </div>
            </div>

            <!-- Environment -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-info">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Environment</div>
                    <div class="stat-value"><?php echo htmlspecialchars(ucfirst($systemInfo['environment'])); ?></div>
                </div>
            </div>

            <!-- Errors Today -->
            <div class="stat-card">
                <div class="stat-icon <?php echo count($recentErrors) > 0 ? 'stat-icon-warning' : 'stat-icon-success'; ?>">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">Errors Today</div>
                    <div class="stat-value"><?php echo count($recentErrors); ?></div>
                </div>
            </div>

            <!-- PHP Version -->
            <div class="stat-card">
                <div class="stat-icon stat-icon-primary">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-label">PHP Version</div>
                    <div class="stat-value"><?php echo htmlspecialchars($systemInfo['php_version']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">System Information</h2>
    </div>
    <div class="card-body">
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Memory Limit</span>
                <span class="info-value"><?php echo htmlspecialchars($systemInfo['memory_limit']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Max Execution Time</span>
                <span class="info-value"><?php echo htmlspecialchars($systemInfo['max_execution_time']); ?>s</span>
            </div>
            <div class="info-item">
                <span class="info-label">Upload Max Filesize</span>
                <span class="info-value"><?php echo htmlspecialchars($systemInfo['upload_max_filesize']); ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Post Max Size</span>
                <span class="info-value"><?php echo htmlspecialchars($systemInfo['post_max_size']); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Errors -->
<?php if (!empty($recentErrors)): ?>
<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Recent Errors</h2>
    </div>
    <div class="card-body">
        <div class="log-entries">
            <?php foreach ($recentErrors as $error): ?>
                <?php if ($error): ?>
                <div class="log-entry log-entry-<?php echo htmlspecialchars($error['level'] ?? 'error'); ?>">
                    <div class="log-header">
                        <span class="log-level"><?php echo htmlspecialchars(strtoupper($error['level'] ?? 'ERROR')); ?></span>
                        <span class="log-time"><?php echo htmlspecialchars($error['timestamp'] ?? ''); ?></span>
                    </div>
                    <div class="log-message"><?php echo htmlspecialchars($error['message'] ?? 'No message'); ?></div>
                    <?php if (!empty($error['context'])): ?>
                    <details class="log-context">
                        <summary>View Context</summary>
                        <pre><?php echo htmlspecialchars(json_encode($error['context'], JSON_PRETTY_PRINT)); ?></pre>
                    </details>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Performance Metrics -->
<?php if (!empty($perfMetrics)): ?>
<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Recent Performance Metrics</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Request Duration</th>
                        <th>Memory Peak</th>
                        <th>Metrics Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($perfMetrics as $metric): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($metric['timestamp'] ?? ''); ?></td>
                        <td><?php echo number_format($metric['summary']['request_duration_ms'] ?? 0, 2); ?> ms</td>
                        <td><?php echo htmlspecialchars($metric['summary']['memory_peak_mb'] ?? 0); ?> MB</td>
                        <td><?php echo htmlspecialchars($metric['summary']['metrics_count'] ?? 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Log Files -->
<div class="admin-card">
    <div class="card-header">
        <h2 class="card-title">Log Files</h2>
    </div>
    <div class="card-body">
        <?php if (empty($logFiles)): ?>
            <div class="empty-state">
                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>No log files found</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Last Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logFiles as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['name']); ?></td>
                            <td><?php echo number_format($file['size'] / 1024, 2); ?> KB</td>
                            <td><?php echo date('Y-m-d H:i:s', $file['modified']); ?></td>
                            <td>
                                <a href="<?php echo get_app_base_url(); ?>/admin/monitoring.php?action=download&file=<?php echo urlencode($file['name']); ?>" 
                                   class="btn btn-sm btn-secondary">
                                    Download
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Page Header */
.admin-page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    gap: 1rem;
}

.admin-page-header-content {
    flex: 1;
}

.admin-page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-gray-900);
    margin: 0 0 0.5rem 0;
}

.admin-page-description {
    font-size: 0.875rem;
    color: var(--color-gray-600);
    margin: 0;
}

.admin-page-header-actions {
    display: flex;
    gap: 0.75rem;
}

/* Card Styles */
.admin-card {
    background: white;
    border: 1px solid var(--color-gray-200);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.card-header {
    padding: 1rem 1.5rem;
    background: var(--color-gray-50);
    border-bottom: 1px solid var(--color-gray-200);
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--color-gray-900);
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--color-gray-50);
    border-radius: 8px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon-success {
    background: #ecfdf5;
    color: #10b981;
}

.stat-icon-warning {
    background: #fffbeb;
    color: #f59e0b;
}

.stat-icon-info {
    background: #eff6ff;
    color: #3b82f6;
}

.stat-icon-primary {
    background: #eef2ff;
    color: #6366f1;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--color-gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--color-gray-900);
}

.text-success {
    color: #10b981;
}

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: var(--color-gray-50);
    border-radius: 6px;
}

.info-label {
    font-size: 0.875rem;
    color: var(--color-gray-600);
}

.info-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--color-gray-900);
}

/* Log Entries */
.log-entries {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.log-entry {
    border-left: 4px solid var(--color-gray-300);
    padding: 1rem;
    background: var(--color-gray-50);
    border-radius: 0 6px 6px 0;
}

.log-entry-error {
    border-left-color: #ef4444;
    background: #fef2f2;
}

.log-entry-warning {
    border-left-color: #f59e0b;
    background: #fffbeb;
}

.log-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.log-level {
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.125rem 0.5rem;
    border-radius: 4px;
    background: var(--color-gray-200);
}

.log-entry-error .log-level {
    background: #fecaca;
    color: #991b1b;
}

.log-entry-warning .log-level {
    background: #fde68a;
    color: #92400e;
}

.log-time {
    font-size: 0.75rem;
    color: var(--color-gray-500);
}

.log-message {
    font-size: 0.875rem;
    color: var(--color-gray-700);
}

.log-context {
    margin-top: 0.75rem;
    font-size: 0.75rem;
}

.log-context summary {
    cursor: pointer;
    color: var(--color-primary);
    font-weight: 500;
}

.log-context pre {
    background: white;
    padding: 0.75rem;
    border-radius: 4px;
    overflow-x: auto;
    margin-top: 0.5rem;
    font-size: 0.75rem;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table th,
.admin-table td {
    padding: 0.75rem 1rem;
    text-align: left;
    border-bottom: 1px solid var(--color-gray-200);
}

.admin-table th {
    background: var(--color-gray-50);
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--color-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.admin-table td {
    font-size: 0.875rem;
    color: var(--color-gray-700);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--color-gray-500);
}

.empty-state svg {
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
}

/* Alerts */
.alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
}

.alert-icon {
    flex-shrink: 0;
}

.alert-success {
    background-color: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    border: 1px solid transparent;
}

.btn-secondary {
    background-color: white;
    color: var(--color-gray-700);
    border-color: var(--color-gray-300);
}

.btn-secondary:hover {
    background-color: var(--color-gray-50);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-page-header {
        flex-direction: column;
    }
    
    .admin-page-header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>

<?php include __DIR__ . '/../templates/admin-footer.php'; ?>
