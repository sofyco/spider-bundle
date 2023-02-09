<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\DependencyInjection;

use Sofyco\Spider\Crawler\Crawler;
use Sofyco\Spider\Crawler\CrawlerInterface;
use Sofyco\Spider\Loader\HttpClientLoader;
use Sofyco\Spider\Loader\LoaderInterface;
use Sofyco\Spider\Parser\Parser;
use Sofyco\Spider\Parser\ParserInterface;
use Sofyco\Spider\Scraper\Scraper;
use Sofyco\Spider\Scraper\ScraperInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class SpiderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Definition(HttpClientLoader::class);
        $loader->setAutowired(true);
        $container->setDefinition(LoaderInterface::class, $loader);

        $parser = new Definition(Parser::class);
        $container->setDefinition(ParserInterface::class, $parser);

        $scraper = new Definition(Scraper::class);
        $scraper->setAutowired(true);
        $container->setDefinition(ScraperInterface::class, $scraper);

        $crawler = new Definition(Crawler::class);
        $crawler->setAutowired(true);
        $container->setDefinition(CrawlerInterface::class, $crawler);
    }
}
