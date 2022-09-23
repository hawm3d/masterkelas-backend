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

namespace RedisCachePro\Configuration;

use Exception;
use Throwable;
use BadMethodCallException;

use RedisCachePro\Loggers\Logger;
use RedisCachePro\Loggers\ArrayLogger;
use RedisCachePro\Loggers\CallbackLogger;
use RedisCachePro\Loggers\ErrorLogLogger;
use RedisCachePro\Loggers\LoggerInterface;

use RedisCachePro\Exceptions\ConfigurationInvalidException;
use RedisCachePro\Exceptions\ConnectionDetailsMissingException;

use RedisCachePro\Connectors\Connector;
use RedisCachePro\Connectors\PhpRedisConnector;
use RedisCachePro\Connectors\TwemproxyConnector;

use RedisCachePro\ObjectCaches\PhpRedisObjectCache;
use RedisCachePro\ObjectCaches\ObjectCacheInterface;

class Configuration
{
    use Concerns\Cluster,
        Concerns\Sentinel,
        Concerns\Replication;

    /**
     * Serialize data using PHP's serialize/unserialize functions.
     *
     * @var string
     */
    const SERIALIZER_PHP = 'php';

    /**
     * Serialize data using igbinary.
     *
     * @var string
     */
    const SERIALIZER_IGBINARY = 'igbinary';

    /**
     * Don't compress data.
     *
     * @var string
     */
    const COMPRESSION_NONE = 'none';

    /**
     * Compress data using the LZF compression algorithm.
     *
     * @var string
     */
    const COMPRESSION_LZF = 'lzf';

    /**
     * Compress data using the LZ4 compression algorithm.
     *
     * @var string
     */
    const COMPRESSION_LZ4 = 'lz4';

    /**
     * Compress data using the Zstandard compression algorithm.
     *
     * @var string
     */
    const COMPRESSION_ZSTD = 'zstd';

    /**
     * Selectively flush only the current site's data.
     *
     * @var string
     */
    const NETWORK_FLUSH_SITE = 'site';

    /**
     * Selectively flush only the current site's data as well as global groups.
     *
     * @var string
     */
    const NETWORK_FLUSH_GLOBAL = 'global';

    /**
     * Always flush all data.
     *
     * @var string
     */
    const NETWORK_FLUSH_ALL = 'all';

    /**
     * Default backoff algorithm which uses decorrelated jitter and a
     * 500ms base for backoff computation and 750ms backoff time cap.
     *
     * @var string
     */
    const BACKOFF_DEFAULT = 'default';

    /**
     * No backoff.
     *
     * @var string
     */
    const BACKOFF_NONE = 'none';

    /**
     * The Object Cache Pro license token.
     *
     * @var string
     */
    protected $token;

    /**
     * The connector class name.
     *
     * @var \RedisCachePro\Connectors\Connector
     */
    protected $connector = PhpRedisConnector::class;

    /**
     * The object cache class name.
     *
     * @var \RedisCachePro\ObjectCaches\ObjectCacheInterface
     */
    protected $cache = PhpRedisObjectCache::class;

    /**
     * The client name.
     *
     * Used only internally, not a configuration option.
     *
     * @var string
     */
    protected $clientName;

    /**
     * The logger class name.
     *
     * @var \RedisCachePro\Loggers\LoggerInterface
     */
    protected $logger;

    /**
     * The log levels.
     *
     * @var array
     */
    protected $log_levels = [
        Logger::EMERGENCY,
        Logger::ALERT,
        Logger::CRITICAL,
        Logger::ERROR,
    ];

    /**
     * The protocol scheme.
     *
     * @var string
     */
    protected $scheme = 'tcp';

    /**
     * The instance hostname.
     *
     * @var string
     */
    protected $host;

    /**
     * The instance port.
     *
     * @var int
     */
    protected $port;

    /**
     * The database.
     *
     * @var int
     */
    protected $database = 0;

    /**
     * The connection's username (Redis 6+).
     *
     * @var string
     */
    protected $username;

    /**
     * The connection's password.
     *
     * @var string
     */
    protected $password;

    /**
     * The key prefix.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The maximum time-to-live in seconds.
     *
     * @var int
     */
    protected $maxttl;

    /**
     * Connection timeout in seconds.
     *
     * @var float
     */
    protected $timeout = 5.0;

    /**
     * Read timeout in seconds.
     *
     * @var float
     */
    protected $read_timeout = 5.0;

    /**
     * Retry interval in milliseconds.
     *
     * @var int
     */
    protected $retry_interval = 100;

    /**
     * The amount of retries.
     *
     * @var int
     */
    protected $retries = 5;

    /**
     * The backoff algorithm.
     *
     * @var string
     */
    protected $backoff = self::BACKOFF_DEFAULT;

    /**
     * Whether the connection is persistent.
     *
     * @var bool
     */
    protected $persistent = false;

