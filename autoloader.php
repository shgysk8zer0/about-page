<?php
namespace Autoloader;

function cli_init($config = './config/env.json')
{
	$vars = json_decode(file_get_contents($config), true);
	forEach($vars as $key => $value) {
		putenv("$key=$value");
	}
}
function assert_callback($script, $line, $code = 0, $message = null)
{
	echo sprintf('Assert failed: [%s:%u] "%s"', $script, $line, $message) . PHP_EOL;
}
// Configure assert options based on server usage (CLI or not)
if (in_array(PHP_SAPI, ['cli', 'cli-server'])) {
	cli_init();
	assert_options(ASSERT_ACTIVE,   true);
	assert_options(ASSERT_BAIL,     true);
	assert_options(ASSERT_WARNING,  false);
	assert_options(ASSERT_CALLBACK, '\\' . __NAMESPACE__ . '\\assert_callback');
} else {
	assert_options(ASSERT_ACTIVE,  false);
	assert_options(ASSERT_BAIL,    false);
	assert_options(ASSERT_WARNING, false);
}
// Do PHP version check
if (version_compare(PHP_VERSION, getenv('MIN_PHP_VERSION'), '<')) {
	if (PHP_SAPI !== 'cli') {
		header('Content-Type: text/plain');
		http_response_code(500);
		exit(sprintf('PHP version %s or greater required.', getenv('MIN_PHP_VERSION')));
	} else {
		throw new \Exception(sprintf('PHP version %s or greater required.', getenv('MIN_PHP_VERSION')));
	}
}

// Configure autoloader
set_include_path(realpath(getenv('AUTOLOAD_DIR')) . PATH_SEPARATOR . getenv('CONFIG_DIR') . DIRECTORY_SEPARATOR . get_include_path());
spl_autoload_register(getenv('AUTOLOAD_FUNC'));
spl_autoload_extensions(getenv('AUTOLOAD_EXTS'));
