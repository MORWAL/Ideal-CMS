<?php
use Ideal\Structure\Service\CheckSiteFiles\Model as CheckSiteFilesModel;

// Ищем корневую папку сайта
$_SERVER['DOCUMENT_ROOT'] = $siteFolder = stream_resolve_include_path(__DIR__ . '/../../../../..');

$isConsole = true;
require_once $siteFolder . '/_.php';

$config = \Ideal\Core\Config::getInstance();

$cmsFolder = $siteFolder . '/' . $config->cmsFolder;

// Подключаем класс проверки целостности файлов
require $cmsFolder . '/Ideal/Structure/Service/CheckSiteFiles/Model.php';

// Собираем хэши файлов
$siteFiles = CheckSiteFilesModel::getAllSystemFiles($siteFolder, '');

// Записываем данные в файл информации о хэшах файлов системы
$file = $siteFolder . '/tmp/site_hash_files';
if (file_put_contents($file, serialize($siteFiles))) {
    echo "Success!\n";
} else {
    echo "Write error in file {$file} \n";
}
