<?php

require_once "Auth.php";

function dd(...$args) {
    echo "<pre>";
    foreach ($args as $arg) {
        echo var_dump($arg);
    }
    echo "</pre>";
    die;
}

function auth(): ?Auth {
    $user = null;
    $is_authenticated = false;

    if (!empty($_SESSION['user'])) {
        $user = @ new Auth(...unserialize($_SESSION['user']));
        $is_authenticated = true;
    }

    if (! $is_authenticated) {
        $_SESSION['errors'] = [
            'Unauthenticated!'
        ];
        header(header: 'Location: login.php');
        exit;
    }

    return $user;
}
function server(): ?Server {
    return new Server();
}

function request(): ?Request {
    return new Request();
}

