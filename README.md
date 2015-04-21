# php-queue
PHP Class to handle concurrent running Jobs to utilize the power of multiple cores

## Background ##

After some search I was not able to find a php-queue alike class.
This code was inspired by php-resque

## Requirements ##

* PHP 5.3+
* php-pctnl
* Composer

## Getting Started ##

The easiest way to work with php-queue is when it's installed as a
Composer package inside your project. Composer isn't strictly
required, but makes life a lot easier.

If you're not familiar with Composer, please see <http://getcomposer.org/>.

1. Add php-queue to your application's composer.json.

```json
{
    // ...
    "require": {
        "jesusch/php-queue": "*"  
    },
    // ...
}
```

2. Run `composer install`.

3. If you haven't already, add the Composer autoload to your project's
   initialization file. (example)

```sh
require 'vendor/autoload.php';
```

## Jobs ##

### Queueing Jobs ###

Jobs are queued as follows:

```php

$queue = new \JobQueue\Queue();
$queue->setMaxProcs(10);

$job = new \JobQueue\Job();

$queue->appendJob($job);

$queue->waitForJobs();
```

### Defining Jobs ###

Each job should be in its own class, must extend `\JobQueue\AbstractJob` and include a public `run` method.

```php
class My_Job extends \JobQueue\AbstractJob
{
    public function run()
    {
        
        // Work work work
        echo 'some fancy job';
        $sleep = rand(1,5);
        sleep($sleep);
    }
}
```



