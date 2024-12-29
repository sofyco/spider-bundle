<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseDescription;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\ParserInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ParseDescriptionHandler
{
    private array $nodes;

    public function __construct(private ParserInterface $parser)
    {
        $this->nodes = [
            new Node(type: Type::ATTRIBUTE, selector: 'meta[property="og:description"]', attribute: 'content'),
            new Node(type: Type::ATTRIBUTE, selector: 'meta[name="twitter:description"]', attribute: 'content'),
            new Node(type: Type::ATTRIBUTE, selector: 'meta[name="description"]', attribute: 'content'),
        ];
    }

    public function __invoke(ParseDescription $message): ?string
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
