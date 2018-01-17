<?php
use Ideal\Structure\Service\CheckSiteFiles\Model as CheckSiteFilesModel;
use Ideal\Core\Util;
use Mail\Sender;

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
    // Получаем папку, куда будет сохранён файл с хэшами
    if ($config->monitoring['tmpDir']) {
        $file = $siteFolder . $config->monitoring['tmpDir'] . '/site_hash_files';
    } else {
        // Если папка хранения файла не указана, то считаем что запись будет идти в папку "tmp" в корне сайта
        $file = $siteFolder . '/tmp/site_hash_files';
    }

    $cmsFolder = $siteFolder . '/' . $config->cmsFolder;

    // Собираем хэши файлов
    $actualSiteFiles = CheckSiteFilesModel::getAllSystemFiles($checkFolder, $siteFolder, $excluded);

    // Отправляем уведомление
    if ($config->monitoring['excludedScanDir']) {
        // Получаем хэши ранее собранных файлов
        $siteFiles = unserialize(file_get_contents($file));

        // Получаем разницу между свежими данными и ранее собранными
        $diff = CheckSiteFilesModel::getDiff($actualSiteFiles, $siteFiles);

        if ($diff['changeFiles']) {
            $changeFiles = 'Были внесены изменения в следующие файлы:<br />';
            $changeFiles .= implode('<br />', array_keys($diff['changeFiles'])) . '<br /><br />';
        } else {
            $changeFiles = 'Нет изменённых файлов<br /><br />';
        }
        if ($diff['delFiles']) {
            $delFiles = 'Были удалены следующие файлы:<br />' . implode('<br />', array_keys($diff['delFiles']));
        } else {
            $delFiles = 'Нет удалённых файлов';
        }
        if ($diff['newFiles']) {
            $newFiles = 'Были добавлены новые файлы:<br />' . implode('<br />', array_keys($diff['newFiles']));
            $newFiles .= '<br /><br />';
        } else {
            $newFiles = 'Нет новых файлов<br /><br />';
        }
        $message = <<<EMAIL
            <html>
            <head></head>
            <body>
                {$newFiles}

                {$changeFiles}
    
                {$delFiles}
            </body>
            </html>            
EMAIL;
        $mail = new Sender();
        $mail->setSubj('Изменения файлов сайта "' . $config->domain . '"');
        $mail->setHtmlBody($message);
        $mail->sent($config->robotEmail, $config->monitoring['excludedScanDir']);
    } else {
        Util::addError('Не указан получатель уведомлений об изменениях файлов сайта');
    }

    // Записываем данные в файл информации о хэшах файлов системы
    if (!file_put_contents($file, serialize($actualSiteFiles))) {
        $message = 'Не удалось записать данные о хэшах в файл "' . $file . '"';
    }
}
