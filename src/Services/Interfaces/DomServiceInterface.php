<?php

namespace Gotyefrid\ComebackpwParser\Services\Interfaces;

interface DomServiceInterface
{
    /**
     * Получить массив кусков html кода, которые отвечают за строчку об одном магазине (из таблицы магазинов)
     * @param string $html Общая страница со всеми котами (магазинами)
     * @return array
     */
    public function getShopsHtml(string $html): array;

    /**
     * Видим ли мы все товары в этом магазине, или нет (на общей странице)
     * @param string $shopGridHtml
     * @return bool
     */
    public function isExistMoreButton(string $shopGridHtml): bool;

    /**
     * Вернуть ID магазина из куска html конкретного магазина с общей страницы
     * @param string $shopHtml
     * @return int
     */
    public function getShopId(string $shopHtml): int;

    /**
     * Получить html код отдельной страницы магазина
     * @param string $html
     * @return string
     */
    public function getFullShopHtml(string $html): string;

    /**
     * Проверить в коде общей страницы магазинов последняя ли это страница
     * @param string $html
     * @return bool
     */
    public function checkIsLastPage(string $html): bool;
}