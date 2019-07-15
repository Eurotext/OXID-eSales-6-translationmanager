## Installation

Copy files to %SHOPROOT%/modules/eurotext/translationmanager6

Add local repository to composer.json

```
"eurotext/translationmanager6": {
    "type": "path",
    "url": "source/modules/eurotext/translationmanager6"
}
```

Require master version of module in composer.json

```
"require": {
    ...,
    "eurotext/translationmanager6": "dev-master"
  },
```

Run `composer update` to regenerate autoload files. The module should be installed and visible in backend.

Activate in backend.
