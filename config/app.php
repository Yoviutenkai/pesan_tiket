<?php
define('APP_NAME', 'Eventa');
$docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
$projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$basePath = '';

if ($docRoot && $projectRoot && strpos($projectRoot, $docRoot) === 0) {
	$basePath = substr($projectRoot, strlen($docRoot));
}

if ($basePath === '') {
	$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
	$projectName = basename(__DIR__ . '/..');
	$needle = '/' . $projectName;
	$pos = strpos($scriptName, $needle);
	if ($pos !== false) {
		$basePath = substr($scriptName, 0, $pos + strlen($needle));
	} else {
		$basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
	}
}

$basePath = '/' . trim($basePath, '/');
if ($basePath === '') {
	$basePath = '/';
}

define('BASE_URL', $basePath);
date_default_timezone_set('Asia/Jakarta');
