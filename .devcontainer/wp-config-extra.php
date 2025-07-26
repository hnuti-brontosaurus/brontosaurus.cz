<?php
// Additional WordPress configuration for development

// Enable debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SCRIPT_DEBUG', true);

// Disable file editing in admin
define('DISALLOW_FILE_EDIT', false);

// Allow direct file access (for development)
define('FS_METHOD', 'direct');

// Memory limit
ini_set('memory_limit', '512M');

// Increase upload limits
ini_set('upload_max_filesize', '64M');
ini_set('post_max_size', '64M');
ini_set('max_execution_time', 300);
ini_set('max_input_vars', 3000);
