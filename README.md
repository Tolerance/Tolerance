# FaultTolerance

**This is an early draft and is not supposed to be used as it now, I may break things :)**

A set of classes that helps to setup fault-tolerant applications in PHP.

- [Operations](#operations)
- [Operation runners](#operation-runners)
- [Wait strategies](#wait-strategies)
- [Waiters](#waiters)

## Operation

An operation is an atomic piece of processing. This is for instance an API call to an third-party service.
You can defines an operation by using the callback method, like this:

```php
use FaultTolerance\Operation\Callback;

$operation = new Callback(function() use ($client) {
    return $client->get('/foo');
});
```

## Operation runners

In order to run the different applications, you can use and combine different operation runners. The list bellow
describes the different operation runners available:

- [`SimpleOperationRunner`](#simpleoperationrunner) that simply calls the operation
- [`BufferedOperationRunner`](#bufferedoperationrunner) will buffer operations and try to run them.
- [`RetryOperationRunner`](#retryoperationrunner) will retry the operation until it is successful.

### SimpleOperationRunner

That's the simplest operation runner ever. It calls `run()` on the operation.

```php
use FaultTolerance\OperationRunner\SimpleOperationRunner;

$runner = new SimpleOperationRunner();
$runner->run($operation);
```

### BufferedOperationRunner

The idea of this runner is to try running the operations but if not possible, then it'll buffer it and then will try to
run it before the operation you'll add an other time.

```php
use FaultTolerance\OperationBuffer\InMemoryOperationBuffer;
use FaultTolerance\OperationRunner\SimpleOperationRunner;
use FaultTolerance\OperationRunner\BufferedOperationRunner;

$buffer = new InMemoryOperationBuffer();
$runner = new BufferedOperationRunner(new SimpleOperationRunner(), $buffer);
```

Then, you can try to run an operation:
```php
// Let's say this operation will fail by throwing an exception
$runner->run($operation);
```

If this operation fails (ie throws an exception) then the runner will keep it in the buffer. So when you'll try to run
another task, it'll **first** attempt to run the operation in the buffer.
```php
$runner->run($secondOperation);

// That will actually run the first one first,
// and then the second one
```

### RetryOperationRunner

This runner will retry to run the operation until it is successful or the wait strategy decide to fail. Again, this
should be used as decorator as an existing operation runner.

```php
use FaultTolerance\OperationRunner\SimpleOperationRunner;
use FaultTolerance\OperationRunner\RetryOperationRunner;
use FaultTolerance\Waiter\SleepWaiter;
use FaultTolerance\WaitStrategy\Exponential;

// This example will run the operation until it is successful
// and will wait an exponential amount of time between the calls.

$runner = new SimpleOperationRunner();
$waitStrategy = new Exponential(new SleepWaiter(), 1);
$runner = new RetryOperationRunner($runner, $waitStrategy);

$runner->run($operation);
```

**Note:** you should decorate your `WaitingStrategy` by the [`Max` strategy](#max) in order to prevent infinite or
 extremely long loops.

## Waiters

These are actual implementations of wait. The only for now is the `SleepWaiter` that calls `sleep` basically.

```php
use FaultTolerance\Waiter\SleepWaiter;

$waiter = new SleepWaiter();

// That will sleep for 500 milliseconds
$waiter->sleep(0.5);
```

## Wait strategies

Many different wait strategies can be used in order to retry things or simply wait for a circuit to be closed, etc...

### Exponential

Each time you will call `wait` on the object, it'll wait an exponential number of seconds, based on your exponent.

```php
use FaultTolerance\WaitStrategy\Exponential;
use FaultTolerance\Waiter\SleepWaiter;

// We use an initial exponent of 1
$waiter = new SleepWaiter();
$waitStrategy = new Exponential($waiter, 1);

// Waits exp(1)
$waitStrategy->wait();

// Waits exp(2)
$waitStrategy->wait();

// Waits exp(3)
$waitStrategy->wait();

// ...
```

### Max

This decoration strategy defines a maximum amount of waits.

```php
// Wait for a maximum amount of 10 times
$waitingStrategy = new Max($waitingStrategy, 10);
```
