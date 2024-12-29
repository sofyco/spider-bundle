<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParseTags;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\ParserInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ParseTagsHandler
{
    private array $nodes;

    public function __construct(private ParserInterface $parser)
    {
        $this->nodes = [
            new Node(type: Type::ATTRIBUTE, selector: 'meta[property="article:tag"]', attribute: 'content'),
            new Node(type: Type::TEXT, selector: '.entry-tags a'),
            new Node(type: Type::TEXT, selector: '.tags-links a'),
        ];
    }

    public function __invoke(ParseTags $message): array
    {
        foreach ($this->nodes as $node) {
            $value = iterator_to_array($this->parser->getResult(content: $message->html, node: $node));

            if (false === empty($value)) {
                return $value;
            }
        }

        return [];
    }
}
