<?php

namespace FormPhp\Field\Referer;


use FormPhp\Field\AbstractField;

/**
 * Простое текстовое поле ввода
 *
 */
class Controller extends AbstractField
{
    public function getValue()
    {
        return (isset($_COOKIE['referer'])) ? $_COOKIE['referer'] : 'empty';
    }
}
