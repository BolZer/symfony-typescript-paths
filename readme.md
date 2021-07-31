# Symfony-Typescript-Routes
![maintained](https://img.shields.io/maintenance/yes/2021)
![ci](https://travis-ci.com/BolZer/symfony-typescript-routes.svg?branch=master)
[![codecov](https://codecov.io/gh/BolZer/symfony-typescript-routes/branch/master/graph/badge.svg?token=W7IYQXY2UD)](https://codecov.io/gh/BolZer/symfony-typescript-routes)

## Description

This Extension for Symfony provides a Generation Service which can be used - after registering the provided extension -
in your code to generate typescript code from the application route.

## Installation

```shell
composer require bolzer/symfony-typescript-routes
```

## Example

1. Registering the Extension
```PHP
// bundles.php
<?php

return [
    Bolzer\SymfonyTypescriptRoutes\Bundle\TypescriptPathBundle::class => ['all' => true],
];

```


2. Write something to create a path.ts file with the content from the service. Like a Command!
```PHP
// some_command.php
<?php

use Bolzer\SymfonyTypescriptRoutes\Service\GeneratorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerationCommand extends Command
{
    public function __construct(
        private GeneratorService $generatorService,
    ){}

      protected function configure(): void
    {
        $this->setName('generate_paths');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
       file_put_contents(__DIR__ . '../../../paths.ts', implode("\n", $this->generatorService->generate()));
       $output->writeln('<comment>Generation of paths done.</comment>');
       return 1;
    }
}
```

The Output may look something like this 

```Typescript
//paths.ts
type L = 'de'|'en'
const rRP = (rawRoute: string, routeParams: Record<string, string>): string => {Object.entries(routeParams).forEach(([key, value]) => rawRoute = rawRoute.replace(`{${key}}`, value)); return rawRoute;}
const aQP = (route: string, queryParams?: Record<string, string>): string => queryParams ? route + "?" + new URLSearchParams(queryParams).toString() : route;
export const path_user_route = (routeParams: {id: string, nodeID: string}, queryParams?: Record<string, string>): string => aQP(rRP('/user/{id}/{nodeID}', routeParams), queryParams);
export const path_users_route = (queryParams?: Record<string, string>): string => aQP('/users', queryParams);

```

And can be used like this

```Typescript
//paths.ts
import * as $ from "jquery";
import {path_users_route} from "./paths";

$.get(path_users_route({"count": "20"}))

// Outputs: /users?count=20
console.log(path_users_route({"count": "20"}))
```

## Conventions

* Query and Route Params must be provided as strings to the Typescript Functions
* All generated path functions in typescript will have a "path_" prefix.
* Currently only relative routes are supported


### Executing Tests

```shell
docker build -t ts-path-tests .
docker run ts-path-tests
```

### Test Coverage

```shell
XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-text
```
