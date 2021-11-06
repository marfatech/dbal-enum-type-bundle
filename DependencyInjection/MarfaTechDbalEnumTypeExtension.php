<?php

declare(strict_types=1);

/*
 * This file is part of the DbalEnumTypeBundle package.
 *
 * (c) MarfaTech <https://marfa-tech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarfaTech\Bundle\DbalEnumTypeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use MarfaTech\Component\DbalEnumType\Type\AbstractEnumType;

class MarfaTechDbalEnumTypeExtension extends Extension implements PrependExtensionInterface
{
    public const PARAMETER_SOURCES = 'marfa_tech_dbal_enum_type.source_directories';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $projectDir = $container->getParameter('kernel.project_dir');

        $configuration = new Configuration($projectDir);
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(self::PARAMETER_SOURCES, $config['source_directories'] ?? []);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'driver_class' => 'MarfaTech\Component\DbalEnumType\Driver\PDOMySql\EnumAwareDriver',
                'types' => $this->getDbalEnumTypeClasses($container),
            ]
        ]);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getDbalEnumTypeClasses(ContainerBuilder $container): array
    {
        $finder = $this->getFinder($container);

        foreach ($finder as $splFileInfo) {
            include_once($splFileInfo->getPathname());
        }

        $declaredClassList = get_declared_classes();
        $sourceClasses = [];

        foreach ($declaredClassList as $className) {
            if (!is_subclass_of($className, AbstractEnumType::class)) {
                continue;
            }

            /** @var AbstractEnumType $className */
            $sourceClasses[$className::getTypeName()] = $className;
        }

        return array_unique($sourceClasses);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return Finder
     */
    private function getFinder(ContainerBuilder $container): Finder
    {
        $marfaTechDbalEnumTypeConfigs = $container->getExtensionConfig('marfa_tech_dbal_enum_type');

        $sourceList = [];

        foreach ($marfaTechDbalEnumTypeConfigs as $marfaTechDbalEnumTypeConfig) {
            if (!isset($marfaTechDbalEnumTypeConfig['source_directories'])) {
                continue;
            }

            $sourceList += $marfaTechDbalEnumTypeConfig['source_directories'];
        }

        $projectDir = $container->getParameter('kernel.project_dir');

        $finder = new Finder();
        $finder->files()->name('*.php');

        foreach ($sourceList as $directoryOrFile) {
            $finder->in($projectDir . DIRECTORY_SEPARATOR . $directoryOrFile);
        }

        return $finder;
    }
}
