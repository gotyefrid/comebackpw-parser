<?php

namespace Gotyefrid\ComebackpwParser\Services\Parser;

use Exception;
use Generator;
use Gotyefrid\ComebackpwParser\Base\BaseObject;
use Gotyefrid\ComebackpwParser\Models\Shop;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;
use Gotyefrid\ComebackpwParser\Services\Interfaces\DomServiceInterface;
use Gotyefrid\ComebackpwParser\Services\WebDriver\Chrome;
use Gotyefrid\ComebackpwParser\Services\WebDriver\WebDriverInterface;

class ParserService extends BaseObject
{
    /**
     * @var string версия где парсить
     */
    public string $version = '146x';

    /**
     * @var string основной адрес
     */
    public string $mainUrl = 'https://comeback.pw/cats/';

    /**
     * Сервис отвечающий за поиск данных в HTML
     * @var DomServiceInterface|null
     */
    public ?DomServiceInterface $domService = null;

    /**
     * Класс ответственный за получение html по url
     * @var WebDriverInterface|null
     */
    public ?WebDriverInterface $webDriver = null;
    /**
     * @var bool Указатель, последняя ли это страница
     */
    public bool $isLastPage = true;

    /**
     * @inheritDoc
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        if (!$this->domService) {
            $this->domService = new DomService();
        }

        if (!$this->webDriver) {
            $this->webDriver = new Chrome();
        }
    }

    /**
     * Получить текущую пачку результатов (магазинов)
     * @param int $page Начальная страница
     * @return Generator По факту возрващается заполненные модели Shop с текущей страницы
     */
    public function parsePage(int &$page = 1): Generator
    {
        while (true) {
            $mainHtml = $this->getMainPageHtml($page);
            $shopsHtml = $this->domService->getShopsHtml($mainHtml);
            $shops = $this->createShops($shopsHtml);
            $this->isLastPage = $this->checkIsLastPage($mainHtml);
            $page++;

            yield $shops;
        }
    }

    /**
     * Получить содержимое общей страницы с магазинами
     * @param int $page
     * @return string
     * @throws Exception
     */
    public function getMainPageHtml(int $page): string
    {
        return $this->getHtml($this->getMainUrl(['page' => $page]));
    }

    /**
     * Получить содержимое конкретного магазина
     * @param int $shopId
     * @return string
     * @throws Exception
     */
    public function getShopPageHtml(int $shopId): string
    {
        return $this->getHtml($this->getShopUrl($shopId));
    }

    /**
     * Получить содержимое страницы
     * @param string $url
     * @return string
     * @throws Exception
     */
    private function getHtml(string $url): string
    {
        $html = $this->webDriver->getHtml($url);

        if (!$html) {
            throw new Exception('Получили пустой html');
        }

        return $html;
    }

    /**
     * Получить основной урл с указанными параметрами
     * @param array $query
     * @return string
     */
    private function getMainUrl(array $query): string
    {
        $query = http_build_query($query);

        return $this->mainUrl . $this->version . "?$query";
    }

    /**
     * Получить урл конкретного магазина
     * @param int $shopId
     * @return string
     */
    private function getShopUrl(int $shopId): string
    {
        $query = [
            'shop' => $shopId
        ];
        $query = http_build_query($query);

        return $this->mainUrl . $this->version . "?$query";
    }

    /**
     * Создать модели магазинов с основной страницы, а если на основной странице не все товары -
     * то зайти в конкретный магазин и спарсить все остальные товары
     * @param array $shopsHtml Набор кусков кода магазинов из общей страницы
     * @return array
     * @throws Exception
     */
    private function createShops(array $shopsHtml): array
    {
        foreach ($shopsHtml as $shopHtml) {
            $isExistMore = $this->domService->isExistMoreButton($shopHtml);
            $shopId = $this->domService->getShopId($shopHtml);

            if ($isExistMore) {
                $shopHtml = $this->getShopPageHtml($shopId);
                $shopHtml = $this->domService->getFullShopHtml($shopHtml);
            }

            $shops[] = new Shop($shopHtml, [
                'id' => $shopId
            ]);
        }

        return $shops ?? [];
    }

    /**
     * Проверить последняя ли это страница
     * @param string $html Код основной страницы с магазинами
     * @return bool
     * @throws Exception
     */
    private function checkIsLastPage(string $html): bool
    {
        return $this->domService->checkIsLastPage($html);
    }
}