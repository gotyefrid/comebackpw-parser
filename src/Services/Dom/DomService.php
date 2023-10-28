<?php

namespace Gotyefrid\ComebackpwParser\Services\Dom;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Models\BaseItem;
use Gotyefrid\ComebackpwParser\Models\Coordinates;
use Gotyefrid\ComebackpwParser\Models\Shop;
use Gotyefrid\ComebackpwParser\Models\ShopOwner;
use Gotyefrid\ComebackpwParser\Services\Interfaces\DomFinderInterface;

class DomService extends BaseObject implements DomFinderInterface
{
    public function getFilledItems(string $html): array
    {
        $pageHtml = self::createDomDocument($html);
        $xpath = new DOMXpath($pageHtml);
        /** @var DOMElement[] $shopsData */
        $shopsData = $xpath->query("//div[@class='cats__item']");

        foreach ($shopsData as $shopData) {
            $shopHtml = $shopData->ownerDocument->saveHTML($shopData);
            $shopHtmlQuery = self::createDomDocument($shopHtml);
            $shopXpath = new DOMXpath($shopHtmlQuery);

            $id = $shopData->getAttribute('data-id');
            $coordsWithRegion = $shopXpath->query("//p[@class='coordinate']")[0]?->textContent;

            $shopModels[] = new Shop([
                'id' => $id,
                'owner' => new ShopOwner([
                    'id' => $id,
                    'nickname' => $shopXpath->query("//p[@class='charactername']")[0]?->textContent,
                ]),
                'name' => $shopXpath->query("//p[@class='catname']")[0]?->textContent,
                'coordinates' => (new Coordinates())->map($coordsWithRegion),
                'html' => $shopHtml
            ]);
        }

        return $shopModels ?? [];
    }

    public function getItemsMain(string $shopHtml): array
    {
        $shopElement = self::createDomDocument($shopHtml);
        $xpath = new DOMXpath($shopElement);

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
     * @return DOMDocument
     */
    private static function createDomDocument($html): DOMDocument
    {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

        return $dom;
    }
}