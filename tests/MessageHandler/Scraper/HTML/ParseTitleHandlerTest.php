<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Tests\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Tests\App\Service\ParseTitleService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ParseTitleHandlerTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        restore_exception_handler();
    }

    public function testExample(): void
    {
        /** @var ParseTitleService $service */
        $service = self::bootKernel()->getContainer()->get(ParseTitleService::class);

        $html = '<div><h1>MyTitle</h1><span>Test</span></div>';

        self::assertSame('MyTitle', $service->getTitle(html: $html));
    }
}
