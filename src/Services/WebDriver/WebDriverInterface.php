<?php

namespace Gotyefrid\ComebackpwParser\Services\WebDriver;

interface WebDriverInterface
{
    /**
     * Получить содержимое по ссылке
     * @param string $url
     * @return string
     */
    public function getHtml(string $url): string;
}