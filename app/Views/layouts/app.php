<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="robots"
        content="noindex,nofollow"
    >

    <title>
        <?= htmlspecialchars(
            $title ?? 'SchoolERP',
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <!-- Bootstrap 5 -->
    <link
        href="/SchoolERP/public/assets/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Application Styles -->
    <style>

        body {
            background-color: #f8fafc;
            color: #212529;
        }

        .app-header {
            background: #0d6efd;
        }

        .app-header .navbar-brand {
            font-weight: 700;
        }

        .app-main {
            min-height: calc(100vh - 130px);
        }

        .app-footer {
            background: #ffffff;
            border-top: 1px solid #dee2e6;
        }

    </style>

</head>

<body>

    <!-- Navigation -->
    <header class="app-header">

        <nav class="navbar navbar-expand-lg navbar-dark">

            <div class="container-fluid px-4">

                <a
                    class="navbar-brand"
                    href="/SchoolERP/public/"
                >
                    SchoolERP
                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNavigation"
                    aria-controls="mainNavigation"
                    aria-expanded="false"
                    aria-label="Toggle navigation"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div
                    class="collapse navbar-collapse"
                    id="mainNavigation"
                >

                    <ul class="navbar-nav ms-auto">

                        <li class="nav-item">

                            <a
                                class="nav-link"
                                href="/SchoolERP/public/students"
                            >
                                Students
                            </a>

                        </li>

                    </ul>

                </div>

            </div>

        </nav>

    </header>


    <!-- Main Content -->
    <main class="app-main">

        <?= $content ?>

    </main>


    <!-- Footer -->
    <footer class="app-footer py-3 mt-4">

        <div class="container-fluid px-4">

            <div class="text-center text-muted small">

                SchoolERP Framework
                &copy;
                <?= date('Y') ?>

            </div>

        </div>

    </footer>


    <!-- Bootstrap JavaScript -->
    <script
        src="/SchoolERP/public/assets/js/bootstrap.bundle.min.js"
    ></script>

</body>

</html>