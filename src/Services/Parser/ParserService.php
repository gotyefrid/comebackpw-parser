<?php

namespace Gotyefrid\ComebackpwParser\Services\Parser;

use Exception;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Models\Shop;
use Gotyefrid\ComebackpwParser\Models\ShopItem;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;
use Gotyefrid\ComebackpwParser\Services\Interfaces\DomFinderInterface;
use Gotyefrid\ComebackpwParser\Services\WebDriver\Chrome;
use Gotyefrid\ComebackpwParser\Services\WebDriver\WebDriverInterface;

class ParserService extends BaseObject
{
    public string $version = '146x';
    public string $mainUrl = 'https://comeback.pw/cats/';
    public ?DomFinderInterface $domFinder = null;
    public ?WebDriverInterface $webDriver = null;

    public const TYPE_ALL = 1;
    public const TYPE_ARMOR = 2;
    public const TYPES = [
        self::TYPE_ALL => 'all',
        self::TYPE_ARMOR => 'armor'
    ];

    public bool $isLastPage = true;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (!$this->domFinder) {
            $this->domFinder = new DomService();
        }

        if (!$this->webDriver) {
            $this->webDriver = new Chrome();
        }
    }

    public function run(int $type = self::TYPE_ALL): ShopItem
    {
        $page = 1;

        do {
            $mainHtml = $this->getMainHtml($page);
            $shopsHtml = $this->domFinder->getShopsHtml($mainHtml);
            $shops[] = $this->createShops($shopsHtml);
            //$this->isLastPage = $this->checkIsLastPage($mainHtml);
            $this->isLastPage = true;
            $page++;
        } while ($this->isLastPage === false);

        $r = 123;
    }

    /**
     * @param int $page
     * @return Shop[]
     * @throws Exception
     */
    public function getMainHtml(int $page): string
    {
        $html = $this->getHtml($this->getMainUrl(['page' => $page]));

        if ($this->isMainHtmlValid($html)) {
            return $html;
        } else {
            throw new Exception('Основной html невалидный');
        }
    }

    private function getHtml(string $url): string
    {
        $html = $this->webDriver::getHtml($url);

        if (!$html) {
            throw new Exception('Получили пустой html');
        }

        return $html;
    }

    /**
     * @param array $query
     * @return string
     */
    public function getMainUrl(array $query): string
    {
        $query = http_build_query($query);

        return $this->mainUrl . $this->version . "?$query";
    }

    public function getShopUrl(int $shopId): string
    {
        $query = [
            'shop' => $shopId
        ];
        $query = http_build_query($query);

        return $this->mainUrl . $this->version . "?$query";
    }

    private function isMainHtmlValid(string $html): bool
    {
        return str_contains($html, '<div id="content">');
    }

    private function createShops(array $shopsHtml)
    {
        foreach ($shopsHtml as $shopHtml) {
            $isExistMore = $this->domFinder->isExistMore($shopHtml);
            $shopId = $this->domFinder->getShopId($shopHtml);

            if ($isExistMore) {
                $shopHtml = $this->getHtml($this->getShopUrl($shopId));
                $shopHtml = $this->domFinder->getShopHtml($shopHtml);
            }

            $shops[] = new Shop($shopHtml, [
                'id' => $shopId
            ]);
        }

        return $shops ?? [];
    }

    private function checkIsLastPage(string $html)
    {
        $dom = DomService::createDomDocument($html);
        /** @var \DOMNodeList $nextButton */
        $nextButton = $dom->query('//button[@class="pagination_button" and text()="Вперед"]');

        if (isset($nextButton[0])) {
            /** @var \DOMElement $btn */
            $btn = $nextButton[0];

            if ((int)$btn->hasAttribute('data-page')) {
                return false;
            }

            return true;
        }

        throw new Exception('Не найдена предпоследняя кнопка пагинации');

    }
}