<?php

namespace PhpSpecExtension\MatcherLoader;

use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\Formatter\Presenter\PresenterInterface;
use PhpSpec\Matcher\CallbackMatcher;
use PhpSpec\Matcher\MatcherInterface;
use PhpSpec\Matcher\MatchersProviderInterface;
use PhpSpec\ServiceContainer;

use PhpSpecExtension\MatcherLoader\Runner\Maintainer\MatchersLoaderMaintainer;

class Extension implements ExtensionInterface
{
    /**
     * @param ServiceContainer $container
     */
    public function load(ServiceContainer $container)
    {
        $container->set('runner.maintainers.configuration_loaded_extensions', function (ServiceContainer $c) {
            $config = $c->getParam('matchers', []);
            $presenter = $c->get('formatter.presenter');
            $instances = $this->createInstances($config);
            $matchers = $this->resolveMatchers($instances, $presenter);

            return new MatchersLoaderMaintainer($matchers);
        });
    }

    /**
     * @param string[] $classNames
     * @return object[]
     */
    private function createInstances(array $classNames)
    {
        $mapper = function ($className) {
            $class = new \ReflectionClass($className);
            return $class->newInstance();
        };

        return array_map($mapper, $classNames);
    }

    /**
     * @param object[] $instances
     * @param PresenterInterface $presenter
     * @return MatcherInterface[]
     */
    private function resolveMatchers(array $instances, PresenterInterface $presenter)
    {
        $resolved = array();

        foreach ($instances as $instance) {
            $matchers = $this->resolveMatchersFromObject($instance, $presenter);
            $resolved = array_merge($resolved, $matchers);
        }

        return $resolved;
    }

    /**
     * @param object $instance
     * @param PresenterInterface $presenter
     * @return MatcherInterface[]
     */
    private function resolveMatchersFromObject($instance, PresenterInterface $presenter)
    {
        if ($instance instanceof MatchersProviderInterface) {
            return $this->extractMatchersFromProvider($instance, $presenter);
        }

        if ($instance instanceof MatcherInterface) {
            return [$instance];
        }

        throw new \UnexpectedValueException(
            sprintf('%s must implement MatchersProviderInterface or MatcherInterface', get_class($instance))
        );
    }

    /**
     * @param MatchersProviderInterface $provider
     * @param PresenterInterface $presenter
     * @return MatcherInterface[]
     */
    private function extractMatchersFromProvider(MatchersProviderInterface $provider, PresenterInterface $presenter)
    {
        $extracted = array();

        foreach ($provider->getMatchers() as $name => $matcher) {
            if (!($matcher instanceof MatcherInterface)) {
                $matcher = new CallbackMatcher($name, $matcher, $presenter);
            }

            array_push($extracted, $matcher);
        }

        return $extracted;
    }
}