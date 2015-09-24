<?php

namespace PhpSpecExtension\MatcherLoader\Runner\Maintainer;

use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Matcher\MatcherInterface;
use PhpSpec\Runner\CollaboratorManager;
use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\SpecificationInterface;

class MatchersLoaderMaintainer implements MaintainerInterface
{
    /**
     * @var MatcherInterface[]
     */
    private $matchers = array();

    /**
     * @param MatcherInterface[] $matchers
     */
    public function __construct(array $matchers)
    {
        $this->matchers = $matchers;
    }

    /**
     * @param ExampleNode $example
     * @return bool
     */
    public function supports(ExampleNode $example)
    {
        return true;
    }

    /**
     * @param ExampleNode $example
     * @param SpecificationInterface $context
     * @param MatcherManager $matchers
     * @param CollaboratorManager $collaborators
     */
    public function prepare(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
        foreach ($this->matchers as $name => $matcher) {
            $matchers->add($matcher);
        }
    }

    /**
     * @param ExampleNode $example
     * @param SpecificationInterface $context
     * @param MatcherManager $matchers
     * @param CollaboratorManager $collaborators
     */
    public function teardown(
        ExampleNode $example,
        SpecificationInterface $context,
        MatcherManager $matchers,
        CollaboratorManager $collaborators
    ) {
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return 10;
    }
}