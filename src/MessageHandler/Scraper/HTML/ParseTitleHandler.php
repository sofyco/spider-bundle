<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseTitle;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\ParserInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ParseTitleHandler
{
    private array $nodes;

    public function __construct(private ParserInterface $parser)
    {
        $this->nodes = [
            new Node(type: Type::ATTRIBUTE, selector: 'meta[property="og:title"]', attribute: 'content'),
            new Node(type: Type::ATTRIBUTE, selector: 'meta[name="twitter:title"]', attribute: 'content'),
            new Node(type: Type::TEXT, selector: 'title'),
            new Node(type: Type::TEXT, selector: 'h1'),
        ];
    }

    public function __invoke(ParseTitle $message): ?string
    {
        foreach ($this->nodes as $node) {
            $value = $this->parser->getResult(content: $message->html, node: $node)->current();

            if (false === empty($value)) {
                return $value;
            }
        }

        return null;
    }
}
