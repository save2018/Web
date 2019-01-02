<?php
error_reporting(E_ERROR);
require_once 'phpqrcode/phpqrcode.php';
$url = urldecode($_GET["data"]);

$errorCorrectionLevel = "L"; //容错级别
$matrixPointSize = "8";//尺寸大小
$margin='1'; //空白边距


QRcode::png($url,false,$errorCorrectionLevel,$matrixPointSize,$margin);
