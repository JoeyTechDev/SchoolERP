<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title><?= $title ?? 'SchoolERP'; ?></title>

<style>

body{
    margin:40px;
    font-family:Arial,sans-serif;
}

header{

    background:#2563eb;
    color:white;
    padding:18px;
    margin-bottom:25px;
}

footer{

    margin-top:40px;
    border-top:1px solid #ddd;
    padding-top:20px;
    color:#777;
}

</style>

</head>

<body>

<header>

<h2>SchoolERP Framework</h2>

</header>

<main>

<?= $content ?>

</main>

<footer>

SchoolERP Framework © <?= date('Y') ?>

</footer>

</body>

</html>