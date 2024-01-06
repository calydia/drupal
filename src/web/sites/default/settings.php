<?php
$settings['migrate_node_migrate_type_classic'] = FALSE;

$databases = [];
$settings['hash_salt'] = getenv('SITE_HASH_SALT');
$settings['update_free_access'] = FALSE;
$settings['file_private_path'] = '../private';
$settings['container_yamls'][] = $app_root . '/' . $site_path . '/services.yml';
$settings['trusted_host_patterns'] = [
    '^'.addcslashes(getenv('SITE_URL') ?: getenv('DDEV_SITENAME') . '.ddev.site', ".").'$',
    'localhost'
];
$settings['file_scan_ignore_directories'] = [
    'node_modules',
    'bower_components',
];
$settings['entity_update_batch_size'] = 50;
$settings['entity_update_backup'] = TRUE;
$settings['config_sync_directory'] = '../config/sync';
$settings['skip_permissions_hardening'] = filter_var(@getenv('SKIP_PERMISSIONS_HARDENING'), FILTER_VALIDATE_BOOLEAN) ?: FALSE;
$databases['default']['default'] = array (
    'database' => getenv('DB_NAME') ?: 'db',
    'username' => getenv('DB_USER') ?: 'db',
    'password' => getenv('DB_PASSWORD') ?: 'db',
    'prefix' => '',
    'host' => getenv('DB_HOST') ?: 'db',
    'port' => '3306',
    'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
    'driver' => 'mysql',
);

if (getenv('SITE_URL') !== null && !empty(getenv('SITE_URL'))) {
  $settings['reverse_proxy'] = TRUE;
  $settings['reverse_proxy_addresses'] = ['0.0.0.0'];
  $settings['reverse_proxy_trusted_headers'] = \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO;
}

// Automatically generated include for settings managed by ddev.
$ddev_settings = dirname(__FILE__) . '/settings.ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
  require $ddev_settings;
}
