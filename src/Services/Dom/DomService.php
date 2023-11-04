<?php

namespace Gotyefrid\ComebackpwParser\Services\Dom;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Models\BaseItem;
use Gotyefrid\ComebackpwParser\Services\Interfaces\DomFinderInterface;

class DomService extends BaseObject implements DomFinderInterface
{
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

    public function getItemsMain(string $shopHtml): array
    {
        $xpath = self::createDomDocument($shopHtml);
        $itemsDom = $xpath->query("//img[@class='ibs_img']");

        foreach ($itemsDom as $item) {
            $items[] = new BaseItem([
                'name' => $item->getAttribute('data-name'),
                'count' => $item->getAttribute('data-count'),
                'price' => $item->getAttribute('data-price'),
            ]);
        }

        return $items ?? [];
    }

    /**
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

    public function isExistMore(string $shopHtml): bool
    {
        $query = self::createDomDocument($shopHtml);

        /** @var DOMNodeList $existMore */
        $existMore = $query->query("//div[@class='its_button']");

        if ($existMore->length) {
            return true;
        }

        return false;
    }

    public function getShopId(string $shopHtml)
    {
        $query = self::createDomDocument($shopHtml);

        return $query->query("//div[@class='cats__item']")[0]->getAttribute('data-id');
    }

    public function getShopHtml(string $html)
    {
        $query = self::createDomDocument($html);
        $shopHtml = $query->query("//div[@class='shop_window']")[0];

        if (!$shopHtml) {
            throw new \Exception();
        }

        return $shopHtml->ownerDocument->saveHTML($shopHtml);
    }
}