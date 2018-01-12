<?php
/**
 * Ideal CMS (http://idealcms.ru/)
 *
 * @link      http://github.com/ideals/idealcms репозиторий исходного кода
 * @copyright Copyright (c) 2012-2018 Ideal CMS (http://idealcms.ru/)
 * @license   http://idealcms.ru/license.html LGPL v3
 */

namespace Ideal\Structure\Service\CheckSiteFiles;

class Model
{

    /**
     * Действие срабатывающее при нажатии на кнопку "Проверка целостности файлов"
     * @param array $actualSystemFilesHash Массив где ключами являются пути до файлов, а значениями их хэши
     * @param array $existingSystemFilesHash Массив уже имеющихся данных о хэшах файлов
     * @return array Массив содержащий информацию о новых, удалённых и изменённых файлах
     */
    public static function getDiff($actualSystemFilesHash, $existingSystemFilesHash)
    {
        $response = array();

        // Получаем список новых системных файлов
        $response['newFiles'] = array_diff_key($actualSystemFilesHash, $existingSystemFilesHash);

        // Получаем список системных файлов которые были удалены
        $response['delFiles'] = array_diff_key($existingSystemFilesHash, $actualSystemFilesHash);

        // Получаем список файлов, которые были изменены
        $changeFiles = array_diff($actualSystemFilesHash, $existingSystemFilesHash);
        $response['changeFiles'] = array_diff($changeFiles, $response['newFiles']);

        return $response;
    }

    /**
     * Получает массив, где ключи это путь до файла, а значения это хэш файла
     *
     * @param string $folder Путь до сканируемой папки
     * @param string $cmsFolder Путь до корневой папки системы
     * @return array Массив где ключами являются пути до файлов, а значениями их хэши
     */
    public static function getAllSystemFiles($folder, $cmsFolder)
    {
        $systemFiles = array();
        $files = scandir($folder);
        foreach ($files as $file) {
            // Отбрасываем не нужные каталоги и файлы
            if (preg_match('/^\..*?|hash_files$/isU', $file)) {
                continue;
            }
            // Если директория, то запускаем сбор внутри директории
            if (is_dir($folder . '/' . $file)) {
                $systemFiles = array_merge($systemFiles, self::getAllSystemFiles($folder . '/' . $file, $cmsFolder));
            } else {
                $fileKeyArray = ltrim(str_replace($cmsFolder, '', $folder) . '/' . $file, '/');
                $systemFiles[$fileKeyArray] = hash_file('crc32b', $folder . '/' . $file);
            }
        }
        return $systemFiles;
    }
}
