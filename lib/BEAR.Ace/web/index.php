<?php
/**
 * This file is part of the BEAR.Ace package
 *
 * @package BEAR.Ace
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 * in console
 * $ php -S localhost:8070 index.php
 *
 * web access
 * http://localhost:8070?file=hello.php
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use BEAR\Ace\Editor;
use BEAR\Ace\Exception;

// config
$rootPath = __DIR__;

try {
    $editor = new Editor;
    $html = (string)$editor->setRootPath($rootPath)->handle($_GET, $_POST, $_SERVER);
    //file_put_contents("file.html", $html);
    $add_Style = "        .ace_editor {position: relative;overflow: hidden;font: 15px/normal 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;direction: ltr;text-align: left;}
                            </style>
                 ";
    $html = str_replace("</style>", $add_Style, $html);
    echo $html;
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo $e->getCode();
}
