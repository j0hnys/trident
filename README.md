# trident
A laravel code generator for developing applications following Domain Driven Design (DDD) principles

At the moment this package is for demonstration purposes only. Alpha software at best.


after `php artisan trident:install` add 

```
App\Providers\TridentAuthServiceProvider::class,
App\Providers\TridentEventServiceProvider::class,
App\Providers\TridentRouteServiceProvider::class,
App\Providers\TridentServiceProvider::class,
```

to config/app
