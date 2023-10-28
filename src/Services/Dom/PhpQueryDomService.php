<?php

namespace Gotyefrid\ComebackpwParser\Services\Dom;

use DOMElement;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Models\Coordinates;
use Gotyefrid\ComebackpwParser\Models\Shop;
use Gotyefrid\ComebackpwParser\Models\ShopOwner;
use Gotyefrid\ComebackpwParser\Services\Interfaces\DomFinderInterface;
use PhpQuery\PhpQuery;

class PhpQueryDomService extends BaseObject implements DomFinderInterface
{
    public function getFilledItems(string $html): array
    {
        $pageHtml = new PhpQuery();
        $pageHtml->load_str($html);

        /** @var DOMElement[] $shopsData */
        $shopsData = $pageHtml->xpath("//div[@class='cats__item']");

        foreach ($shopsData as $shopData) {
            $shopHtml = $shopData->ownerDocument->saveHTML($shopData);
            $shopHtmlQuery = new PhpQuery();
            $shopHtmlQuery->load_str($shopHtml);

            $id = $shopData->getAttribute('data-id');
            $coordsWithRegion = $pageHtml->xpath("//*[@data-id=$id]/p[@class=\"coordinate\"]")[0]?->textContent;

            $shopModels[] = new Shop([
                'id' => $id,
                'owner' => new ShopOwner([
                    'id' => $id,
                    'nickname' => $pageHtml->xpath("//*[@data-id=$id]/p[@class=\"charactername\"]")[0]?->textContent,
                ]),
                'name' => $pageHtml->xpath("//*[@data-id=$id]/p[@class=\"catname\"]")[0]?->textContent,
                'coordinates' => (new Coordinates())->map($coordsWithRegion),
                'html' => $shopHtml
            ]);
        }

        return $shopModels ?? [];
    }

    public function getItemsMain(string $shopHtml): array
    {
        $query = new PhpQuery();
        $query->load_str($shopHtml);

        $items = $query->xpath("//div[@class='itemsell']//*");


        ob_flush();
        var_dump(123);die;
    }
}