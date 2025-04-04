# Kreyu DataTableBundle Demo

This is an unofficial demo application that aims to showcase the main features of [DataTableBundle](https://github.com/Kreyu/data-table-bundle) by [Kreyu](https://github.com/Kreyu). Work in progress.

## Features
- Theme switcher (Bootstrap 5, Tabler)
- Locale switcher (Polish and English)
- Async switcher
- Light/Dark mode switcher
- AssetMapper (Node.JS not required)
- Source code preview for each table
- Tables...

## Requirements
- PHP 8.2+ with extensions (especially SQLite3 and DOM)
- Some webserver (the one included with [Symfony CLI](https://symfony.com/download) will be completely sufficient)

## Getting started

First of all: clone this repo and `cd` into it.

If you're using [DDEV](https://ddev.com/), you can simply use `ddev start` to launch whole environment.
If you're using [Symfony CLI](https://symfony.com/download), you can use `symfony local:server:start -d` to start webserver.
Of course you can use any other PHP environment capable of running Symfony apps.

Install dependencies:
```
composer install
```

Create database:
```
php bin/console doctrine:database:create
```

Load database structure and fake data:
```
php bin/console app:reload-database
```

Open app in web browser and *voilà!*
