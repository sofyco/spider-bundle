<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\XML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\ContentResult;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\XML\ParseSitemap;
use Sofyco\Spider\Context;
use Sofyco\Spider\Scraper\ScraperInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ParseSitemapHandler
{
    private const string PLACEHOLDER_YEAR = '{{year}}';
    private const string PLACEHOLDER_MONTH = '{{month}}';
    private const string PLACEHOLDER_DAY = '{{day}}';

    public function __construct(private ScraperInterface $scraper)
    {
    }

    public function __invoke(ParseSitemap $message): iterable
    {
        $content = $this->getContent(url: $message->url);

        if (null === $content) {
            return;
        }

        try {
            $xml = new \SimpleXMLElement(data: $content, options: \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_ERR_WARNING);
        } catch (\Throwable $exception) {
            return;
        }

        $items = $xml->url ?? [];

        foreach ($items as $item) {
            $url = (string) $item->loc;

            if (empty($url)) {
                continue;
            }

            yield new ContentResult(url: $url);
        }
    }

    private function getContent(string $url): ?string
    {
        $url = strtr($url, [
            self::PLACEHOLDER_YEAR => date('Y'),
            self::PLACEHOLDER_MONTH => date('m'),
            self::PLACEHOLDER_DAY => date('d'),
        ]);

        try {
            return $this->scraper->getResult(context: new Context(url: $url));
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