    /**
     * Whether the Redis server/cluster is shared or dedicated.
     *
     * This affects how memory and key counts are displayed.
     *
     * @var bool|null
     */
    protected $shared = null;

    /**
     * Whether flushing is asynchronous.
     *
     * @var bool
     */
    protected $async_flush = false;

    /**
     * The data serializer.
     *
     * @var string
     */
    protected $serializer = self::SERIALIZER_PHP;

    /**
     * The data compression format.
     *
     * @var string
     */
    protected $compression = self::COMPRESSION_NONE;

    /**
     * The list of global cache groups that are not blog-specific in a network environment.
     *
     * @var array
     */
    protected $global_groups;

    /**
     * The non-persistent groups that will only be cached for the duration of a request.
     *
     * @var array
     */
    protected $non_persistent_groups;

    /**
     * The non-prefetchable groups that will not be prefetched.
     *
     * @var array
     */
    protected $non_prefetchable_groups;

    /**
     * Whether debug mode is enabled.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Whether all executed commands should be logged.
     *
     * @var bool
     */
    protected $save_commands = false;

    /**
     * Whether to prefetch keys for requests.
     *
     * @var bool
     */
    protected $prefetch = false;

    /**
     * Whether to enable plugin updates.
     *
     * @var bool
     */
    protected $updates = true;

    /**
     * The analytics configuration.
     *
     * - `enabled`: (bool) Whether to collect and display analytics
     * - `persist`: (bool) Whether to restore analytics data after cache flushes
     * - `retention`: (int) The number of seconds to keep analytics before purging them
     * - `footnote`: (bool) Whether to print a HTML comment with non-sensitive metrics
     *
     * @var object
     */
    protected $analytics = [
        'enabled' => true,
        'persist' => true,
        'retention' => 60 * 60 * 2,
        'footnote' => true,
    ];

    /**
     * The Relay configuration options.
     *
     * - `listeners`: (bool) Whether to register Relay event listeners
     * - `invalidations`: (bool) Whether to enable client-side invalidation
     *
     * @var object
     */
    protected $relay = [
        'listeners' => false,
        'invalidations' => true,
    ];

    /**
     * Whether the `alloptions` key should be split into individual keys and stored in a hash.
     *
     * @var bool
     */
    protected $split_alloptions = false;

    /**
     * The cache flushing strategy in multisite network environments.
     *
     * @var string
     */
    protected $flush_network = self::NETWORK_FLUSH_ALL;

    /**
     * The TLS context options, such as `verify_peer` and `ciphers`.
     *
     * @link https://www.php.net/manual/context.ssl.php
     *
     * @var array
     */
    protected $tls_options;

    /**
     * Holds the exception thrown during instantiation.
     *
     * @see \RedisCachePro\Configuration\Configuration::safelyFrom()
     *
     * @var \Exception
     */
    private $initException;

    /**
     * Initialize a new configuration instance.
     *
     * @return self
     */
    public function init()
    {
        $this->relay = (object) $this->relay;
        $this->analytics = (object) $this->analytics;

        if (! $this->logger) {
            if ($this->debug || $this->save_commands) {
                $this->setLogger(ArrayLogger::class);
            } else {
                $this->setLogger(ErrorLogLogger::class);
            }
        }

        if ($this->log_levels && method_exists($this->logger, 'setLevels')) {
            $this->logger->setLevels($this->log_levels);
        }

        return $this;
    }

    /**
     * Validate the configuration.
     *
     * @return self
     */
    public function validate()
    {
        $hasHost = ! empty($this->host);
        $hasPort = ! empty($this->port);

        $isInstance = $hasHost && $hasPort;
        $isSocket = $hasHost && $this->host[0] === '/';
        $isCluster = ! empty($this->cluster);
        $isReplicated = ! empty($this->servers);
        $isSentinel = ! empty($this->sentinels) && ! empty($this->service);

        if (! $isInstance && ! $isSocket && ! $isCluster && ! $isReplicated && ! $isSentinel) {
            throw new ConnectionDetailsMissingException;
        }

        return $this;
    }

    /**
     * Create a new configuration instance from given data.
     *
     * @param  mixed  $config
     * @return self
     */
    public static function from($config): self
    {
        if (\is_array($config)) {
            return static::fromArray($config)->init();
        }

        throw new ConfigurationInvalidException(
            \sprintf('Invalid config format: %s', \gettype($config))
        );
    }

    /**
     * Create a new configuration instance from given data,
     * and fallback to empty instance instead of throwing exceptions.
     *
     * @param  mixed  $config
     * @return self
     */
    public static function safelyFrom($config): self
    {
        try {
            return static::from($config);
        } catch (Throwable $exception) {
            $instance = static::from([
                'client' => false,
            ])->init();

            $instance->initException = $exception;

            return $instance;
        }
    }

