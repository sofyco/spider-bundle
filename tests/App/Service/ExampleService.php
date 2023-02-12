<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Tests\App\Service;

use Sofyco\Spider\Crawler\CrawlerInterface;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\ScraperInterface;

final readonly class ExampleService
{
    public function __construct(public CrawlerInterface $crawler, public ScraperInterface $scraper, public ParserInterface $parser)
    {
    }
}
