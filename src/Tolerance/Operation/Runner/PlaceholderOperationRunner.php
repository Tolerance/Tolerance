<?php

/*
 * This file is part of the Tolerance package.
 *
 * (c) Samuel ROZE <samuel.roze@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tolerance\Operation\Runner;

use Psr\Log\LoggerInterface;
use Tolerance\Operation\ExceptionCatcher\ThrowableCatcherVoter;
use Tolerance\Operation\ExceptionCatcher\WildcardExceptionVoter;
use Tolerance\Operation\Operation;
use Tolerance\Operation\Placeholder\PlaceholderResponseResolver;

/**
 * When an operation fails, it can be better to answer an empty response than propagating
 * the exception.
 *
 */
class PlaceholderOperationRunner implements OperationRunner
{
    /**
     * @var OperationRunner
     */
    private $decoratedRunner;

    /**
     * @var PlaceholderResponseResolver
     */
    private $placeholderResponseResolver;

    /**
     * @var ThrowableCatcherVoter
     */
    private $catcherVoter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(OperationRunner $decoratedRunner, PlaceholderResponseResolver $placeholderResponseResolver, ThrowableCatcherVoter $catcherVoter = null, LoggerInterface $logger = null)
    {
        $this->decoratedRunner = $decoratedRunner;
        $this->placeholderResponseResolver = $placeholderResponseResolver;
        $this->catcherVoter = $catcherVoter ?: new WildcardExceptionVoter();
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run(Operation $operation)
    {
        try {
            return $this->decoratedRunner->run($operation);
        } catch (\Exception $e) {
            // Handled later
        } catch (\Throwable $e) {
            // Handled later
        }

        if (!$this->catcherVoter->shouldCatchThrowable($e)) {
            throw $e;
        }

        $placeholder = $this->placeholderResponseResolver->createResponse($operation, $e);
        if (null !== $this->logger) {
            $this->logger->warning('An operation exception was replaced by a placeholder', [
                'operation' => $operation,
                'placeholder' => $placeholder,
                'exception' => $e,
                'message' => $e->getMessage(),
            ]);
        }

        return $placeholder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Operation $operation)
    {
        return $this->decoratedRunner->supports($operation);
    }
}
