<style>
    #iframe {
        margin-top: 15px;
    }

    #iframe iframe {
        width: 100%;
        border: 1px solid #E7E7E7;
        border-radius: 6px;
        height: 300px;
    }

    #loading {
        -webkit-animation: loading 3s linear infinite;
        animation: loading 3s linear infinite;
    }

    @-webkit-keyframes loading {
        0% {
            color: rgba(34, 34, 34, 1);
        }
        50% {
            color: rgba(34, 34, 34, 0);
        }
        100% {
            color: rgba(34, 34, 34, 1);
        }
    }

    @keyframes loading {
        0% {
            color: rgba(34, 34, 34, 1);
        }
        50% {
            color: rgba(34, 34, 34, 0);
        }
        100% {
            color: rgba(34, 34, 34, 1);
        }
    }
</style>

<?php
$config = \Ideal\Core\Config::getInstance();
$file = new \Ideal\Structure\Service\SiteData\ConfigPhp();

if (!$file->loadFile(DOCUMENT_ROOT . '/' . $config->cmsFolder . '/site_turbo.php')) {
    // Если не удалось прочитать данные из кастомного файла, значит его нет
    // Поэтому читаем данные из демо-файла
    $file->loadFile(DOCUMENT_ROOT . '/' . $config->cmsFolder . '/Ideal/Library/YandexTurboPage/site_turbo_demo.php');
    $params = $file->getParams();
    $params['default']['arr']['website']['value'] = 'http://' . $config->domain;
    if (empty($_SERVER['DOCUMENT_ROOT'])) {
        // Обнаружение корня сайта, если скрипт запускается из стандартного места в Ideal CMS
        $self = $_SERVER['PHP_SELF'];
        $path = substr($self, 0, strpos($self, 'Ideal') - 1);
        $params['default']['arr']['pageroot']['value'] = dirname($path);
    } else {
        $params['default']['arr']['pageroot']['value'] = $_SERVER['DOCUMENT_ROOT'];
    }
    $file->setParams($params);
}

if (isset($_POST['edit'])) {
    $file->changeAndSave(DOCUMENT_ROOT . '/' . $config->cmsFolder . '/site_turbo.php');
}
?>

<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="active"><a href="#settings" data-toggle="tab">Настройки</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="settings">
        <form action="" method=post enctype="multipart/form-data">

            <?php echo $file->showEdit(); ?>

            <br/>

            <input type="submit" class="btn btn-info" name="edit" value="Сохранить настройки"/>
        </form>
    </div>
</div>