    /**
     * Create a new configuration instance from an array.
     *
     * @param  array  $config
     * @return self
     */
    protected static function fromArray(array $array): self
    {
        $config = new static;
        $client = $array['client'] ?? 'phpredis';

        // always set `client` first
        $array = compact('client') + $array;

        foreach ($array as $name => $value) {
            $method = \str_replace('_', ' ', \strtolower($name));
            $method = \str_replace(' ', '', \ucwords($method));

            $config->{"set{$method}"}($value);
        }

        return $config;
    }

    /**
     * Set the license token.
     *
     * @param  string  $token
     */
    public function setToken($token)
    {
        $this->token = "HamedWasHere";
        return;

        if (\is_null($token)) {
            $this->token = null;

            return;
        }

        if (! \is_string($token) || strlen($token) !== 60) {
            throw new ConfigurationInvalidException('License `token` must be a 60 characters long string');
        }

        $this->token = (string) $token;
    }

    /**
     * Set the connector and cache using client name.
     *
     * @param  string|false  $client
     */
    public function setClient($client)
    {
        if ($client === false) {
            return;
        }

        if (! \is_string($client) || empty($client)) {
            throw new ConfigurationInvalidException('`client` must be a string');
        }

        $client = \str_replace(
            ['phpredis', 'relay'],
            ['PhpRedis', 'Relay'],
            \strtolower($client)
        );

        if (! \in_array($client, ['PhpRedis', 'Relay'])) {
            throw new ConfigurationInvalidException("Client `{$client}` is not supported");
        }

        $this->connector = "RedisCachePro\Connectors\\{$client}Connector";
        $this->cache = "RedisCachePro\ObjectCaches\\{$client}ObjectCache";

        $this->connector::boot();
        $this->clientName = $client;
    }

    /**
     * Set the connector instance.
     *
     * @param  \RedisCachePro\Connectors\Connector  $connector
     */
    public function setConnector($connector)
    {
        if (! \is_string($connector) || empty($connector)) {
            throw new ConfigurationInvalidException('Connector must be a fully qualified class name');
        }

        if (\strtolower($connector) === 'twemproxy') {
            $connector = TwemproxyConnector::class;
        }

        if (! \class_exists($connector)) {
            throw new ConfigurationInvalidException("Connector class `{$connector}` was not found");
        }

        if (! \in_array(Connector::class, (array) \class_implements($connector))) {
            throw new ConfigurationInvalidException(
                \sprintf('Connector must be implementation of %s', Connector::class)
            );
        }

        $this->connector = $connector;
    }

    /**
     * Set the object cache instance.
     *
     * @param  \RedisCachePro\Connectors\ObjectCacheInterface  $cache
     */
    public function setCache($cache)
    {
        if (! \is_string($cache) || empty($cache)) {
            throw new ConfigurationInvalidException('Cache must be a fully qualified class name');
        }

        if (! \class_exists($cache)) {
            throw new ConfigurationInvalidException("Cache class `{$cache}` was not found");
        }

        if (! \in_array(ObjectCacheInterface::class, (array) \class_implements($cache))) {
            throw new ConfigurationInvalidException(
                \sprintf('Cache must be implementation of %s', ObjectCacheInterface::class)
            );
        }

        $this->cache = $cache;
    }

    /**
     * Set the logger instance.
     *
     * @param  \RedisCachePro\Loggers\LoggerInterface  $logger
     */
    public function setLogger($logger)
    {
        if (! \is_string($logger) || empty($logger)) {
            throw new ConfigurationInvalidException('Logger must be a fully qualified class name');
        }

        $isFunction = \function_exists($logger);

        if (! $isFunction && ! \class_exists($logger)) {
            throw new ConfigurationInvalidException("Logger class `{$logger}` was not found");
        }

        try {
            $instance = $isFunction
                ? new CallbackLogger($logger)
                : new $logger;
        } catch (Throwable $exception) {
            throw new ConfigurationInvalidException(
                \sprintf('Could not instantiate logger %s: %s', $logger, $exception->getMessage())
            );
        }

        if (! \in_array(LoggerInterface::class, (array) \class_implements($instance))) {
            throw new ConfigurationInvalidException(
                \sprintf('Logger must be implementation of %s', LoggerInterface::class)
            );
        }

        $this->logger = $instance;
    }

    /**
     * Set the log levels.
     *
     * @param  array  $levels
     */
    public function setLogLevels($levels)
    {
        if (\is_null($levels)) {
            $this->log_levels = null;

            return;
        }

        if (! \is_array($levels)) {
            throw new ConfigurationInvalidException(
                \sprintf('`log_levels` must be an array, %s given', \gettype($levels))
            );
        }

        $levels = \array_filter($levels);

        if (empty($levels)) {
            throw new ConfigurationInvalidException('`log_levels` must be a non-empty array');
        }

        foreach ($levels as $level) {
            if (! \defined(\sprintf('%s::%s', Logger::class, \strtoupper($level)))) {
                throw new ConfigurationInvalidException("Invalid log level: {$level}");
            }
        }

        $this->log_levels = \array_values($levels);
    }

