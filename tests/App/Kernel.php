<?php declare(strict_types=1);

namespace Sofyco\Bundle\SpiderBundle\Tests\App;

use Sofyco\Bundle\SpiderBundle\SpiderBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new SpiderBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'messenger' => [
                'transports' => [
                    'sync' => 'in-memory://',
                ],
            ],
        ]);

        $container->services()->set(Service\ExampleService::class, Service\ExampleService::class)->autowire()->public();
    }
}
