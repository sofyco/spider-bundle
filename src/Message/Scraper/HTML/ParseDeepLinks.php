<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML;

final class ParseDeepLinks
{
    public function __construct(public string $url)
    {
    }
}
