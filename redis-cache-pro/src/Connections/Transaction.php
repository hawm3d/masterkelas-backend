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

namespace RedisCachePro\Connections;

use RedisCachePro\Connections\Connection;

class Transaction
{
    /**
     * The transaction type.
     *
     * @var int
     */
    public $type;

    /**
     * The underlying connection to execute the transaction on.
     *
     * @var \RedisCachePro\Connections\Connection
     */
    public $connection = [];

    /**
     * Holds all queued commands.
     *
     * @var array
     */
    public $commands = [];

    /**
     * Creates a new transaction instance.
     *
     * @param  int  $type
     * @param  \RedisCachePro\Connections\Connection  $connection
     * @return void
     */
    public function __construct(int $type, Connection $connection)
    {
        $this->type = $type;
        $this->connection = $connection;
    }

    /**
     * Shim to execute the transaction on the underlying connection.
     *
     * @return array
     */
    public function exec()
    {
        return $this->connection->executeMulti($this->type, $this);
    }

    /**
     * Memorize all method calls for later execution.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->commands[] = [$method, $parameters];

        return $this;
    }
}
