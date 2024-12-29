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
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final readonly class ParseContentByUrlHandler
{
    public function __construct(private MessageBusInterface $bus, private HttpClientInterface $httpClient)
    {
    }

    public function __invoke(ParseContentByUrl $message): ?ContentResult
    {
        try {
            $response = $this->httpClient->request(method: Request::METHOD_GET, url: $message->url);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                return null;
            }

            $html = $response->getContent();
        } catch (TransportExceptionInterface $exception) {
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

        /** @var array $tags */
        $tags = $this->bus->dispatch(new ParseTags(html: $html))->last(HandledStamp::class)?->getResult();

        /** @var array $categories */
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
