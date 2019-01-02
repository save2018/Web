<?php
/**
 * 统一访问入口
 */

require_once dirname(__FILE__) . '/init.php';

$pai = new \PhalApi\PhalApi();


/********入口默认接收json格式数据*******/
$HTTP_RAW_POST_DATA = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents('php://input');
\PhalApi\DI()->request = new PhalApi\Request(@array_merge($_GET,json_decode($HTTP_RAW_POST_DATA, true)));
/***********************************/

$pai->response()->output();

