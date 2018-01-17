<?php
use Ideal\Structure\Service\CheckSiteFiles\Model as CheckSiteFilesModel;
use Ideal\Core\Util;
use Mail\Sender;

$errorMessage = '';

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
    $errorMessage = 'Указанной папки для сканирования не существует';
} else {
    // Получаем папку, куда будет сохранён файл с хэшами
    if ($config->monitoring['tmpDir']) {
        $filesHashesFile = $siteFolder . $config->monitoring['tmpDir'] . '/site_hash_files';
    } else {
        // Если папка хранения файла не указана, то считаем что запись будет идти в папку "tmp" в корне сайта
        $filesHashesFile = $siteFolder . '/tmp/site_hash_files';
    }

    // В массив путей исключённых из сканирования добавляем файл с хэшами
    $relativePathFilesHashesFile = str_replace($siteFolder, '', $filesHashesFile);
    $excluded[] = preg_quote($relativePathFilesHashesFile, '/');

    $cmsFolder = $siteFolder . '/' . $config->cmsFolder;

    // Собираем хэши файлов
    $actualSiteFiles = CheckSiteFilesModel::getAllSystemFiles($checkFolder, $siteFolder, $excluded);

    // Отправляем уведомление
    if ($config->monitoring['mailNotification']) {
        // Получаем хэши ранее собранных файлов
        $siteFiles = unserialize(file_get_contents($filesHashesFile));

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
        $messageToSend = <<<EMAIL
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
        $mail->setHtmlBody($messageToSend);
        $mail->sent($config->robotEmail, $config->monitoring['mailNotification']);
    } else {
        $errorMessage = 'Не указан получатель уведомлений об изменениях файлов сайта';
    }

    // Записываем данные в файл информации о хэшах файлов системы
    if (!file_put_contents($filesHashesFile, serialize($actualSiteFiles))) {
        $errorMessage = 'Не удалось записать данные о хэшах в файл "' . $filesHashesFile . '"';
    }
}

// Если есть какие-либо ошибки, уведомляем о них
if ($errorMessage) {
    Util::addError($errorMessage);
}
