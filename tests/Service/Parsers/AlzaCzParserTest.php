<?php

namespace Service\Parsers;

use App\Dto\ParsedProductDto;
use App\Service\Parsers\AlzaCzParser;
use App\Service\Parsers\SiteParserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AlzaCzParserTest extends KernelTestCase
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
        $this->service = $container->get(AlzaCzParser::class);
    }

    protected function tearDown(): void
    {
        $this->service = null;

        parent::tearDown();
    }

    public function urlProvider()
    {
        yield 'ValidUrl v1' => [
            'url'    => 'https://www.alza.cz/EN/sandisk-microsdxc-512gb-extreme-rescue-pro-deluxe-sd-adapter-d7261106.htm',
            'result' => true,
        ];

        yield 'ValidUrl v2' => [
            'url'    => 'https://alza.cz/EN/sandisk-microsdxc-512gb-extreme-rescue-pro-deluxe-sd-adapter-d7261106.htm',
            'result' => true,
        ];

        yield 'InvalidUrl v1' => [
            'url'    => 'https://www.alza0.cz/EN/sandisk-microsdxc-512gb-extreme-rescue-pro-deluxe-sd-adapter-d7261106.htm',
            'result' => false,
        ];

        yield 'InvalidUrl v2' => [
            'url'    => 'https://alza.cn/EN/sandisk-microsdxc-512gb-extreme-rescue-pro-deluxe-sd-adapter-d7261106.htm',
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
            'html' => \file_get_contents(__DIR__.'/../../_data/alza.cz.0.html'),
            'expected' => [
                'title' => 'SanDisk microSDXC 512GB Extreme + Rescue PRO Deluxe + SD adapter',
                'descr' => 'Memory Card micro SDXC, 512GB, read speed up to 190MB/s, write speed up to 130MB/s, Class 10, UHS-I, U3, V30, A2',
                'price' => 1299.00,
                'image' => 'https://cdn.alza.cz/Foto/f16/DU/DU828a2a4.jpg',
            ]
        ];

        yield 'Valid HTML v2' => [
            'html' => \file_get_contents(__DIR__.'/../../_data/alza.cz.1.html'),
            'expected' => [
                'title' => 'Amazon Kindle Paperwhite 5 2021 32GB Signature Edition (renovovaný bez reklamy)',
                'descr' => 'E-Book Reader 6,8" backlit touchscreen display, WiFi and Bluetooth, 32GB, No memory card, USB-C',
                'price' => 3699.00,
                'image' => 'https://cdn.alza.cz/Foto/f16/MD/MD050c172a13.jpg',
            ]
        ];

        yield 'Valid HTML v3' => [
            'html' => \file_get_contents(__DIR__.'/../../_data/alza.cz.2.html'),
            'expected' => [
                'title' => 'Apple AirPods 2019',
                'descr' => 'Bezdrátová sluchátka - s mikrofonem, True Wireless, pecky, uzavřená konstrukce, Bluetooth 5.0, Ambient sound, Hi-Res audio, hlasový asistent, přepínání skladeb, přijímání hovorů, výdrž baterie až 24 h (5 h+19 h)',
                'price' => 3290.00,
                'image' => 'https://cdn.alza.cz/Foto/ImgGalery/Image/Article/placeholder-fyzicke.png',
            ]
        ];

        yield 'Valid HTML v4' => [
            'html' => \file_get_contents(__DIR__.'/../../_data/alza.cz.3.html'),
            'expected' => [
                'title' => 'Kesper Wine Stand, Dark Pine 50 x 50 x 25cm',
                'descr' => 'Wine Rack - size: width: 50cm, height: 50cm, depth: 25cm, thickness of individual boards: 1.5cm, can take up to 20 bottles, can be combined with other stands, goods are delivered disassembled, assembly instructions and assembly material included',
                'price' => 1290.00,
                'image' => 'https://cdn.alza.cz/Foto/f16/KE/KESBD149.jpg',
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

    public function errorHtmlProvider()
    {
        yield 'Error NoTitle' => [
            'html'     => \file_get_contents(__DIR__.'/../../_data/alza.cz.0.error.title.html'),
            'expected' => 'Title Path not found in downloaded document.',
        ];

        yield 'Error NoDescription' => [
            'html'     => \file_get_contents(__DIR__.'/../../_data/alza.cz.0.error.descr.html'),
            'expected' => 'Description Path not found in downloaded document.',
        ];

        yield 'Error NoImage' => [
            'html'     => \file_get_contents(__DIR__.'/../../_data/alza.cz.0.error.image.html'),
            'expected' => 'Image Path not found in downloaded document.',
        ];

        yield 'Error NoPrice' => [
            'html'     => \file_get_contents(__DIR__.'/../../_data/alza.cz.0.error.price.html'),
            'expected' => 'Price Path not found in downloaded document.',
        ];
    }

    /**
     * @dataProvider errorHtmlProvider
     *
     * @return void
     */
    public function testErrorProcessDto(string $html, string $expected)
    {
        $dto = (new ParsedProductDto('test.url'))
            ->setResponseHtml($html);

        $this->expectExceptionMessage($expected);

        $this->service->processDto($dto);
    }

}
