<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static function (Configuration $config): Configuration {
    return $config
        // used because we use the services they provide
        ->addNamedFilter(NamedFilter::fromString('symfony/web-link'))
        ->addNamedFilter(NamedFilter::fromString('setono/client-id-bundle'))
    ;
};
