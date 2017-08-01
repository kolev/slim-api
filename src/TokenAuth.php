<?php
namespace App;

class TokenAuth {

    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
        $this->whiteList = ['\/auth\/login'];
    }

    public function authenticate($token)
    {
        return (new \App\Controllers\AuthController($this->container))->validateToken($token);
    }

    public function isPublicUrl($url)
    {
        $patterns_flattened = implode('|', $this->whiteList);
        $matches = null;
        preg_match('/' . $patterns_flattened . '/', $url, $matches);
        return (count($matches) > 0);
    }

    public function __invoke($request, $response, $next)
    {
        $token = $request->getHeader('Authorization');

        if ($this->isPublicUrl($request->getUri()->getPath()) || $request->isOptions()) {
            return $next($request, $response);
        }
        if (!empty($token) && $this->authenticate($token[0])) {
            return $next($request, $response);
        }

        return  $response->withStatus(401);
    }
}
