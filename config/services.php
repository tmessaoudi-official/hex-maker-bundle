<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TakiTech\HexMakerBundle\Maker\MakeCqrsCommand;
use TakiTech\HexMakerBundle\Maker\MakeCqrsQuery;
use TakiTech\HexMakerBundle\Maker\MakeDomainEntity;
use TakiTech\HexMakerBundle\Maker\MakeDomainEvent;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(MakeDomainEntity::class)
        ->tag('maker.command')
        ->autoconfigure(false)
        ->autowire(true);

    $services->set(MakeCqrsCommand::class)
        ->tag('maker.command')
        ->autoconfigure(false)
        ->autowire(true);

    $services->set(MakeCqrsQuery::class)
        ->tag('maker.command')
        ->autoconfigure(false)
        ->autowire(true);

    $services->set(MakeDomainEvent::class)
        ->tag('maker.command')
        ->autoconfigure(false)
        ->autowire(true);
};
