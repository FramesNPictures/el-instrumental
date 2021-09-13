<?php

namespace Fnp\ElInstrumental\Connectors;

use ErrorException;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class InstrumentalConnector
{
    const VERSION = "1.0.2";
    const HOST    = 'collector.instrumentalapp.com';
    const PORT    = 8000;

    protected mixed  $socket;
    protected string $host;
    protected string $apiKey;
    protected int    $port;
    protected int    $connectionTimeout;
    protected int    $responseTimeout;

    /**
     * @param  string  $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey            = $apiKey;
        $this->host              = 'collector.instrumentalapp.com';
        $this->port              = 8000;
        $this->connectionTimeout = 10;
        $this->responseTimeout   = 2;
    }

    /**
     * @param  string  $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param  string  $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param  int  $port
     */
    public function setPort(int $port): void
    {
        $this->port = $port;
    }

    /**
     * @param  int  $connectionTimeout
     */
    public function setConnectionTimeout(int $connectionTimeout): void
    {
        $this->connectionTimeout = $connectionTimeout;
    }

    /**
     * @param  int  $responseTimeout
     */
    public function setResponseTimeout(int $responseTimeout): void
    {
        $this->responseTimeout = $responseTimeout;
    }

    public function connect()
    {
        $errorCode    = null;
        $errorMessage = null;

        $this->socket = stream_socket_client(
            "tcp://{$this->host}:{$this->port}",
            $errorCode,
            $errorMessage,
        );

        if ($errorCode) {
            throw new RuntimeException($errorMessage, $errorCode);
        }

        stream_set_timeout($this->socket, $this->responseTimeout, 0);

        $hostname = gethostname();
        $pid      = getmypid();
        $runtime  = phpversion();
        $platform = preg_replace('/\s+/', '_', php_uname());
        $hello    =
            'hello version php/instrumental_agent/'.self::VERSION.' '.
            "hostname {$hostname} ".
            "pid {$pid} ".
            "runtime {$runtime} ".
            "platform {$platform}";

        $this->send($hello);
        $this->send("authenticate {$this->apiKey}");
    }

    public function send($command, $attempts = 5)
    {
        if (!$this->isConnected()) {
            $this->connect();
        }

        try {
            if (fwrite($this->socket, $command.PHP_EOL) === false) {
                throw new RuntimeException('Could not write to collector');
            }
        } catch (Exception $e) {
            if ($attempts>0) {
                $this->disconnect();
                usleep(500);
                $this->connect();
                $this->send($command, $attempts-1);
            }
        }

        usleep(500);
    }

    public function isConnected(): bool
    {
        return !is_null($this->socket);
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function disconnect(): void
    {
        stream_socket_shutdown($this->socket, STREAM_SHUT_RDWR);
    }

    protected function isValidMetric($metric, $value, $time, $count): bool
    {
        $valid_metric = preg_match("/^([\d\w\-_]+\.)*[\d\w\-_]+$/i", $metric);
        $valid_value  = preg_match("/^-?\d+(\.\d+)?((e|E)-\d+)?$/", print_r($value, true));

        if (!$valid_metric || !$valid_value) {
            return false;
        }

        return true;
    }
}