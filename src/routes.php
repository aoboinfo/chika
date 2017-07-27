<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("LandPrice '/' route");

    // Render index view
    return $this->view->render($response, 'top.twig', array('name' => 'Fabien'));
});
