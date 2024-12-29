<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;

use Sofyco\Bundle\SpiderBundle\Message\Scraper\HTML\ParsePublishedTime;
use Sofyco\Spider\Parser\Builder\Node;
use Sofyco\Spider\Parser\Builder\Node\Type;
use Sofyco\Spider\Parser\ParserInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ParsePublishedTimeHandler
{
    private array $nodes;

    public function __construct(private ParserInterface $parser)
    {
        $this->nodes = [
            new Node(type: Type::ATTRIBUTE, selector: 'meta[property="article:published_time"]', attribute: 'content'),
        ];
    }

    public function __invoke(ParsePublishedTime $message): ?\DateTime
    {
        foreach ($this->nodes as $node) {
            $value = $this->parser->getResult(content: $message->html, node: $node)->current();

            if (false === empty($value)) {
                return new \DateTime($value);
            }
        }

        return null;
    }
}
