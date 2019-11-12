# laravel-pinba
Laravel pinba middleware and timers integraion

## Description
Integrates [pinba](http://pinba.org/ "Pinba site") with [Laravel](https://laravel.com "Laravel site")

## Installation
```bash
composer require chocofamilyme/laravel-pinba
```

## Publishing the configuration (optional)
```bash
php artisan vendor:publish --provider="Chocofamilyme\LaravelPinba\Providers\PinbaServiceProvider"
```

## Configuration
### Pinba
Pinba configuration file is located under config/pinba.php

## Usage
There is a Facade for the libary called "Pinba"
### Start the timer
```php
$timerId = Pinba::startTimer(string $group, string $type, string $method, string $category);
```

### Stop the timer
```php
Pinba::stopTimer($timerId)
```

### Stop all timers
```php
Pinba::stopAllTimers();
```

### More methods
Just see the class "Chocofamilyme\LaravelPinba\Profiler\PinbaDestination"

## Destinations
### Pinba
This library sends the data to the pinba server

### File
This library sends the data to log file

### Null
The data is not beeing sent