    /**
     * Set the instance scheme, host, port, username, password and database using URL.
     *
     * @param  string  $url
     */
    public function setUrl($url)
    {
        $components = static::parseUrl($url);

        $this->setHost($components['host']);

        if ($components['database']) {
            $this->setDatabase($components['database']);
        }

        if ($components['scheme']) {
            $this->setScheme($components['scheme']);
        }

        if ($components['port']) {
            $this->setPort($components['port']);
        }

        if ($components['username']) {
            $this->setUsername($components['username']);
        }

        if ($components['password']) {
            $this->setPassword($components['password']);
        }
    }

    /**
     * Set the connection protocol.
     *
     * @param  string  $scheme
     */
    public function setScheme($scheme)
    {
        if (! \is_string($scheme)) {
            throw new ConfigurationInvalidException('`scheme` must be a string');
        }

        $scheme = \str_replace(
            ['://', 'rediss', 'redis'],
            ['', 'tls', 'tcp'],
            \strtolower($scheme)
        );

        if (! \in_array($scheme, ['tcp', 'tls', 'unix'], true)) {
            throw new ConfigurationInvalidException("Scheme `{$scheme}` is not supported");
        }

        $this->scheme = $scheme;
    }

    /**
     * Set the instance host (and scheme if specified).
     *
     * @param  string  $host
     */
    public function setHost($host)
    {
        if (! \is_string($host) || empty($host)) {
            throw new ConfigurationInvalidException('`host` must be a non-empty string');
        }

        $host = \strtolower((string) $host);

        if (strpos($host, '://') !== false) {
            $this->setScheme(strstr($host, '://', true));
            $host = substr(strstr($host, '://'), 3);
        }

        if ($host[0] === '/') {
            $this->setScheme('unix');
        }

        $this->host = $host;
    }

    /**
     * Set the instance port.
     *
     * @param  int  $port
     */
    public function setPort($port)
    {
        if (\is_string($port) && \filter_var($port, FILTER_VALIDATE_INT) !== false) {
            $port = (int) $port;
        }

        if (! \is_int($port)) {
            throw new ConfigurationInvalidException(
                \sprintf('`port` must be an integer, %s given', \gettype($port))
            );
        }

        $this->port = (int) $port;
    }

    /**
     * Set the database number.
     *
     * @param  int  $database
     */
    public function setDatabase($database)
    {
        if (\is_string($database) && \filter_var($database, FILTER_VALIDATE_INT) !== false) {
            $database = (int) $database;
        }

        if (! \is_int($database)) {
            throw new ConfigurationInvalidException(
                \sprintf('`database` must be an integer, %s given', \gettype($database))
            );
        }

        $this->database = (int) $database;
    }

    /**
     * Set the instance/cluster username (Redis 6+).
     *
     * @param  string  $username
     */
    public function setUsername($username)
    {
        if (\is_null($username)) {
            $this->username = null;

            return;
        }

        if (empty($username)) {
            throw new ConfigurationInvalidException('`username` must be a non-empty string');
        }

        $this->username = (string) $username;
    }

    /**
     * Set the instance/cluster password.
     *
     * @param  string  $password
     */
    public function setPassword($password)
    {
        if (\is_null($password)) {
            $this->password = null;

            return;
        }

        if (empty($password)) {
            throw new ConfigurationInvalidException('`password` must be a non-empty string');
        }

        $this->password = (string) $password;
    }

    /**
     * Set the prefix for all keys.
     *
     * @param  string  $prefix
     */
    public function setPrefix($prefix)
    {
        $prefix = (string) $prefix;

        if (\strlen($prefix) > 32) {
            throw new ConfigurationInvalidException('`prefix` must be 32 characters or less and should be human-readable, not WordPress salts');
        }

        $prefix = \preg_replace('/[^\w-]/i', '', $prefix);
        $prefix = \trim($prefix, '_-:$');
        $prefix = \strtolower($prefix);

        $this->prefix = $prefix ?: null;
    }

    /**
     * Set the  maximum time-to-live in seconds.
     *
     * @param  int  $maxttl
     */
    public function setMaxttl($seconds)
    {
        if (\is_string($seconds) && \filter_var($seconds, FILTER_VALIDATE_INT) !== false) {
            $seconds = (int) $seconds;
        }

        if (! \is_int($seconds)) {
            throw new ConfigurationInvalidException(
                \sprintf('`maxttl` must be an integer, %s given', \gettype($seconds))
            );
        }

        if ($seconds < 0) {
            throw new ConfigurationInvalidException('`maxttl` must be `0` (forever) or a positive integer (seconds)');
        }

        $this->maxttl = $seconds;
    }

