<?php

namespace App\Tests\Service\Parsers;

use App\Dto\ParsedProductDto;
use App\Service\Parsers\SiteParserInterface;
use App\Service\Parsers\SulpakKgParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SulpakKgParserTest extends KernelTestCase
{
    /**
     * @var SiteParserInterface
     */
    private mixed $service;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container     = static::getContainer();
        $this->service = $container->get(SulpakKgParser::class);
    }

    protected function tearDown(): void
    {
        $this->service = null;

        parent::tearDown();
    }

    public function urlProvider()
    {
        yield 'ValidUrl v1' => [
            'url'    => 'https://www.sulpak.kg/g/noutbuki_asus_x1500ea_bq2259w_i385suw1_90nb0ty5_m038f0',
            'result' => true,
        ];

        yield 'ValidUrl v2' => [
            'url'    => 'https://sulpak.kg/g/noutbuki_asus_x1500ea_bq2259w_i385suw1_90nb0ty5_m038f0',
            'result' => true,
        ];

        yield 'InvalidUrl v1' => [
            'url'    => 'https://www.sulpak.kz/g/noutbuki_asus_x1500ea_bq2259w_i385suw1_90nb0ty5_m038f0',
            'result' => false,
        ];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testIsSupported(string $url, bool $result)
    {
        $this->assertEquals($result, $this->service->isSupported($url));
    }

    public function htmlProvider()
    {
        yield 'Valid HTML v1' => [
            'html' => \file_get_contents(__DIR__.'/../../_data/sulpak.kg.0.html'),
            'expected' => [
                'title' => 'Смарт-часы Apple Watch Series 8 GPS 45mm Midnight Aluminium Case with Midnight Sport Band - Regular MNP13GK/A',
                'descr' => 'Уведомления с просмотром или ответом: Да / Телефонные звонки: Нет / Диагональ дисплея: 1,77″ - 45 мм / Операционная система: Watch OS / Датчики: Оптический датчик пульса, датчик ЭКГ, пульсоксиметр, акселерометр, гироскоп ,датчикпадения, датчикосвещенности, альтиметр / Особенности: Часы с возможностью распознования падения и аварии, с функцией отследивания сердечного ритма',
                'price' => 43190.00,
                'image' => 'https://object.pscloud.io/cms/cms/Photo/img_0_911_722_0_1.jpg',
            ]
        ];

        yield 'Valid HTML v2' => [
            'html' => \file_get_contents(__DIR__.'/../../_data/sulpak.kg.1.html'),
            'expected' => [
                'title' => 'Ноутбук Asus X1500EA-BQ2259W Corei3 1115G4 8GB / SSD 512GB / Win11 / 90NB0TY5-M038F0',
                'descr' => 'Процессор: Intel® Core™ i3 / Объем оперативной памяти: 8 GB / Операционная система: Windows 11 Home / Диагональ: 15,6″ - 39,62 см',
                'price' => 50990.00,
                'image' => 'https://object.pscloud.io/cms/cms/Photo/img_0_62_2746_0_6.png',
            ]
        ];

    }

    /**
     * @dataProvider htmlProvider
     *
     * @return void
     */
    public function testProcessDto(string $html, array $expected)
    {
        $dto = (new ParsedProductDto('test.url'))
            ->setResponseHtml($html);
        $dto = $this->service->processDto($dto);

        $this->assertEquals($expected['title'], $dto->getTitle());
        $this->assertEquals($expected['descr'], $dto->getDescription());
        $this->assertEquals($expected['price'], $dto->getPrice());
        $this->assertEquals($expected['image'], $dto->getImageUrl());
    }
}
