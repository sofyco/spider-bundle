<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\ContentResult;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseCategories;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseContent;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseContentByUrl;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseDescription;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseImage;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParsePublishedTime;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseTags;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseTitle;
use Sofyco\Spider\Context;
use Sofyco\Spider\Scraper\ScraperInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsMessageHandler]
final readonly class ParseContentByUrlHandler
{
    public function __construct(private MessageBusInterface $bus, private ScraperInterface $scraper)
    {
    }

    public function __invoke(ParseContentByUrl $message): ?ContentResult
    {
        try {
            $html = $this->scraper->getResult(context: new Context(url: $message->url));
        } catch (\Throwable $exception) {
            return null;
        }

        /** @var string|null $image */
        $image = $this->bus->dispatch(new ParseImage(html: $html))->last(HandledStamp::class)?->getResult();

        /** @var string|null $title */
        $title = $this->bus->dispatch(new ParseTitle(html: $html))->last(HandledStamp::class)?->getResult();

        /** @var string|null $description */
        $description = $this->bus->dispatch(new ParseDescription(html: $html))->last(HandledStamp::class)?->getResult();

        /** @var string|null $content */
        $content = $this->bus->dispatch(new ParseContent(html: $html))->last(HandledStamp::class)?->getResult();

        /** @var string[] $tags */
        $tags = $this->bus->dispatch(new ParseTags(html: $html))->last(HandledStamp::class)?->getResult();

        /** @var string[] $categories */
        $categories = $this->bus->dispatch(new ParseCategories(html: $html))->last(HandledStamp::class)?->getResult();

        /** @var \DateTime|null $publishedAt */
        $publishedAt = $this->bus->dispatch(new ParsePublishedTime(html: $html))->last(HandledStamp::class)?->getResult();

        return new ContentResult(
            url: $message->url,
            image: $image,
            title: $title,
            description: $description,
            content: $content,
            tags: $tags,
            categories: $categories,
            publishedAt: $publishedAt,
        );
    }
}