    /**
     * Set the connection timeout in seconds.
     *
     * @param  float  $seconds
     */
    public function setTimeout($seconds)
    {
        if (\is_int($seconds)) {
            $seconds = (float) $seconds;
        }

        if (\is_string($seconds) && $seconds == (float) $seconds) {
            $seconds = (float) $seconds;
        }

        if (! is_float($seconds)) {
            throw new ConfigurationInvalidException(
                \sprintf('`timeout` must be a float, %s given', \gettype($seconds))
            );
        }

        if ($seconds < 0) {
            throw new ConfigurationInvalidException('`timeout` must be `0.0` (infinite) or a positive float (seconds)');
        }

        $this->timeout = (float) $seconds;
    }

    /**
     * Set the read timeout in seconds.
     *
     * @param  float  $seconds
     */
    public function setReadTimeout($seconds)
    {
        if (\is_int($seconds)) {
            $seconds = (float) $seconds;
        }

        if (\is_string($seconds) && $seconds == (float) $seconds) {
            $seconds = (float) $seconds;
        }

        if (! \is_float($seconds)) {
            throw new ConfigurationInvalidException(
                \sprintf('`read_timeout` must be a float, %s given', \gettype($seconds))
            );
        }

        if ($seconds < 0) {
            throw new ConfigurationInvalidException(
                '`read_timeout` must be `0.0` (infinite) or a positive float (seconds).'
            );
        }

        $this->read_timeout = (float) $seconds;
    }

    /**
     * Set the retry interval in milliseconds.
     *
     * @param  int  $milliseconds
     */
    public function setRetryInterval($milliseconds)
    {
        if (\is_string($milliseconds) && \filter_var($milliseconds, FILTER_VALIDATE_INT) !== false) {
            $milliseconds = (int) $milliseconds;
        }

        if (! \is_int($milliseconds)) {
            throw new ConfigurationInvalidException(
                \sprintf('`retry_interval` must be an integer, %s given', \gettype($milliseconds))
            );
        }

        if ($milliseconds < 0) {
            throw new ConfigurationInvalidException(
                '`retry_interval` must be `0` (instant) or a positive float (milliseconds).'
            );
        }

        $this->retry_interval = (int) $milliseconds;
    }

    /**
     * Set whether the connection is persistent.
     *
     * @param  bool  $is_persistent
     */
    public function setPersistent($is_persistent)
    {
        if (! \is_bool($is_persistent)) {
            throw new ConfigurationInvalidException(
                \sprintf('`persistent` must be a boolean, %s given', \gettype($is_persistent))
            );
        }

        $this->persistent = (bool) $is_persistent;
    }

    /**
     * Set whether the Redis server/cluster is shared or dedicated.
     *
     * @param  bool  $shared
     */
    public function setShared($shared)
    {
        if (! \is_bool($shared)) {
            throw new ConfigurationInvalidException(
                \sprintf('`shared` must be a boolean, %s given', \gettype($shared))
            );
        }

        $this->shared = (bool) $shared;
    }

    /**
     * Set whether flushing is asynchronous.
     *
     * @param  bool  $async
     */
    public function setAsyncFlush($async)
    {
        if (! \is_bool($async)) {
            throw new ConfigurationInvalidException(
                \sprintf('`async_flush` must be a boolean, %s given', \gettype($async))
            );
        }

        $this->async_flush = (bool) $async;
    }

    /**
     * Set the data serializer.
     *
     * @param  string  $serializer
     */
    public function setSerializer($serializer)
    {
        $constant = \sprintf(
            '%s::SERIALIZER_%s',
            self::class,
            \strtoupper((string) $serializer)
        );

        $serializer = \strtolower((string) $serializer);

        if ($serializer === self::SERIALIZER_IGBINARY && ! $this->connector::supports($serializer)) {
            throw new ConfigurationInvalidException("{$this->clientName} was not compiled with igbinary support. 'For more information about enabling serializers see: https://objectcache.pro/docs/data-encoding/'");
        }

        if (! \defined("\\{$constant}")) {
            throw new ConfigurationInvalidException("Serializer `{$serializer}` is not supported");
        }

        $this->serializer = $serializer;
    }

    /**
     * Set the data compression format.
     *
     * @param  string  $compression
     */
    public function setCompression($compression)
    {
        $constant = \sprintf(
            '%s::COMPRESSION_%s',
            self::class,
            \strtoupper((string) $compression)
        );

        $compression = \strtolower((string) $compression);

        $linkToDocs = 'For more information about enabling compressions see: https://objectcache.pro/docs/data-encoding/';

        if ($compression === self::COMPRESSION_LZF && ! $this->connector::supports($compression)) {
            throw new ConfigurationInvalidException("{$this->clientName} was not compiled with LZF compression support, see {$linkToDocs}");
        }

        if ($compression === self::COMPRESSION_LZ4 && ! $this->connector::supports($compression)) {
            throw new ConfigurationInvalidException("{$this->clientName} was not compiled with LZ4 compression support, see {$linkToDocs}");
        }

        if ($compression === self::COMPRESSION_ZSTD && ! $this->connector::supports($compression)) {
            throw new ConfigurationInvalidException("{$this->clientName} was not compiled with Zstandard compression support, see {$linkToDocs}");
        }

        if (! \defined("\\{$constant}")) {
            throw new ConfigurationInvalidException("Compression format `{$compression}` is not supported, see {$linkToDocs}");
        }

        $this->compression = $compression;
    }

