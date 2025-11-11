<style>
    body {
        background: #f8f9fa;
    }

    .session-card {
        border-radius: 12px;
    }

    .session-key {
        font-weight: 600;
        font-size: 14px;
    }

    pre {
        background: #1d1f21;
        color: #eee;
        padding: 12px;
        border-radius: 8px;
        font-size: 14px;
        overflow-x: auto;
    }
</style>
<div class="container py-4">

    <h2 class="mb-4 text-center text-secondary">
        <i class="material-symbols-rounded text-3xl"> physical_therapy</i> PHP Session Inspector
    </h2>

    <?php if (empty($_SESSION)): ?>
        <div class="alert alert-warning text-center">
            <strong>No session data found.</strong>
        </div>
    <?php else: ?>

        <?php foreach ($_SESSION as $key => $value): ?>
            <div class="card shadow-sm mb-3 session-card">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <span class="session-key"><?= htmlspecialchars($key) ?></span>
                    <span class="badge bg-light text-primary">Type: <?= gettype($value) ?></span>
                </div>
                <div class="card-body">
                    <pre><?php print_r($value); ?></pre>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>