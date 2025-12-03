<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\XML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\ContentResult;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\XML\ParseRSS;
use Sofyco\Spider\Context;
use Sofyco\Spider\Scraper\ScraperInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ParseRSSHandler
{
    private const string PLACEHOLDER_PAGE = '{{page}}';

    public function __construct(private ScraperInterface $scraper)
    {
    }

    public function __invoke(ParseRSS $message): iterable
    {
        yield from $this->getPaginatedContent(url: $message->url);
    }

    private function getPaginatedContent(string $url, int $page = 1): iterable
    {
        $xml = $this->getContent(url: strtr($url, [self::PLACEHOLDER_PAGE => $page]));

        if (null === $xml) {
            return;
        }

        yield from $this->getItems(xml: $xml);

        unset($xml);

        if (str_contains($url, self::PLACEHOLDER_PAGE)) {
            yield from $this->getPaginatedContent(url: $url, page: $page + 1);
        }
    }

    private function getItems(string $xml): iterable
    {
        $data = new \SimpleXMLElement(data: $xml, options: \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_ERR_WARNING);

        foreach ($data->channel?->item as $item) {
            $url = $this->getElementText(element: $item->link);

            if (empty($url)) {
                continue;
            }

            $tags = $categories = [];

            foreach ($item->keyword ?? [] as $keyword) {
                if ($keyword = $this->getElementText(element: $keyword)) {
                    $tags[] = $keyword;
                }
            }

            foreach ($item->category ?? [] as $category) {
                if ($category = $this->getElementText(element: $category)) {
                    $categories[] = $category;
                }
            }

            if (null !== $publishedAt = $this->getElementText(element: $item->pubDate)) {
                $publishedAt = new \DateTime($publishedAt);
            }

            yield new ContentResult(
                url: $url,
                image: $this->getElementAttribute(element: $item->enclosure, attribute: 'url'),
                title: $this->getElementText(element: $item->title),
                description: $this->getElementText(element: $item->description),
                content: $this->getElementText(element: $item->content),
                tags: $tags,
                categories: $categories,
                publishedAt: $publishedAt
            );
        }
    }

    private function getContent(string $url): ?string
    {
        try {
            return $this->getTransformedContent(content: $this->scraper->getResult(context: new Context(url: $url)));
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function getTransformedContent(string $content): string
    {
        $content = str_replace('content:encoded', 'content', $content);
        $content = str_replace('dc:creator', 'author', $content);

        return (string) preg_replace('/(\r\n|\r|\n){2,}/', "\n\n", $content);
    }

    private function getElementText(\SimpleXMLElement $element): ?string
    {
        return (string) $element ?: null;
    }

    private function getElementAttribute(\SimpleXMLElement $element, string $attribute): ?string
    {
        $attributes = $element->attributes();

        if ($attributes?->count() && $attributes->{$attribute} && is_string($attributes->{$attribute})) {
            return (string) $attributes->{$attribute};
        }

        return null;
    }
}
