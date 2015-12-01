# FaultTolerance

**This is an early draft and is not supposed to be used as it now, I may break things :)**

A set of classes that helps to setup fault-tolerant applications in PHP.

- [Operations](#operations)
- [Operation Runners](#operation-runners)

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

### SimpleOperationRunner

That's the simplest operation runner ever. It calls `run()` on the operation.

```php
use FaultTolerance\OperationRunner\SimpleOperationRunner;

$runner = new SimpleOperationRunner();
$runner->run($operation);
```

