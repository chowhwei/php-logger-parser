<?php

namespace Chowhwei\PhpLoggerParser;

use Exception;

/**
 * 解析 nginx 日志
 */
class Parser
{
    protected static $defaultFields = [
        '{host}',
        '{logname}',
        '{user}',
        '{time}',
        '"{referer}}"',
        '{status}',
        '{response_bytes}'
    ];
    protected $pcreFormat;
    protected $patterns = [
        '{percent}' => '(?P<percent>\%)',
        '{remote_ip}' => '(?P<remote_ip>)',
        '{local_ip}' => '(?P<local_ip>)',
        '{host}' => '(?P<host>[a-zA-Z0-9\-\._:]+)',
        '{logname}' => '(?P<logname>(?:-|[\w-]+))',
        '{request_method}' => '(?P<request_method>OPTIONS|GET|HEAD|POST|PUT|DELETE|TRACE|CONNECT|PATCH|PROPFIND)',
        '{port}' => '(?P<port>\d+)',
        '{protocol}' => '(?P<protocol>HTTP/(1\.0|1\.1|2\.0))',
        '{request}' => '(?P<request>.+?)',
        '{time}' => '\[(?P<time>\d{2}/(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)/\d{4}:\d{2}:\d{2}:\d{2} (?:-|\+)\d{4})\]',
        '{user}' => '(?P<user>(?:-|[\w-]+))',
        '{url}' => '(?P<url>.+?)',
        '{referer}' => '(?P<referer>.+?)',
        '{user_agent}' => '(?P<user_agent>.+?)',
        '{server_name}' => '(?P<server_name>([a-zA-Z0-9]+)([a-z0-9.-]*))',
        '{canonical_server_name}' => '(?P<canonical_server_name>([a-zA-Z0-9]+)([a-z0-9.-]*))',
        '{status}' => '(?P<status>\d{3}|-)',
        '{response_bytes}' => '(?P<response_bytes>(\d+|-))',
        '{request_time}' => '(?P<request_time>(\d+\.?\d*))',
        '{sent_bytes}' => '(?P<sent_bytes>[0-9]+)',
        '{received_bytes}' => '(?P<received_bytes>[0-9]+)',
        '{time_serve_request}' => '(?P<time_serve_request>[0-9]+)',
        '{http_x_forwarded_for}' => '(?P<http_x_forwarded_for>.+?)',
        '{gzip_ratio}' => '(?P<gzip_ratio>(\d+\.?\d*))',
        '{upstream_cache_status}' => '(?P<upstream_cache_status>.+?)'
    ];

    /**
     * @throws Exception
     */
    public function __construct($fields = null)
    {
        $ipPatterns = implode('|', array(
            'ipv4' => '(((25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9])\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9]))',
            'ipv6full' => '([0-9A-Fa-f]{1,4}(:[0-9A-Fa-f]{1,4}){7})', // 1:1:1:1:1:1:1:1
            'ipv6null' => '(::)',
            'ipv6leading' => '(:(:[0-9A-Fa-f]{1,4}){1,7})', // ::1:1:1:1:1:1:1
            'ipv6mid' => '(([0-9A-Fa-f]{1,4}:){1,6}(:[0-9A-Fa-f]{1,4}){1,6})', // 1:1:1::1:1:1
            'ipv6trailing' => '(([0-9A-Fa-f]{1,4}:){1,7}:)', // 1:1:1:1:1:1:1::
        ));
        $this->patterns['{remote_ip}'] = '(?P<remote_ip>' . $ipPatterns . ')';
        $this->patterns['{local_ip}'] = '(?P<local_ip>' . $ipPatterns . ')';
        $this->setFields($fields ?: self::$defaultFields);
    }

    public function setFields($fields)
    {
        $format = implode(' ', $fields);
        $expr = "#^$format$#";
        foreach ($this->patterns as $pattern => $replace) {
            $expr = preg_replace("/$pattern/", $replace, $expr);
        }
        $this->pcreFormat = $expr;
    }

    /**
     * @throws Exception
     */
    public function parse($line)
    {
        if (!preg_match($this->pcreFormat, $line, $matches)) {
            throw new Exception("Error parsing line");
        }
        $entry = new LogEntity();
        foreach (array_filter(array_keys($matches), 'is_string') as $key) {
            if ('time' === $key && true !== $stamp = strtotime($matches[$key])) {
                $entry['stamp'] = $stamp;
            }
            $entry[$key] = $matches[$key];
        }
        return $entry;
    }
}
