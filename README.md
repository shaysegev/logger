# Logger library (Not another one!)

Not to worry, this is an MVP for a simple yet efficient logging library. 

Following the [PSR-3 Logging standard](https://www.php-fig.org/psr/psr-3/) and inspired by [Monolog](https://github.com/Seldaek/monolog), the library comes with a handy functionality to get you started though it's extremely unopinionated (if you want it to be), and is adjustable to suit various technical needs.

That means that with a few lines of code you can have a logger that is capturing and storing all PHP errors and exceptions to the path of your choice, as well as any custom logging you might want to do across your application. And with a few more lines, you can override or add any customisations you could think of, which essentially hooks into the library internal processing.

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
use Shays\Stream\FileStream;

$log = (new Logger('MyApp'))->addStream(new FileStream('application.log'));
```

And that's it. In the example we've created a logger instance with the application's name, that stores any PHP notices/warnings/errors and exceptions occurring in the application to the path of your choice, in a JSON format which includes the error message, the log severity, and a timestamp. Any exceptions would also be added in details.

### Timezones

You might find that you need to specify the timezone in which the log timestamps will be generated. You can do that with the `setTimezone` method, and passing a `DateTimeZone` instance:

```php
use DateTimeZone;
use Shays\Logger;
use Shays\Stream\FileStream;

$log = (new Logger('MyApp'))
    ->addStream(new FileStream('application.log'))
    ->setTimezone(new DateTimeZone('Europe/London'));
```

### Additional Context

You might want to include additional data in the log, such as environment or user related information. You can specify them with `addContext`, which accepts an associative array of the information you would like to add:

```php
use Shays\Logger;
use Shays\Stream\FileStream;

$log = (new Logger('MyApp'))
    ->addStream(new FileStream('application.log'))
    ->addContext([
        'environment' => 'local',
    ]);
```

The context added on the logger instance level would be added to each individual log entry.

 ### Logging Errors Only
 
 As an example, it's possible to set a second argument to the `addStream` method to tell the file logger which is the least severe log level that you are interested in logging to the file (In this case, all log levels from errors up to the most severe errors would be stored, skipping notices, warnings, etc).
 
 ```php
 use Shays\Logger;
 use Shays\Logger\LogLevel;
 use Shays\Stream\FileStream;
 
 $log = (new Logger('MyApp'))
     ->addStream(new FileStream('application.log', LogLevel::ERROR));
 ```

## Advanced Usages

The logger library comes with powerful extended abilities. Pretty much everything is allowed to be customised, you can even create your own `Log` object which is used by the handlers and log streams, and rename the fields and serialize it the way you wish to, as long as you follow its interface (Examples below).

... todo