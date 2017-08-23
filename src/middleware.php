<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
$container = $app->getContainer();
/*
 * Slim treats a URL pattern with a trailing slash as different to one without.
 * That is, /user and /user/ are different and so can have different callbacks attached.
 * For GET requests a permanent redirect is fine, but for other request methods like POST or PUT the browser will send the second request with the GET method.
 * To avoid this you simply need to remove the trailing slash and pass the manipulated url to the next middleware.
 */
/*
$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        // permanently redirect paths with a trailing slash
        // to their non-trailing counterpart
        $uri = $uri->withPath(substr($path, 0, -1));

        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }
    return $next($request, $response);
});
*/