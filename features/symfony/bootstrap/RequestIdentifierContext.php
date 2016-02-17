<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Tolerance\MessageProfile\Storage\InMemoryStorage;

class RequestIdentifierContext implements Context, SnippetAcceptingContext
{
    /**
     * @var InMemoryStorage
     */
    private $inMemoryStorage;

    /**
     * @param InMemoryStorage $inMemoryStorage
     */
    public function __construct(InMemoryStorage $inMemoryStorage)
    {
        $this->inMemoryStorage = $inMemoryStorage;
    }

    /**
     * @Then a request profile should have been stored
     */
    public function aRequestProfileShouldHaveBeenStored()
    {
        $numberOfProfiles = count($this->inMemoryStorage->getProfiles());

        if (0 === $numberOfProfiles) {
            throw new \RuntimeException('Found 0 profiles');
        }
    }
}
