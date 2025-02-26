<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\DependencyInjection;

use Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\HTML;
use Sofyco\Bundle\SpiderBundle\MessageHandler\Scraper\XML;
use Sofyco\Spider\Crawler\{Crawler, CrawlerInterface};
use Sofyco\Spider\Parser\{Parser, ParserInterface};
use Sofyco\Spider\Scraper\{Scraper, ScraperInterface};
use Symfony\Component\DependencyInjection\{ContainerBuilder, Definition, Extension\Extension};

final class SpiderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $parser = new Definition(Parser::class);
        $container->setDefinition(ParserInterface::class, $parser);

        $scraper = new Definition(Scraper::class);
        $scraper->setAutowired(true);
        $container->setDefinition(ScraperInterface::class, $scraper);

        $crawler = new Definition(Crawler::class);
        $crawler->setAutowired(true);
        $container->setDefinition(CrawlerInterface::class, $crawler);

        foreach ($this->getMessageHandlers() as $messageHandlerClassName) {
            $messageHandler = new Definition($messageHandlerClassName);
            $messageHandler->setAutowired(true);
            $messageHandler->setAutoconfigured(true);
            $container->setDefinition($messageHandlerClassName, $messageHandler);
        }
    }

    private function getMessageHandlers(): array
    {
        return [
            XML\ParseRSSHandler::class,
            XML\ParseSitemapHandler::class,
            HTML\ParseDeepLinksHandler::class,
            HTML\ParseCategoriesHandler::class,
            HTML\ParseContentByUrlHandler::class,
            HTML\ParseContentHandler::class,
            HTML\ParseDescriptionHandler::class,
            HTML\ParseImageHandler::class,
            HTML\ParsePublishedTimeHandler::class,
            HTML\ParseTagsHandler::class,
            HTML\ParseTitleHandler::class,
        ];
    }
}
