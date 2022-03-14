# Webcron

Setting up a queue worker and scheduler for Laravel is not always possible, especially when designing a self-hosted web application. This package provides a solution to this problem by introducing two routes (`/cron` and `/worker`) which can replace (or work alongside) Laravel's built-in queue worker and scheduler.

**Please note this package is still in beta, and we do not recommend using it in a production environment yet.**

### A quick note about the scheduler
Unlike Laravel's built-in scheduler, this package will run missed jobs. How it works in a nutshell is on each request (the package tries to use locking so it's not run on *every* request) it checks the last run date. If the job has been dispatched since then, nothing happens. If not, the job is dispatched and the current timestamp is stored.

The downside of this approach is that there's no guarantee when jobs will run, and the scheduler is pretty dumb in that it might dispatch more of the same job even if the last one did not get run yet, or is still running. To solve this, we recommend using the `WithoutOverlapping` job middleware, and to make any scheduled jobs not break if they run twice. The easiest way of doing the latter is to store the timestamp when the job completes in the cache, for example:

```php
public function handle()
{
    if (Cache::get(static::class . ',last_run_timestamp', now()->subYear())->add('1 hour')->gt(now())) {
        return;
    }
    
    // Do some stuff
    
    Cache::put(static::class . ',last_run_timestamp', now());
}
```

In this example, the job will only run if another instance hasn't completed in the past hour. The `now()->subYear()` default is there to return a carbon instance of a date that will never be more recent than an hour (or however often the job should run).

## Installation
If you're using this before we've set it up on Packagist, you'll need to [set up a repository](https://getcomposer.org/doc/05-repositories.md). After that, require the package:

```shell
composer require wanakin/webcron
```

The last step is to run migrations and publish the config file:

```shell
php artsian migrate
php artsian vendor:publish --tag=webcron-config
```

## Usage
### Scheduler
Using the scheduler is a bit different from Laravel's built-in one. To use the webcron scheduler, we recommend using the `WebcronScheduler` facade (`WanaKin\Webcron\Facades\WebcronScheduler`) in the `boot` method of a service provider:

```php
WebcronScheduler::schedule('0 0 * * *', new MyJob());
```

The first argument to the `schedule` command is the cron expression, and the second one is the job.

### Worker
The worker requires no configuration.

### Frontend
In order to work, the endpoints for `cron` and `worker` must be called when someone visits your site. One way to do this is by using `axios` to make a request once the page loads:

```html
<script>
(function () {
    axios.get("/worker");
    axios.get("/cron");
})();
</script>
```

## Testing
To run the tests, install all dev dependencies and run `composer test`:

```shell
composer install
composer test
```