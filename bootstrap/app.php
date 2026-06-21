<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.owner'          => \App\Http\Middleware\AuthOwner::class,
            'auth.storeuser'      => \App\Http\Middleware\AuthStoreUser::class,
            'auth.admin'          => \App\Http\Middleware\AuthAdmin::class,
            'role'                => \App\Http\Middleware\CheckStoreUserRole::class,
            'check.subscription'  => \App\Http\Middleware\CheckSubscription::class,
        ]);

        // Railway pone su propio proxy/load balancer delante de tu app y
        // termina el SSL ahí. El tráfico real que recibe tu contenedor es
        // HTTP, con un header X-Forwarded-Proto: https indicando que el
        // usuario sí está en HTTPS. Sin confiar en ese proxy, Laravel cree
        // que TODA la conexión es HTTP y genera formularios/links con
        // http://, lo que dispara la advertencia del navegador.
        //
        // '*' confía en cualquier proxy. Es seguro aquí porque Railway es
        // el único punto de entrada a tu contenedor (no es accesible
        // directamente desde fuera de su red).
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO |
                     Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();