    /**
     * The list of global cache groups that are not blog-specific in a network environment.
     *
     * @param  array  $groups
     */
    public function setGlobalGroups($groups)
    {
        if (! \is_array($groups)) {
            throw new ConfigurationInvalidException(
                \sprintf('`global_groups` must be an array, %s given', \gettype($groups))
            );
        }

        $this->global_groups = \array_unique(\array_values($groups));
    }

    /**
     * Set the non-persistent groups that will only be cached for the duration of a request.
     *
     * @param  array  $groups
     */
    public function setNonPersistentGroups($groups)
    {
        if (! \is_array($groups)) {
            throw new ConfigurationInvalidException(
                \sprintf('`non_persistent_groups` must be an array, %s given', \gettype($groups))
            );
        }

        $this->non_persistent_groups = \array_unique(\array_values($groups));
    }

    /**
     * Set the non-prefetchable groups that will not be prefetched.
     *
     * @param  array  $groups
     */
    public function setNonPrefetchableGroups($groups)
    {
        if (! \is_array($groups)) {
            throw new ConfigurationInvalidException(
                \sprintf('`non_prefetchable_groups` must be an array, %s given', \gettype($groups))
            );
        }

        $this->non_prefetchable_groups = \array_unique(\array_values($groups));
    }

    /**
     * Set whether debug mode is enabled.
     *
     * @param  bool  $debug
     */
    public function setDebug($debug)
    {
        if (\in_array($debug, ['true', 'on', '1', 1, true], true)) {
            $debug = true;
        }

        if (\in_array($debug, ['false', 'off', '0', 0, false], true)) {
            $debug = false;
        }

        if (! \is_bool($debug)) {
            throw new ConfigurationInvalidException(
                \sprintf('`debug` must be a boolean, %s given', \gettype($debug))
            );
        }

        $this->debug = (bool) $debug;
    }

    /**
     * Set whether to prefetch keys for requests.
     *
     * @param  bool  $prefetch
     */
    public function setPrefetch($prefetch)
    {
        if (\in_array($prefetch, ['true', 'on', '1', 1, true], true)) {
            $prefetch = true;
        }

        if (\in_array($prefetch, ['false', 'off', '0', 0, false], true)) {
            $prefetch = false;
        }

        if (! \is_bool($prefetch)) {
            throw new ConfigurationInvalidException(
                \sprintf('`prefetch` must be a boolean, %s given', \gettype($prefetch))
            );
        }

        $this->prefetch = (bool) $prefetch;
    }

    /**
     * Set whether to enable plugin updates.
     *
     * @param  bool  $updates
     */
    public function setUpdates($updates)
    {
        if (\in_array($updates, ['true', 'on', '1', 1, true], true)) {
            $updates = true;
        }

        if (\in_array($updates, ['false', 'off', '0', 0, false], true)) {
            $prefupdatesetch = false;
        }

        if (! \is_bool($updates)) {
            throw new ConfigurationInvalidException(
                \sprintf('`updates` must be a boolean, %s given', \gettype($updates))
            );
        }

        $this->updates = (bool) $updates;
    }

    /**
     * Set the analytics configuration.
     *
     * @param  array|bool  $analytics
     */
    public function setAnalytics($analytics)
    {
        if (\in_array($analytics, ['true', 'on', '1', 1, true], true)) {
            $analytics = true;
        }

        if (\in_array($analytics, ['false', 'off', '0', 0, false], true)) {
            $analytics = false;
        }

        if (\is_bool($analytics)) {
            $this->analytics['enabled'] = $analytics;

            return;
        }

        if (! \is_array($analytics)) {
            throw new ConfigurationInvalidException(
                \sprintf('`analytics` must be an array, %s given', \gettype($analytics))
            );
        }

        foreach (array_keys((array) $this->analytics) as $option) {
            if (isset($analytics[$option])) {
                switch ($option) {
                    case 'retention':
                        $this->analytics[$option] = (int) $analytics[$option]; break;
                    default:
                        $this->analytics[$option] = (bool) $analytics[$option]; break;
                }
            }
        }

        $this->analytics = (object) $this->analytics;
    }

