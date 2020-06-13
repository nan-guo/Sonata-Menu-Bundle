<?php

namespace Prodigious\Sonata\MenuBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Prodigious\Sonata\MenuBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProdigiousSonataMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DoctrineResolveTargetEntityPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1000);
    }
}
