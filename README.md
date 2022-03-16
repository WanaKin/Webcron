# Webcron For Laravel

Setting up a queue worker and scheduler for Laravel is not always possible, especially when designing a self-hosted web application. This package provides a solution to this problem by introducing two routes (`/cron` and `/worker`) which can replace (or work alongside) Laravel's built-in queue worker and scheduler.

**Please note this package is still in beta, and we do not recommend using it in a production environment yet.**

### A quick note about the scheduler
Unlike Laravel's built-in scheduler, this package will run jobs that missed their exact scheduled time. How it works in a nutshell is on each request (the package tries to use locking so it's not run on *every* request), it checks the previous scheduled date. If the job has been dispatched since then, nothing happens. If not, the job is dispatched and the current timestamp is stored.

The downside of this approach is that there's no guarantee when jobs will run, and the scheduler is pretty dumb in that it might dispatch more of the same job even if the last one did not get run yet, or is still running. To solve this, we recommend using the `WithoutOverlapping` job middleware, and to configure [unique jobs](https://laravel.com/docs/9.x/queues#unique-jobs).

## Installation
If you're using this before we've set it up on Packagist, you'll need to [update your repositories in composer](https://getcomposer.org/doc/05-repositories.md#loading-a-package-from-a-vcs-repository). After that, require the package:

```shell
composer require wanakin/webcron
```

The last step is to publish the config file and run migrations:

```shell
php artsian vendor:publish --tag=webcron-config
php artsian migrate
```

## Usage
### Scheduler
Using the scheduler is a bit different from Laravel's built-in one. To use the webcron scheduler, we recommend using the `WebcronScheduler` facade (`WanaKin\Webcron\Facades\WebcronScheduler`) in the `boot` method of a service provider:

```php
WebcronScheduler::schedule('0 0 * * *', new MyJob($a, $b, $c));
```

The first argument to the `schedule` command is the cron expression, and the second one is the job instance.

#### Using Laravel Actions?
If you're using the [Laravel Actions package](https://laravelactions.com/), you can use the `makeJob` method to schedule actions:

```php
WebcronScheduler::schedule('0 0 * * *', MyAction::makeJob($a, $b, $c));
```

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

The axios/ajax method is ideal because it won't affect the initial page load speed.

Alternatively, you can use [terminable middleware](https://laravel.com/docs/9.x/middleware#terminable-middleware), and make a web request to your server (kind of like how WordPress handles their cron implementation). This has the advantage of creating new requests to handle the scheduler and worker, and not needing to add any JavaScript code. However, unless you're using FastCGI, it will likely slow down your requests. To reduce slowdowns, you may want to try setting a low timeout, but that may not work as expected (for example, Laravel's built-in HTTP client will accept a minimum timeout of 1 second). If you would like to go this route anyway, you might want to try cURL with `CURLOPT_NOSIGNAL` and `CURLOPT_TIMEOUT_MS` ([relevant Stack Overflow answer](https://stackoverflow.com/a/20462702/8292439)).

## Testing
To run the tests, install all dev dependencies and run `composer test`:

```shell
composer install
composer test
```