<?php

declare(strict_types=1);

$appName = 'SchoolERP';
$baseUrl = '';

/**
 * --------------------------------------------------------------------------
 * SchoolERP Error Layout
 * --------------------------------------------------------------------------
 *
 * Master layout for all application error pages.
 *
 * Variables provided by ErrorHandler:
 *
 * @var int    $statusCode
 * @var string $title
 * @var string $message
 * @var string $contentFile
 * --------------------------------------------------------------------------
 */

if (
    !isset(
        $statusCode,
        $title,
        $message,
        $contentFile
    )
) {
    throw new \RuntimeException(
        'Error layout loaded without the required variables.'
    );
}

if (!is_file($contentFile)) {
    throw new \RuntimeException(
        sprintf(
            'Missing error content file: %s',
            $contentFile
        )
    );
}

$appName = defined('APP_NAME')
    ? APP_NAME
    : 'SchoolERP';
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1"
>

<meta
    name="robots"
    content="noindex,nofollow"
>

<title><?= htmlspecialchars(
    $title,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
) ?></title>

<link
 href="<?= $baseUrl ?>/assets/css/bootstrap.min.css" rel="stylesheet"
>

<style>

:root{

    --primary:#0d6efd;
    --background:#f8fafc;
    --muted:#5a6169;
    --shadow:0 12px 40px rgba(0,0,0,.08);

}

body{

    margin:0;

    background:var(--background);

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    font-family:
        "Segoe UI",
        Tahoma,
        Geneva,
        Verdana,
        sans-serif;

}

.error-card{

    border:none;

    border-radius:18px;

    box-shadow:var(--shadow);

}

.error-code{

    font-size:90px;

    font-weight:800;

    color:var(--primary);

    line-height:1;

}

.school-name{

    font-size:14px;

    letter-spacing:3px;

    color:var(--muted);

    text-transform:uppercase;

    font-weight:600;

}

@media (max-width:576px){

.error-code{

    font-size:56px;

}

}

</style>

</head>

<body>

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8 col-md-10">

<div class="card error-card">

<main
    class="card-body p-5 text-center"
    role="main"
>

<div class="school-name mb-3">

<?= htmlspecialchars(
    $appName,
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
) ?>

</div>

<?php require $contentFile; ?>

<hr class="my-4">

<a
    href="<?= $baseUrl ?>/"
    class="btn btn-primary px-4"
>
    Return Home
</a>

</main>

</div>

</div>

</div>

</div>

<script
    src="<?= $baseUrl ?>/assets/js/bootstrap.bundle.min.js"
></script>

</body>

</html>