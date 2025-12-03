<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Tests\DependencyInjection;

use Sofyco\Bundle\SpiderBundle\Tests\App\Service\ExampleService;
use Sofyco\Spider\Crawler\CrawlerInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class SpiderExtensionTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    public function testInjectedServices(): void
    {
        /** @var ExampleService $service */
        $service = self::bootKernel()->getContainer()->get(ExampleService::class);

        self::assertInstanceOf(CrawlerInterface::class, $service->crawler);
        self::assertInstanceOf(ScraperInterface::class, $service->scraper);
        self::assertInstanceOf(ParserInterface::class, $service->parser);
    }
}
