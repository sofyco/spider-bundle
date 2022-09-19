<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Tests\App\Service;

use Sofyco\Spider\Crawler\CrawlerInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;

final class ExampleService
{
    public function __construct(public readonly CrawlerInterface $crawler, public readonly ScraperInterface $scraper, public readonly ParserInterface $parser)
    {
    }
}
