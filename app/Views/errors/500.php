<h1 class="error-code">
    <?= htmlspecialchars(
        (string) $statusCode,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    ) ?>
</h1>

<h2 class="mb-3">
    <?= htmlspecialchars(
        $title,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    ) ?>
</h2>

<p class="lead text-muted mb-4">
    <?= htmlspecialchars(
        $message,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    ) ?>
</p>