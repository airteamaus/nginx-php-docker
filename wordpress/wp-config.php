<?php
# Load configuration from a .env file.
require_once '/composer/vendor/autoload.php';
$dotenv = new Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/.env');

# Load configuration from a AWS SecretsManager secret.
$secret_id = getenv('AWS_SECRET_ID');
if ($secret_id) {
	try {
		$sm = new Aws\SecretsManager\SecretsManagerClient();
		$result = $sm->GetSecretValue(['SecretId' => $secret_id]);
		if (isset($result['SecretString'])) {
			$secret = $result['SecretString'];
		} else {
			$secret = base64_decode($result['SecretBinary']);
		}
		$secret = json_decode($secret, true);
		foreach ($secret as $key => $value) {
			putenv($key . '=' . $value);
		}
	} catch (Exception $e) {
		error_log($e);
	}
}

# Database Configuration
define('DB_NAME', getenv('WP_DB_NAME'));
define('DB_USER', getenv('WP_DB_USER'));
define('DB_PASSWORD', getenv('WP_DB_PASSWORD'));
define('DB_HOST', getenv('WP_DB_HOST'));
define('DB_HOST_SLAVE', getenv('WP_DB_HOST_SLAVE'));
define('DB_CHARSET', getenv('WP_DB_CHARSET'));
define('DB_COLLATE', getenv('WP_DB_COLLATE'));

$table_prefix = getenv('WP_DB_TABLE_PREFIX');
if (!$table_prefix) {
	$table_prefix = 'wp_';
}

# Security Salts, Keys, Etc
define('AUTH_KEY', getenv('WP_AUTH_KEY'));
define('SECURE_AUTH_KEY', getenv('WP_SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', getenv('WP_LOGGED_IN_KEY'));
define('NONCE_KEY', getenv('WP_NONCE_KEY'));
define('AUTH_SALT', getenv('WP_AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('WP_SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', getenv('WP_LOGGED_IN_SALT'));
define('NONCE_SALT', getenv('WP_NONCE_SALT'));

# Support being load balanced.
if (
	!strcasecmp(getenv('WP_TRUST_FORWARDED_HEADERS'), 'false') &&
	!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
	!strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https')
) {
	$_SERVER['HTTPS'] = 'on'; // Convince WordPress.
}

# Load wp-settings, this should be at the end of this file.
if (!defined('ABSPATH'))
	define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
