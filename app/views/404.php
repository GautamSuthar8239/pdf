<div class="error-container bg-dark d-flex flex-column align-items-center justify-content-center text-center min-vh-100 p-4">
    <div class="lottie-animation mb-3"></div>

    <div class="error-content text-white" style="max-width: 600px; word-wrap: break-word;">
        <h2 class="fw-bold mb-3">404 - Page Not Found</h2>

        <?php 
            $url = esc($_SERVER['REQUEST_URI']); 
            $displayUrl = strlen($url) > 60 ? substr($url, 0, 57) . '...' : $url;
        ?>

        <p class="mb-4">
            Sorry, the page <strong>"<?= htmlspecialchars($displayUrl) ?>"</strong> could not be found.
        </p>

        <a href="/" class="btn btn-primary">
            <i class="material-symbols-rounded align-middle me-1">home</i> Go Home
        </a>
    </div>
</div>
