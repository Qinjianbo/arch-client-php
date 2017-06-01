<?php

/*
 * This file is part of the arch client php package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Liugj\Arch;

use ArrayAccess;
use Exception;
use GuzzleHttp\Psr7\Response as GuzzleHttpResponse;
use Iterator;

class Response implements Iterator, ArrayAccess
{
    /**
     * body.
     *
     * @var mixed
     */
    public $body;
    /**
     * headers.
     *
     * @var array
     */
    public $headers = array();
    /**
     * json.
     *
     * @var string
     */
    protected $json = '';

    /**
     * __construct.
     *
     * @param GuzzleHttpResponse $response
     *
     * @return mixed
     */
    public function __construct(GuzzleHttpResponse $response)
    {
        $code = $response->getStatusCode(); // 200

        if ($code != 200) {
            $reason = $response->getReasonPhrase(); // OK
            throw new Exception($reason, $code);
        }
        foreach ($response->getHeaders() as $name => $values) {
            $this->headers[$name] = $values;
        }
        $this->body = (string) $response->getBody();

        if (!isset($this->headers['X-Api-Proxy']) || !$this->headers['X-Api-Proxy']) {
            $this->json = $this->json(true);
            if (!isset($this->json['ResponseStatus']) || $this->json['ResponseStatus'] != 0) {
                throw new Exception($this->json['ResponseMsg']);
            }
        }
    }

    /**
     * toArray.
     *
     *
     * @return mixed
     */
    public function toArray()
    {
        return $this->json['ResponseData'];
    }

    /**
     * first.
     *
     *
     * @return mixed
     */
    public function first()
    {
        return isset($this->json['ResponseData']) && is_array($this->json['ResponseData']) ?
                 current($this->json['ResponseData']) : null;
    }

    /**
     * firstValue.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function firstValue($key)
    {
        $first = $this->first();

        return isset($first[$key]) ? $first[$key] : null;
    }

    /**
     * body.
     *
     *
     * @return mixed
     */
    public function body()
    {
        return $this->body;
    }

    /**
     * json.
     *
     * @param mixed $assoc
     *
     * @return mixed
     */
    public function json($assoc = true)
    {
        $json = json_decode($this->body, $assoc);
        if (!$json) {
            throw new Exception\JsonDecode(json_last_error_msg());
        }

        return $json;
    }

    /**
     * status.
     *
     *
     * @return mixed
     */
    public function status()
    {
        return $this->header['Status-Code'];
    }

    /**
     * header.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function header($key, $default = null)
    {
        return isset($this->header[$key]) ? $this->header[$key] : $default;
    }

    /**
     * rewind.
     *
     *
     *
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->json['ResponseData']);
    }

    /**
     * current.
     *
     *
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->json['ResponseData']);
    }

    /**
     * key.
     *
     *
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->json['ResponseData']);
    }

    /**
     * next.
     *
     *
     *
     * @return mixed
     */
    public function next()
    {
        return next($this->json['ResponseData']);
    }

    /**
     * valid.
     *
     *
     *
     * @return mixed
     */
    public function valid()
    {
        return is_array($this->json['ResponseData'])
            && (key($this->json['ResponseData']) !== null);
    }

    /**
     * offsetExists.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetExists($key)
    {
        return is_array($this->json['ResponseData']) ?
            isset($this->json['ResponseData'][$key]) : false;
    }

    /**
     * offsetGet.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (!$this->offsetExists($key)) {
            return;
        }

        return is_array($this->json['ResponseData']) ?
            $this->json['ResponseData'][$key] : null;
    }

    /**
     * offsetSet.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function offsetSet($key, $value)
    {
        throw new Exception\ArrayAccess('Decoded response data is immutable.');
    }

    /**
     * offsetUnset.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetUnset($key)
    {
        throw new Exception\ArrayAccess('Decoded response data is immutable.');
    }

    /**
     * __toString.
     *
     *
     *
     * @return mixed
     */
    public function __toString()
    {
        return $this->body;
    }
}
