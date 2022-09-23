<?php
/**
 * Copyright © Rhubarb Tech Inc. All Rights Reserved.
 *
 * All information contained herein is, and remains the property of Rhubarb Tech Incorporated.
 * The intellectual and technical concepts contained herein are proprietary to Rhubarb Tech Incorporated and
 * are protected by trade secret or copyright law. Dissemination and modification of this information or
 * reproduction of this material is strictly forbidden unless prior written permission is obtained from
 * Rhubarb Tech Incorporated.
 *
 * You should have received a copy of the `LICENSE` with this file. If not, please visit:
 * https://objectcache.pro/license.txt
 */

declare(strict_types=1);

namespace RedisCachePro\Plugin\Options;

use WP_Error;
use RedisCachePro\Plugin;

class Validator
{
    /**
     * The plugin instance.
     *
     * @var \RedisCachePro\Plugin
     */
    private $plugin;

    /**
     * Creates a new instance.
     *
     * @param  \RedisCachePro\Plugin  $plugin
     * @return void
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Returns a `WP_Error` instance for the given code and message.
     *
     * @param  string  $code
     * @param  string  $message
     * @return WP_Error
     */
    protected function error($code, $message)
    {
        return new WP_Error($code, $message);
    }

    /**
     * Validate the `channel` option value.
     *
     * @param  string  $value
     * @return void|WP_Error
     */
    public function channel($value)
    {
        $license = $this->plugin->license();

        $stabilities = $license::Stabilities;
        $accessibleStabilities = $license->accessibleStabilities();

        if (! in_array($value, array_keys($stabilities), true)) {
            return $this->error('invalid-channel', sprintf('"%s" is not a valid channel.', $value));
        }

        if (! in_array($value, array_keys($accessibleStabilities), true)) {
            return $this->error('inaccessible-channel', sprintf('Your license cannot use the "%s" update channel.', $value));
        }
    }

    /**
     * Validate the `flushlog` option value.
     *
     * @param  bool  $value
     * @return void|WP_Error
     */
    public function flushlog($value)
    {
        if (! is_bool($value)) {
            return $this->error('invalid-type', 'Option must be boolean.');
        }
    }
}
