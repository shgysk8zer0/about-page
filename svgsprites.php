<?php
namespace SVGSprites;

use \shgysk8zer0\DOM as DOM;
use \shgysk8zer0\Core_API\Abstracts\HTTPStatusCodes as HTTPStatus;

require_once './autoloader.php';
if (in_array(PHP_SAPI, ['cli'])) {
	$icons = json_decode(file_get_contents('images/icons.json'), true);
	$sprites = new DOM\SVGSprite($icons);
	$sprites->save('images/icons.svg');
} else {
	http_response_code(HTTPStatus::FORBIDDEN);
}
