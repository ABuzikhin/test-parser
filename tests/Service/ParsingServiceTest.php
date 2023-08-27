<?php

namespace Service;

use App\Entity\ParsedStatus;
use App\Entity\ParsedUrl;
use App\Entity\Product;
use App\Service\ParsingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group manual
 */
class ParsingServiceTest extends KernelTestCase
{
    /**
     * @var ParsingService
     */
    private $service;

    /**
     * @var EntityManagerInterface
     */
    private mixed $em;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $container     = static::getContainer();
        $this->service = $container->get(ParsingService::class);
        $this->em      = $container->get(EntityManagerInterface::class);

        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        $this->em->getConnection()->close();

        $this->service = null;
        $this->em      = null;

        parent::tearDown();
    }

    public function testParse()
    {
        $urls = [
            'https://www.sulpak.kg/g/smart_chasiy_apple_apple_watch_series_8_gps_45mm_midnight_aluminium_case_with_midnight_sport_band___regular_mnp13gka',
//            'https://www.sulpak.kg/g/mobilniyj_telefon_nokia_150_ds_black_',
//            'https://www.alza.cz/EN/kesper-wine-stand-dark-pine-50-x-50-x-25cm-d5649863.htm',
//            'https://www.alza.cz/EN/sandisk-microsdxc-512gb-extreme-rescue-pro-deluxe-sd-adapter-d7261106.htm',
//            'https://www.alza.cz/EN/amazon-kindle-paperwhite-5-2021-32gb-signature-edition-renovovany-bez-reklamy-d7729660.htm',
        ];

        $countBefore = $this->em->getRepository(Product::class)->count([]);

        foreach ($urls as $index => $url) {
            $parsedUrl = new ParsedUrl();
            $parsedUrl->setUrl($url);
            $this->em->persist($parsedUrl);

            $this->service->parse($parsedUrl);
            $this->em->clear();

            $this->em->find(ParsedUrl::class, $parsedUrl->getId());
            $this->assertEquals(ParsedStatus::PARSED, $parsedUrl->getStatus());
            $this->assertInstanceOf(Product::class, $parsedUrl->getProduct());

            $parsedResult = $parsedUrl->getParsedResult();
            $this->assertIsArray($parsedResult);
            $this->assertArrayHasKey('title', $parsedResult);
            $this->assertArrayHasKey('description', $parsedResult);
            $this->assertArrayHasKey('image_url', $parsedResult);
            $this->assertArrayHasKey('price', $parsedResult);
            $countAfter = $this->em->getRepository(Product::class)->count([]);

            $this->assertEquals($countBefore + 1 + $index, $countAfter);
        }

        $this->assertTrue(true);
    }
}
