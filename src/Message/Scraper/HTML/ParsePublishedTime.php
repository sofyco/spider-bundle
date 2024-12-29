<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML;

final class ParsePublishedTime
{
    public function __construct(public string $html)
    {
    }
}
