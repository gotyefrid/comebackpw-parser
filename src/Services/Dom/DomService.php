<?php

namespace Gotyefrid\ComebackpwParser\Services\Dom;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Exception;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Services\Interfaces\DomServiceInterface;

class DomService extends BaseObject implements DomServiceInterface
{
    /**
     * Получить массив кусков html кода, которые отвечают за строчку об одном магазине (из таблицы магазинов)
     * @param string $html Общая страница со всеми котами (магазинами)
     * @return array
     */
    public function getShopsHtml(string $html): array
    {
        $xpath = self::createDomDocument($html);
        /** @var DOMElement[] $shopsData */
        $shopsData = $xpath->query("//div[@class='cats__item']");

        foreach ($shopsData as $shopData) {
            $shopHtml[] = $shopData->ownerDocument->saveHTML($shopData);
        }

        return $shopHtml ?? [];
    }

    /**
     * Создать ДОМ документ и вернуть инструмент поиска по нему через xPath
     * Добавяем строку перед html чтобы избежать проблем с кодировкой кириллицы
     * @param $html
     * @return DOMXPath
     */
    public static function createDomDocument($html): DOMXpath
    {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        return new DOMXpath($dom);
    }

    /**
     * Видим ли мы все товары в этом магазине, или нет (на общей странице)
     * @param string $shopGridHtml
     * @return bool
     */
    public function isExistMoreButton(string $shopGridHtml): bool
    {
        $query = self::createDomDocument($shopGridHtml);

        /** @var DOMNodeList $existMore */
        $existMore = $query->query("//div[@class='its_button']");

        if ($existMore->length) {
            return true;
        }

        return false;
    }

    /**
     * Вернуть ID магазина из куска html конкретного магазина с общей страницы
     * @param string $shopHtml
     * @return int
     */
    public function getShopId(string $shopHtml): int
    {
        $query = self::createDomDocument($shopHtml);

        return (int)$query->query("//div[@class='cats__item']")[0]->getAttribute('data-id');
    }

    /**
     * Получить html код отдельной страницы магазина
     * @param string $html
     * @return string
     * @throws Exception
     */
    public function getFullShopHtml(string $html): string
    {
        $query = self::createDomDocument($html);
        $shopHtml = $query->query("//div[@class='shop_window']")[0];

        if (!$shopHtml) {
            throw new Exception('Не получен html конкретного магазина');
        }

        return $shopHtml->ownerDocument->saveHTML($shopHtml);
    }

    /**
     * Проверить в коде общей страницы магазинов последняя ли это страница
     * @param string $html
     * @return bool
     * @throws Exception
     */
    public function checkIsLastPage(string $html): bool
    {
        $dom = self::createDomDocument($html);
        /** @var DOMNodeList $nextButton */
        $nextButton = $dom->query('//button[@class="pagination_button" and text()="Вперед"]');

        if (isset($nextButton[0])) {
            /** @var DOMElement $btn */
            $btn = $nextButton[0];

            if ((int)$btn->hasAttribute('data-page')) {
                return false;
            }

            return true;
        }

        throw new Exception('Не найдена предпоследняя кнопка пагинации');
    }
}