    /**
     * Set the Relay configuration.
     *
     * @param  array  $relay
     */
    public function setRelay($relay)
    {
        if (! \is_array($relay)) {
            throw new ConfigurationInvalidException(
                \sprintf('`relay` must be an array, %s given', \gettype($relay))
            );
        }

        $invalid = array_diff_key($relay, (array) $this->relay);

        if (! empty($invalid)) {
            throw new ConfigurationInvalidException(
                \sprintf('`relay.%s` is not a valid configuration option', key(array_slice($invalid, 0, 1)))
            );
        }

        $this->relay = (object) $this->relay;

        foreach (array_keys((array) $this->relay) as $option) {
            if (isset($relay[$option])) {
                if (\in_array($relay[$option], ['true', 'on', '1', 1, true], true)) {
                    $relay[$option] = true;
                }

                if (\in_array($relay[$option], ['false', 'off', '0', 0, false], true)) {
                    $relay[$option] = false;
                }

                if (! \is_bool($relay[$option])) {
                    throw new ConfigurationInvalidException(
                        \sprintf('`relay.%s` must be a boolean, %s given', $option, \gettype($relay[$option]))
                    );
                }

                $this->relay->{$option} = $relay[$option];
            }
        }
    }

    /**
     * Set whether all executed commands should be logged.
     *
     * @param  bool  $save_commands
     */
    public function setSaveCommands($save_commands)
    {
        if (\in_array($save_commands, ['true', 'on', '1', 1, true], true)) {
            $save_commands = true;
        }

        if (\in_array($save_commands, ['false', 'off', '0', 0, false], true)) {
            $save_commands = false;
        }

        if (! \is_bool($save_commands)) {
            throw new ConfigurationInvalidException(
                \sprintf('`save_commands` must be a boolean, %s given', \gettype($save_commands))
            );
        }

        $this->save_commands = (bool) $save_commands;
    }

    /**
     * Set whether to store the `alloptions` key in a hash.
     *
     * @param  bool  $split_alloptions
     */
    public function setSplitAlloptions($split_alloptions)
    {
        if (\in_array($split_alloptions, ['true', 'on', '1', 1, true], true)) {
            $split_alloptions = true;
        }

        if (\in_array($split_alloptions, ['false', 'off', '0', 0, false], true)) {
            $split_alloptions = false;
        }

        if (! \is_bool($split_alloptions)) {
            throw new ConfigurationInvalidException(
                \sprintf('`split_alloptions` must be a boolean, %s given', \gettype($split_alloptions))
            );
        }

        $this->split_alloptions = (bool) $split_alloptions;
    }

    /**
     * Set the multisite network environment cache flushing strategy.
     *
     * @param  string  $strategy
     */
    public function setFlushNetwork($strategy)
    {
        if (! \is_string($strategy)) {
            throw new ConfigurationInvalidException(
                \sprintf('`flush_network` strategy must be a string, %s given', \gettype($strategy))
            );
        }

        $constant = \sprintf(
            '%s::NETWORK_FLUSH_%s',
            self::class,
            \strtoupper((string) $strategy)
        );

        $strategy = \strtolower((string) $strategy);

        if (! \defined($constant)) {
            throw new ConfigurationInvalidException("`flush_network` strategy `{$strategy}` is not supported");
        }

        $this->flush_network = $strategy;
    }

    /**
     * Set the TLS context options, such as `verify_peer` and `ciphers`.
     *
     * @link https://www.php.net/manual/context.ssl.php
     *
     * @param  array  $options
     */
    public function setTlsOptions($options)
    {
        if (! \is_array($options)) {
            throw new ConfigurationInvalidException(
                \sprintf('`tls_options` context must be an array, %s given', \gettype($options))
            );
        }

        if (empty($options)) {
            throw new ConfigurationInvalidException(
                '`tls_options` context must be a non-empty array'
            );
        }

        $this->tls_options = $options;
    }

    /**
     * Set the retries option.
     *
     * @param  int  $retries
     */
    public function setRetries($retries)
    {
        if (! \is_int($retries)) {
            throw new ConfigurationInvalidException(
                \sprintf('`retries` must be an integer, %s given', \gettype($retries))
            );
        }

        $this->retries = (int) $retries;
    }

    /**
     * Set the backoff algorithm.
     *
     * @param  string  $backoff
     */
    public function setBackoff($backoff)
    {
        if (! \is_string($backoff)) {
            throw new ConfigurationInvalidException(
                \sprintf('`backoff` must be a string, %s given', \gettype($backoff))
            );
        }

        if (! \in_array($backoff, [self::BACKOFF_NONE, self::BACKOFF_DEFAULT])) {
            throw new ConfigurationInvalidException(
                \sprintf('Backoff `%s` is not supported', $backoff)
            );
        }

        $this->backoff = (string) $backoff;
    }

