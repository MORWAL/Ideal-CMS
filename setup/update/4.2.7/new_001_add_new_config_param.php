<?php
/**
 * Добавление дополнительных полей в файл site_data.php
 */

$config = \Ideal\Core\Config::getInstance();
$configSD = new \Ideal\Structure\Service\SiteData\ConfigPhp();

$file = DOCUMENT_ROOT . '/' . $config->cmsFolder . '/site_data.php';
$configSD->loadFile($file);
$params = $configSD->getParams();
// Если поле уже есть, то ничего делать не нужно
if (!isset($params['monitoring'])) {
    $params['monitoring'] = array(
        'name' => 'Мониторинг изменений в системе',
        'arr' => array(
            'scanDir' => array(
                'label' => 'Путь до папки (с ведущим слешем), в которой нужно проводить сканирование. Если пусто, то сканируется весь сайт.',
                'value' => '',
                'type' => 'Ideal_Text'
            ),
            'excludedScanDir' => array(
                'label' => 'Пути до папок или файлов (с ведущим слешем), которые нужно исключить из сканирования (каждый с новой строки)',
                'value' => '',
                'type' => 'Ideal_Area'
            ),
            'mailNotification' => array(
                'label' => 'Почта для отправки уведомлений об изменениях',
                'value' => 'top@neox.ru',
                'type' => 'Ideal_Text'
            ),
            'tmpDir' => array(
                'label' => 'Путь к папке (с ведущим слешем) с временными файлами',
                'value' => '/tmp',
                'type' => 'Ideal_Text'
            )
        )
    );
}
$configSD->setParams($params);
$configSD->saveFile($file);
