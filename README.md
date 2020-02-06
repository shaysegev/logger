# Logger library (Not another one!)

Not to worry, this is an MVP for a simple yet efficient logging library. 

Following the [PSR-3 Logging standard](https://www.php-fig.org/psr/psr-3/) and inspired by [Monolog](https://github.com/Seldaek/monolog), the library comes with a handy functionality to get you started, though it's extremely unopinionated (if you want it to be), and is adjustable to suit various technical needs.

This means that with a few lines of code you can have a logger that is capturing and storing all PHP errors and exceptions to the path of your choice, as well as any custom logging you might want to do across your application. And with a few more lines, you can override or add any customisations you could think of, which essentially hooks into the library internal processing.

The documentation is assuming you are familiar with logging in PHP, and with the PSR-3 standard.

## Installation

You can install the latest version with composer:

    composer require shays/logger

## Get Started

The Logger library uses the builder design pattern, which helps providing the flexibility and extendability that could be used by the library, which the examples would soon help clarify.

To get started with the most basic and simple logger: 

### The Basic Example

```php
use Shays\Logger;
use Shays\Logger\Stream\FileStream;

$log = (new Logger('MyApp'))->addStream(new FileStream('application.log'));
$log->notice('Log message');
```

And that's it. In the example we've created a logger instance with the application's name, that stores any PHP notices/warnings/errors and exceptions occurring in the application to the path of your choice, in a JSON format which includes the error message, the log severity, and a timestamp. Any exceptions would also be added in details.

### Timezones

You might find that you need to specify the timezone in which the log timestamps will be generated. You can do that with the `setTimezone` method, and passing a `DateTimeZone` instance:

```php
use DateTimeZone;
use Shays\Logger;
use Shays\Logger\Stream\FileStream;

$log = (new Logger('MyApp'))
    ->addStream(new FileStream('application.log'))
    ->setTimezone(new DateTimeZone('Europe/London'));

$log->notice('Log message');
```

### Additional Context

You might want to include additional data in the log, such as environment or user related information. You can specify them with `addContext`, which accepts an associative array of the information you would like to add:

```php
use Shays\Logger;
use Shays\Logger\Stream\FileStream;

$log = (new Logger('MyApp'))
    ->addStream(new FileStream('application.log'))
    ->addContext([
        'environment' => 'local',
    ]);

$log->notice('Log message');
```

The context added on the logger instance level would be added to each individual log entry.

#### Adding Context To Logs

You can add context to individual logs, such as added relevant information, exceptions objects, etc.

```php
// Simple log context added
$log->notice('User not found', ['id' => 123]);
```

##### Capturing Exceptions

Exceptions can be added as context, which would then be parsed and extracted by the logger to provide the relevant details.  

```php
// Adding captured exception information
try {
    // Trying an API connection
} catch (\Throwable $e) {
    $log->error('API connection failed', ['exception' => $e]);   
}
```


### Logging Errors Only
 
 As an example, it's possible to set a second argument to the `addStream` method to tell the file logger which is the least severe log level that you are interested in logging to the file (In this case, all log levels from errors up to the most severe errors would be stored, skipping notices, warnings, etc).
 
 ```php
 use Shays\Logger;
 use Shays\Logger\LogLevel;
 use Shays\Logger\Stream\FileStream;
 
 $log = (new Logger('MyApp'))
     ->addStream(new FileStream('application.log', LogLevel::ERROR));

// This won't be logged to the file
$log->notice('Log message');
 ```

## Advanced Usages

The logger library comes with powerful extended abilities. Pretty much everything is allowed to be customised, you can even create your own `Log` object which is used by the handlers and log streams, and rename the fields and serialize the data the way you wish to, as long as you follow [its interface](src/Logger/LogInterface.php) (Examples below).

### Custom Log Levels

It's possible to add custom log levels dynamically via the `addLogLevel` method, which takes the log level number and the name.

 ```php
 use Shays\Logger;
 use Shays\Logger\Stream\FileStream;
 
 $log = (new Logger('MyApp'))
     ->addStream(new FileStream('application.log'))
     ->addLogLevel(50, 'PROGRESS');

// This is now possible to call it directly 
$log->progress('Log message');
 ```

*TODO: There's a gotcha with passing the least severe level to the file stream in this case, and it has to be reworked.*

### Custom Handlers

You can add your custom handlers to the logger instance, which would be called whenever a log is created. The custom handlers should all follow the [LogHandlerInterface](src/Logger/Handlers/LogHandlerInterface.php) which is consisted of two methods, `handle` and `shouldHandle`.

 ```php
// MyCustomHandler.php

use Shays\Logger\Handlers\LogHandlerInterface;
use Shays\Logger\LogInterface;
use Shays\Logger\LogLevel;

class MyCustomHandler implements LogHandlerInterface
{
    public function handle(LogInterface $log): void
    {
        // Do anything with the log object 
        // (e.g. get the message, context, etc)
    }

    public function shouldHandle(LogInterface $log): bool
    {
        // e.g. handle notices and the more severe logs 
	    return $log->getLevel() >= LogLevel::NOTICE;
    }
}
 ```

And in the app:
```php
 use Shays\Logger;
 
 $log = (new Logger('MyApp'))
     ->addHandler(new MyCustomHandler());
```

Using the custom handlers you can send messages to Slack or use any third party integrations that helps monitoring the health of your application.

### Custom Streamers

Custom streamers are similar to custom handlers, only they are intended for streaming of serialized log data. The custom streamers should follow the [StreamInterface](src/Logger/Stream/StreamInterface.php), which is consisted of two methods: `write` and `shouldWrite`.

In the examples above, we've used the library's `FileStream` class to store logs to a particular file, though we can use our own Stream class to create our unique functionality:

 ```php
// DatabaseStreamer.php

use Shays\Logger\LogInterface;
use Shays\Logger\Stream\StreamInterface;

class DatabaseStream implements StreamInterface
{
	/** @var int Lowest log level for handling log */
	private $lowestLevel;

	public function __construct(int $lowestLevel)
	{
        $this->lowestlevel = $lowestLevel;
	}

	public function write(string $log): void
	{
	    // Write the serialized log to the database or system file
	}

	public function shouldWrite(LogInterface $log): bool
	{
	    return $log->getLevel() >= $this->lowestLevel;
	}
}
```

And in the app:
```
use DatabaseStream;
use Shays\Logger;
 
 $log = (new Logger('MyApp'))
     ->addStream(new DatabaseStream(LogLevel::ERROR));
 ```

#### Streaming Serializers

By default the log is being serialized to JSON when passed to the `write` method, though more serializers can be added, and can be passed down the Logger instance, as long as they follow the [SerializerInterface](src/Logger/Serializer/SerializerInterface.php).

```php
// XmlSerializer.php

use Shays\Logger\LogInterface;
use Shays\Logger\Serializer\SerializerInterface;

class XmlSerializer implements SerializerInterface
{
    public function serialize(LogInterface $log)
    {
        // Serialize the data to XML format which would
        // be then passed to the streaming classes.

        // you can use $log->serialize() method to serialize the
        // log to an array with all the relevant log information
    }
}
```

And in the app:
```php
use XmlSerializer;
use Shays\Logger;
 
 $log = (new Logger('MyApp', new XmlSerializer()))
     ->addStream(new DatabaseStream(LogLevel::ERROR));
```

And all the custom streamers would now be handled with XML.

### Custom Log Object

The custom log object provides maximum flexibility over the data being passed down to the handlers and streamers. By default the library's [Log object](src/Logger/Log.php) has the most minimal and sensible data that makes each log a useful entry, though you can create your own log object and have full control over the the data being logged.

TODO example 