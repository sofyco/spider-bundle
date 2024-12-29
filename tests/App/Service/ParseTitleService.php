<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Tests\App\Service;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseTitle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class ParseTitleService
{
    public function __construct(public MessageBusInterface $bus)
    {
    }

    public function getTitle(string $html): mixed
    {
        return $this->bus->dispatch(new ParseTitle(html: $html))->last(HandledStamp::class)?->getResult();
    }
}
