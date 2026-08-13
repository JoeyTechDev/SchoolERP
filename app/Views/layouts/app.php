<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= htmlspecialchars(
            $title ?? 'SchoolERP',
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }

        header {
            background: #2563eb;
            color: white;
            padding: 18px 40px;
        }

        header h2 {
            margin: 0;
        }

        main {
            padding: 30px 40px;
            min-height: 70vh;
        }

        footer {
            margin-top: 40px;
            padding: 20px 40px;
            border-top: 1px solid #ddd;
            color: #777;
            background: white;
        }

        a {
            text-decoration: none;
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

    SchoolERP Framework &copy; <?= date('Y') ?>

</footer>

</body>

</html>