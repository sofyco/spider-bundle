<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\XML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\ContentResult;
use Sofyco\Bundle\SpiderBundle\Message\Scraper\XML\ParseSitemap;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsMessageHandler]
final readonly class ParseSitemapHandler
{
    private const string PLACEHOLDER_YEAR = '{{year}}';
    private const string PLACEHOLDER_MONTH = '{{month}}';
    private const string PLACEHOLDER_DAY = '{{day}}';

    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function __invoke(ParseSitemap $message): iterable
    {
        $content = $this->getContent(url: $message->url);

        if (null === $content) {
            return;
        }

        $xml = new \SimpleXMLElement(data: $content, options: \LIBXML_NOBLANKS | \LIBXML_NOCDATA | \LIBXML_ERR_WARNING);

        foreach ($xml->url as $item) {
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
            $response = $this->httpClient->request(method: Request::METHOD_GET, url: $url);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                return null;
            }

            return $response->getContent();
        } catch (TransportExceptionInterface $exception) {
            return null;
        }
    }
}
