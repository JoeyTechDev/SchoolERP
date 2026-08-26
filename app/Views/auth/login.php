<?php

declare(strict_types=1);

use SchoolERP\Session\SessionInterface;

/**
 * @var string $csrf_token
 * @var string|null $title
 */

$title = $title ?? 'Login';

$session = $GLOBALS['container']->make(
    SessionInterface::class
);

$error = $session->flash('_auth_error');
$oldLogin = $session->flash('_old_login') ?? [];

$oldLogin = is_array($oldLogin)
    ? $oldLogin
    : [];

$email = (string) (
    $oldLogin['email'] ?? ''
);
?>

<style>
    .auth-page {
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .auth-card {
        width: 100%;
        max-width: 460px;
        border: 0;
        border-radius: 18px;
        box-shadow: 0 12px 35px rgba(15, 42, 74, 0.10);
        overflow: hidden;
    }

    .auth-brand {
        background: linear-gradient(
            135deg,
            #0F2A4A 0%,
            #1E56A0 70%,
            #4C8FE0 100%
        );
        color: #fff;
        padding: 2rem;
    }

    .auth-brand-mark {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.30);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        overflow: hidden;
    }

    .auth-brand-mark img {
        max-width: 42px;
        max-height: 42px;
    }

    .auth-form {
        padding: 2rem;
        background: #fff;
    }

    .auth-form .form-control {
        border-radius: 10px;
        padding: .75rem .9rem;
    }

    .auth-form .form-control:focus {
        border-color: #4C8FE0;
        box-shadow: 0 0 0 .2rem rgba(76, 143, 224, .15);
    }

    .auth-submit {
        border: 0;
        border-radius: 10px;
        padding: .75rem 1rem;
        font-weight: 600;
        background: #1E56A0;
        color: #fff;
    }

    .auth-submit:hover {
        background: #0F2A4A;
        color: #fff;
    }

    .auth-alert {
        border-radius: 10px;
    }
</style>

<div class="auth-page">

    <div class="card auth-card">

        <div class="auth-brand">

            <div class="d-flex align-items-center gap-3">

                <div class="auth-brand-mark">

                    <img
                        src="/SchoolERP/public/assets/images/logo.png"
                        alt="SchoolERP Logo"
                    >

                </div>

                <div>
                    <div class="fw-bold fs-5">
                        SchoolERP
                    </div>

                    <div class="small opacity-75">
                        School Management System
                    </div>
                </div>

            </div>

            <div class="mt-4">

                <h1 class="h4 fw-bold mb-2">
                    Welcome back
                </h1>

                <p class="mb-0 opacity-75">
                    Sign in to access your SchoolERP account.
                </p>

            </div>

        </div>

        <div class="auth-form">

            <?php if (
                is_string($error)
                && $error !== ''
            ): ?>

                <div
                    class="alert alert-danger auth-alert py-2"
                    role="alert"
                >
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>

            <?php endif; ?>

            <form
                method="POST"
                action="/SchoolERP/public/auth/login"
                novalidate
            >

                <input
                    type="hidden"
                    name="_token"
                    value="<?= htmlspecialchars(
                        (string) $csrf_token,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

                <div class="mb-3">

                    <label
                        for="email"
                        class="form-label fw-semibold"
                    >
                        Email address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars(
                            $email,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>"
                        placeholder="Enter your email"
                        autocomplete="username"
                        required
                        autofocus
                    >

                </div>

                <div class="mb-3">

                    <label
                        for="password"
                        class="form-label fw-semibold"
                    >
                        Password
                    </label>

                    <div class="input-group">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            id="togglePassword"
                            aria-label="Show password"
                        >
                            Show
                        </button>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn auth-submit w-100"
                >
                    Sign In
                </button>

            </form>

            <div class="text-center mt-4">

                <small class="text-muted">
                    Trouble signing in?
                    Contact your school administrator.
                </small>

            </div>

        </div>

    </div>

</div>

<script>
(function () {
    const passwordInput =
        document.getElementById('password');

    const togglePassword =
        document.getElementById('togglePassword');

    if (
        passwordInput === null ||
        togglePassword === null
    ) {
        return;
    }

    togglePassword.addEventListener(
        'click',
        function () {
            const hidden =
                passwordInput.type === 'password';

            passwordInput.type =
                hidden ? 'text' : 'password';

            togglePassword.textContent =
                hidden ? 'Hide' : 'Show';

            togglePassword.setAttribute(
                'aria-label',
                hidden
                    ? 'Hide password'
                    : 'Show password'
            );
        }
    );
})();
</script>