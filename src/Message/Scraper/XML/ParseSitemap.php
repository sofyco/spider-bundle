<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Message\Scraper\XML;

final readonly class ParseSitemap
{
    public function __construct(public string $url)
    {
    }
}
