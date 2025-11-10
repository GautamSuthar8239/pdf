<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?? 'Probid' ?></title>
    <link rel="icon" type="image/x-icon" href="<?= ROOT; ?>/assets/images/favicon.ico">

    <link rel="icon" type="image/png" sizes="32x32" href="<?= ROOT; ?>/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= ROOT; ?>/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= ROOT; ?>/assets/images/apple-touch-icon.png">

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"> -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link id="pagestyle" href="<?= ROOT; ?>/assets/css/material-dashboard.css" rel="stylesheet" />
    <link href="<?= ROOT; ?>/assets/css/styles.css" rel="stylesheet" />
</head>

<body class="bg-gray-100">

    <?php if (!isset($showLayout) || $showLayout): ?>
        <main class="main-content position-relative h-100 ">
            <?php require 'navbar.php'; ?>
            <div class="content-area px-4">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="d-flex align-items-center">
                    <ol class="breadcrumb bg-transparent mb-0 p-0">
                        <?php foreach ($breadcrumb as $i => $crumb): ?>
                            <?php if ($i === array_key_last($breadcrumb)): ?>
                                <li class="breadcrumb-item text-sm fw-semibold" style="color: #ff6b35;">
                                    <?= $crumb ?>
                                </li>
                            <?php else: ?>
                                <li class="breadcrumb-item text-sm">
                                    <a class="text-decoration-none fw-medium" style="color: #6c757d;"
                                        href="/<?= lcfirst($crumb) ?>">
                                        <?= $crumb ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
                <!-- Your page content goes here -->
            <?php elseif ($title === 'NotFound'): ?>
                <main class="main-content position-relative h-100">
                <?php else: ?>
                    <main class="main-content position-relative h-100 px-4">
                    <?php endif; ?>