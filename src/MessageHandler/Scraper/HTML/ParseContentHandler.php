<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseContent;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\ParserInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ParseContentHandler
{
    private array $nodes;

    public function __construct(private ParserInterface $parser)
    {
        $this->nodes = [
            new Node(type: Type::HTML, selector: '.article-body'),
            new Node(type: Type::LARGEST_NESTED_CONTENT, selector: 'body'),
        ];
    }

    public function __invoke(ParseContent $message): ?string
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
