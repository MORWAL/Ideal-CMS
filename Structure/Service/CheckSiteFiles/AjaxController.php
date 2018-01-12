<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2017 Ideal CMS (http://idealcms.ru/)
 * @license   http://idealcms.ru/license.html LGPL v3
 */
namespace Ideal\Structure\Service\CheckSiteFiles;

use Ideal\Core\Config;
use Ideal\Structure\Service\CheckSiteFiles\Model as CheckSiteFilesModel;

/**
 * Проверка целостности файлов системы
 *
 */
class AjaxController extends \Ideal\Core\AjaxController
{

    /**
     * Действие срабатывающее при нажатии на кнопку "Проверка целостности файлов"
     */
    public function checkCmsFilesAction()
    {
        $config = Config::getInstance();
        $cmsFolder = DOCUMENT_ROOT . '/' . $config->cmsFolder . '/Ideal';

        // Получаем актуальную информацию о хэшах системных файлов
        $actualSystemFilesHash = CheckSiteFilesModel::getAllSystemFiles($cmsFolder, $cmsFolder);

        // Подгружаем имеющуюся информацию о хэшах системных файлов
        $existingSystemFilesHash = unserialize(file_get_contents($cmsFolder . '/setup/prepare/hash_files'));

        $filesHashInfo = CheckSiteFilesModel::getDiff($actualSystemFilesHash, $existingSystemFilesHash);

        // Получаем строковое представление всех различий
        $changeFiles = implode('<br />', array_keys($filesHashInfo['changeFiles']));
        $delFiles = implode('<br />', array_keys($filesHashInfo['delFiles']));
        $newFiles = implode('<br />', array_keys($filesHashInfo['newFiles']));

        print json_encode(array('newFiles' => $newFiles, 'delFiles' => $delFiles, 'changeFiles' => $changeFiles));
        exit;
    }
}
