<?php

namespace Tolerance\Bridge\Symfony\Bundle\AppBundle\ThirdParty;

class StepByStepHookApiClient implements ApiClient
{
    /**
     * @var callable[]
     */
    private $hooks = [];

    /**
     * @var int
     */
    private $counter = 0;

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        if (!array_key_exists($this->counter, $this->hooks)) {
            throw new ApiException(sprintf('No hook configured for step %d', $this->counter));
        }

        $hook = $this->hooks[$this->counter++];

        return $hook();
    }

    /**
     * @param int $step
     * @param callable $hook
     */
    public function registerStepHook($step, callable $hook)
    {
        $this->hooks[$step] = $hook;
    }
}