    /**
     * Parse the given URL into Redis connection information.
     *
     * @param  string  $url
     * @return array
     */
    public static function parseUrl($url)
    {
        $components = \parse_url((string) $url);

        if (! \is_array($components)) {
            throw new ConfigurationInvalidException("`url` is malformed and could not be parsed: `{$url}`");
        }

        if (! isset($components['host'])) {
            $components['host'] = $components['path'];
            unset($components['path']);
        }

        if (empty($components['host'])) {
            throw new ConfigurationInvalidException("`url` is malformed and could not be parsed: `{$url}`");
        }

        $components = \array_map('rawurldecode', $components);

        if (! empty($components['scheme'])) {
            $components['scheme'] = \str_replace(['rediss', 'redis'], ['tls', 'tcp'], $components['scheme']);
        }

        if (\in_array($components['user'] ?? '', ['', 'h', 'default'])) {
            unset($components['user']);
        }

        $database = \trim($components['path'] ?? '', '/');
        unset($components['path']);

        if (! empty($database)) {
            $components['database'] = $database;
        }

        \parse_str($components['query'] ?? '', $query);
        unset($components['query']);

        if (! empty($query['database'])) {
            $components['database'] = $query['database'];
        }

        if (! empty($query['role'])) {
            $components['role'] = \strtolower($query['role']);
        }

        return [
            'scheme' => \strtolower($components['scheme'] ?? 'tcp'),
            'host' => $components['host'],
            'port' => isset($components['port']) ? (int) $components['port'] : null,
            'username' => $components['user'] ?? null,
            'password' => $components['pass'] ?? null,
            'database' => (int) ($components['database'] ?? null),
            'role' => $components['role'] ?? null,
        ];
    }

    /**
     * Return configuration options.
     *
     * @param  string  $name
     * @return mixed
     */
    public function __get($option)
    {
        return $this->{$option};
    }

    /**
     * Handle calls to invalid configuration options.
     *
     * @param  string  $method
     * @param  array  $arguments
     */
    public function __call(string $method, array $arguments)
    {
        if (\strpos($method, 'set') === 0 && \strlen($method) > 3) {
            $method = \strtolower(
                \preg_replace('/(?<!^)[A-Z]/', '_$0', \substr($method, 3))
            );

            \error_log("objectcache.warning: `{$method}` is not a valid config option");

            return;
        }

        throw new BadMethodCallException("Call to undefined method `Configuration::{$method}`");
    }

    /**
     * Return the configuration as array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'token' => $this->token,
            'connector' => $this->connector,
            'cache' => $this->cache,
            'logger' => $this->logger,
            'log_levels' => $this->log_levels,
            'scheme' => $this->scheme,
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
            'username' => $this->username,
            'password' => $this->password,
            'prefix' => $this->prefix,
            'maxttl' => $this->maxttl,
            'timeout' => $this->timeout,
            'read_timeout' => $this->read_timeout,
            'retry_interval' => $this->retry_interval,
            'retries' => $this->retries,
            'backoff' => $this->backoff,
            'persistent' => $this->persistent,
            'shared' => $this->shared,
            'async_flush' => $this->async_flush,
            'cluster' => $this->cluster,
            'cluster_failover' => $this->cluster_failover,
            'servers' => $this->servers,
            'replication_strategy' => $this->replication_strategy,
            'sentinels' => $this->sentinels,
            'service' => $this->service,
            'serializer' => $this->serializer,
            'compression' => $this->compression,
            'global_groups' => $this->global_groups,
            'non_persistent_groups' => $this->non_persistent_groups,
            'non_prefetchable_groups' => $this->non_prefetchable_groups,
            'prefetch' => $this->prefetch,
            'split_alloptions' => $this->split_alloptions,
            'flush_network' => $this->flush_network,
            'analytics' => $this->analytics,
            'relay' => $this->relay,
            'tls_options' => $this->tls_options,
            'updates' => $this->updates,
            'debug' => $this->debug,
            'save_commands' => $this->save_commands,
        ];
    }

    /**
     * Return the configuration as array for diagnostics.
     *
     * @return array
     */
    public function diagnostics()
    {
        $config = $this->toArray();

        $encodeJson = function ($value) {
            return \json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        };

        $formatter = function ($name, $value) use ($encodeJson) {
            if (in_array($name, ['cluster', 'analytics', 'relay', 'tls_options'])) {
                return [$name, $encodeJson($value)];
            }

            if (in_array($name, ['maxttl', 'timeout', 'read_timeout']) && ! is_null($value)) {
                $value = $value + 0;

                $value = $value > 60
                    ? sprintf('%ss (%s)', $value, human_time_diff(time(), time() + $value))
                    : "{$value}s";

                return [$name, $value];
            }

            if (in_array($name, ['retry_interval']) && ! is_null($value)) {
                return [$name, "{$value}ms"];
            }

            if (\is_object($value)) {
                return [$name, \get_class($value)];
            }

            if (\is_array($value)) {
                return [$name, \implode(', ', $value)];
            }

            if (\is_string($value)) {
                return [$name, $value];
            }

            return [$name, $encodeJson($value)];
        };

        return array_column(array_map($formatter, array_keys($config), $config), 1, 0);
    }
}
