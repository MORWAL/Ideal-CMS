<?php
use Ideal\Structure\Service\CheckSiteFiles\Model as CheckSiteFilesModel;

$message = '';

// Ищем корневую папку сайта
$_SERVER['DOCUMENT_ROOT'] = $siteFolder = $checkFolder = stream_resolve_include_path(__DIR__ . '/../../../../..');

$isConsole = true;
require_once $siteFolder . '/_.php';

$config = \Ideal\Core\Config::getInstance();

// Если указана папка для сканирования, то берём её, иначе берём корневую папку сайта
if ($config->monitoring['scanDir']) {
    $checkFolder .= $config->monitoring['scanDir'];
}

// Собираем пути для исключения из сканировани
$excluded = array();
if ($config->monitoring['excludedScanDir']) {
    $excluded = explode(PHP_EOL, $config->monitoring['excludedScanDir']);
    // Подготавливаем пути исключений из сканирования для использования в регулярных выражениях
    if ($excluded) {
        foreach ($excluded as &$value) {
            $value = preg_quote($value, '/');
        }
    }
}

if (!file_exists($checkFolder)) {
    $message = 'Указанной папки для сканирования не существует';
} else {
    $cmsFolder = $siteFolder . '/' . $config->cmsFolder;

    // Собираем хэши файлов
    $siteFiles = CheckSiteFilesModel::getAllSystemFiles($checkFolder, $siteFolder, $excluded);

    // Записываем данные в файл информации о хэшах файлов системы
    $file = $siteFolder . '/tmp/site_hash_files';
    if (file_put_contents($file, serialize($siteFiles))) {
        echo "Success!\n";
    } else {
        echo "Write error in file {$file} \n";
    }
}
