<?php

declare(strict_types=1);

namespace RedisCachePro\Exceptions;

use RedisCachePro\Connectors\RelayConnector;

class RelayOutdatedException extends ObjectCacheException
{
    public function __construct($message = '', $code = 0, $previous = null)
    {
        if (empty($message)) {
            $message = sprintf(
                'Object Cache Pro requires Relay %s or newer. This environment (%s) was loaded with Relay %s.',
                explode('-', RelayConnector::RequiredVersion)[0],
                PHP_SAPI,
                phpversion('relay')
            );
        }

        parent::__construct($message, $code, $previous);
    }
}
