<?php
// @codingStandardsIgnoreFile
return array(
    'pageroot' => "", // Корневая папка сайта на диске | Ideal_Text
    'website' => "", // Сайт для сканирования | Ideal_Text
    'yandexRssFile' => "/yandexrss.xml", // Путь до файла турбо-фида | Ideal_Text
    'sitemapFile' => "/sitemap.xml", // Путь до файла карты сайта | Ideal_Text
    'yandexRssTempFile' => "/tmp/yandexTempRss.xml", // Путь до временного файла турбо-фида | Ideal_Text
    'linksFile' => "/tmp/links", // Путь до файла со списком ссылок из карты сайта | Ideal_Text
    'tagLimiter' => "turbofeed", // тэг html-комментария (например "turbofeed", тогда контент будет браться между тегами <!--turbofeed--><!--end_turbofeed-->)| Ideal_Text
    'disallow_regexp' => "", // Регулярные выражения для адресов, которые не надо включать в турбо-фид | Ideal_RegexpList
    'email_notify' => "errors@neox.ru", // Электронная почта для уведомления об ошибках в процессе работы скрипта  | Ideal_Text
);
