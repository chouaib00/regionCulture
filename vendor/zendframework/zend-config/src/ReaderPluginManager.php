<?php
/**
 * @see       https://github.com/zendframework/zend-config for the canonical source repository
 * @copyright Copyright (c) 2005-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/zendframework/zend-config/blob/master/LICENSE.md New BSD License
 */

namespace Zend\Config;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Factory\InvokableFactory;

class ReaderPluginManager extends AbstractPluginManager
{
    protected $instanceOf = Reader\ReaderInterface::class;

    protected $aliases = [
        'ini'            => Reader\Ini::class,
        'Ini'            => Reader\Ini::class,
        'json'           => Reader\Json::class,
        'Json'           => Reader\Json::class,
        'xml'            => Reader\Xml::class,
        'Xml'            => Reader\Xml::class,
        'yaml'           => Reader\Yaml::class,
        'Yaml'           => Reader\Yaml::class,
        'javaproperties' => Reader\JavaProperties::class,
        'javaProperties' => Reader\JavaProperties::class,
        'JavaProperties' => Reader\JavaProperties::class,
    ];

    protected $factories = [
        Reader\Ini::class            => InvokableFactory::class,
        Reader\Json::class           => InvokableFactory::class,
        Reader\Xml::class            => InvokableFactory::class,
        Reader\Yaml::class           => InvokableFactory::class,
        Reader\JavaProperties::class => InvokableFactory::class,
        // Legacy (v2) due to alias resolution; canonical form of resolved
        // alias is used to look up the factory, while the non-normalized
        // resolved alias is used as the requested name passed to the factory.
        'zendconfigreaderini'            => InvokableFactory::class,
        'zendconfigreaderjson'           => InvokableFactory::class,
        'zendconfigreaderxml'            => InvokableFactory::class,
        'zendconfigreaderyaml'           => InvokableFactory::class,
        'zendconfigreaderjavaproperties' => InvokableFactory::class,
    ];

    /**
     * Validate the plugin is of the expected type (v3).
     *
     * Validates against `$instanceOf`.
     *
     * @param mixed $instance
     * @throws InvalidServiceException
     */
    public function validate($instance)
    {
        if (! $instance instanceof $this->instanceOf) {
            throw new InvalidServiceException(sprintf(
                '%s can only create instances of %s; %s is invalid',
                get_class($this),
                $this->instanceOf,
                (is_object($instance) ? get_class($instance) : gettype($instance))
            ));
        }
    }

    /**
     * Validate the plugin is of the expected type (v2).
     *
     * Proxies to `validate()`.
     *
     * @param mixed $instance
     * @throws Exception\InvalidArgumentException
     */
    public function validatePlugin($instance)
    {
        try {
            $this->validate($instance);
        } catch (InvalidServiceException $e) {
            throw new Exception\InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function __construct(ContainerInterface $container, array $config = [])
    {
        $config = array_merge_recursive(['aliases' => $this->aliases], $config);
        parent::__construct($container, $config);
    }
}
