# trident
A laravel code generator for developing applications following Domain Driven Design (DDD) principles

At the moment this package is for demonstration purposes only. Alpha software at best.

## instructions

### to add to a laravel project as a package
add 
```
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/j0hnys/trident"
    }
],
```
and 
```
"require": {
    "j0hnys/trident": "dev-master"
},
```
to laravels `composer.json`

### to install in laravel

after `php artisan trident:install` add 

```
App\Providers\TridentAuthServiceProvider::class,
App\Providers\TridentEventServiceProvider::class,
App\Providers\TridentRouteServiceProvider::class,
App\Providers\TridentServiceProvider::class,
```

to config/app

