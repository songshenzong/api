<?php

namespace Songshenzong\Log\DataCollector;

use Songshenzong\Log\DataCollector\DataCollector;
use Songshenzong\Log\DataCollector\DataCollectorInterface;

use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Based on \Symfony\Component\HttpKernel\DataCollector\RequestCollector by Fabien Potencier <fabien@symfony.com>
 *
 */
class RequestCollector extends DataCollector implements DataCollectorInterface
{
    /** @var \Symfony\Component\HttpFoundation\Request $request */
    protected $request;
    /** @var  \Symfony\Component\HttpFoundation\Request $response */
    protected $response;
    /** @var  \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
    protected $session;

    /**
     * Create a new RequestCollector
     *
     * @param \Symfony\Component\HttpFoundation\Request                  $request
     * @param \Symfony\Component\HttpFoundation\Request                  $response
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct($request, $response, $session = null)
    {
        $this -> request  = $request;
        $this -> response = $response;
        $this -> session  = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'request';
    }


    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $request  = $this -> request;
        $response = $this -> response;

        $responseHeaders = $response -> headers -> all();
        $cookies         = [];
        foreach ($response -> headers -> getCookies() as $cookie) {
            $cookies[] = $this -> getCookieHeader(
                $cookie -> getName(),
                $cookie -> getValue(),
                $cookie -> getExpiresTime(),
                $cookie -> getPath(),
                $cookie -> getDomain(),
                $cookie -> isSecure(),
                $cookie -> isHttpOnly()
            );
        }
        if (count($cookies) > 0) {
            $responseHeaders['Set-Cookie'] = $cookies;
        }

        $statusCode = $response -> getStatusCode();


        $data = [
            'format'           => $request -> getRequestFormat(),
            'content_type'     => $response -> headers -> get('Content-Type') ? $response -> headers -> get(
                'Content-Type'
            ) : 'text/html',
            'status_text'      => isset(Response ::$statusTexts[$statusCode]) ? Response ::$statusTexts[$statusCode] : '',
            'status_code'      => $statusCode,
            'path_info'        => $request -> getPathInfo(),
            'query'            => $request -> query -> all(),
            'request'          => $request -> request -> all(),
            'get'              => $GLOBALS['_GET'],
            'post'             => $GLOBALS['_POST'],
            'session'          => isset($GLOBALS['_SESSION']) ? $GLOBALS['_SESSION'] : '',
            'headers'          => $request -> headers -> all(),
            'server'           => array_change_key_case($request -> server -> all(), CASE_LOWER),
            'cookies'          => $request -> cookies -> all(),
            'response_headers' => $responseHeaders,
        ];


        foreach ($data['server'] as $key => $value) {
            if (str_is('*_KEY', $key) || str_is('*_PASSWORD', $key)
                || str_is('*_SECRET', $key) || str_is('*_PW', $key)
            ) {
                $data['server'][$key] = '******';
            }
        }

        if (isset($data['headers']['php-auth-pw'])) {
            $data['headers']['php-auth-pw'] = '******';
        }


        return $data;
    }

    private function getCookieHeader($name, $value, $expires, $path, $domain, $secure, $httponly)
    {
        $cookie = sprintf('%s=%s', $name, urlencode($value));

        if (0 !== $expires) {
            if (is_numeric($expires)) {
                $expires = (int)$expires;
            } elseif ($expires instanceof \DateTime) {
                $expires = $expires -> getTimestamp();
            } else {
                $expires = strtotime($expires);
                if (false === $expires || -1 == $expires) {
                    throw new \InvalidArgumentException(
                        sprintf('The "expires" cookie parameter is not valid.', $expires)
                    );
                }
            }

            $cookie .= '; expires=' . substr(
                    \DateTime ::createFromFormat('U', $expires, new \DateTimeZone('UTC')) -> format('D, d-M-Y H:i:s T'),
                    0,
                    -5
                );
        }

        if ($domain) {
            $cookie .= '; domain=' . $domain;
        }

        $cookie .= '; path=' . $path;

        if ($secure) {
            $cookie .= '; secure';
        }

        if ($httponly) {
            $cookie .= '; httponly';
        }

        return $cookie;
    }
}
