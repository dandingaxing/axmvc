<?php

namespace common\middleware;

// use DebugBar\StandardDebugBar;



class debug
{

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        // $response->getBody()->write('BEFORE');
        $response = $next($request, $response);
        // $response->getBody()->write('AFTER -- debug to here');
        return $response;
    }

    public function pageDebug(){
        $debugbar = new StandardDebugBar();
        $debugbarRenderer = $debugbar->getJavascriptRenderer();
        $debugbar["messages"]->addMessage("hello world!");
        echo "page debug";
    }







}