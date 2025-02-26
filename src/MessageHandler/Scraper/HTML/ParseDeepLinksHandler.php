<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\ContentResult;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseContentByUrl;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseDeepLinks;
use Sofyco\Spider\Context;
use Sofyco\Spider\Crawler\CrawlerInterface;
use Sofyco\Spider\Parser\Builder\Node;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsMessageHandler]
final readonly class ParseDeepLinksHandler
{
    public function __construct(private MessageBusInterface $bus, private CrawlerInterface $crawler)
    {
    }

    public function __invoke(ParseDeepLinks $message): iterable
    {
        $node = new Node(type: Node\Type::ATTRIBUTE, selector: 'a', attribute: 'href');
        $context = new Context(url: $message->url);

        foreach ($this->crawler->getResult(context: $context, node: $node) as $item) {
            /** @var ContentResult $contentResult */
            $contentResult = $this->bus->dispatch(new ParseContentByUrl(url: $item->getUrl()))->last(HandledStamp::class)?->getResult();

            if (empty($contentResult->image) || empty($contentResult->categories) || empty($contentResult->publishedAt)) {
                continue;
            }

            yield $contentResult;
        }
    }
}
