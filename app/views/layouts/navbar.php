<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg d-flex flex-sm-nowrap px-0 navbar-expand-sm px-3 border-radius-sm"
    style="background-color: #ffffff; backdrop-filter: blur(10px);">

    <a class="navbar-brand user-select-none d-flex align-items-center"
        href="/"
        draggable="false"
        style="outline: none;">

        <img src="<?= ROOT; ?>/assets/images/probid_image.png"
            alt="Probid Logo"
            class="img-fluid"
            style="
            width: 100px;
            max-width: 40vw;
            pointer-events: none;
            user-select: none;
        "
            draggable="false">
    </a>


    <div class="container-fluid py-0 d-flex justify-content-between align-items-center">

        <div class="animated-banner user-select-none <?= isset($headlineEnabled) && $headlineEnabled === 'off' ? 'd-none' : '' ?>">
            <span id="bannerText"></span>
        </div>


        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar">
            <i class="material-symbols-rounded">menu</i>
        </button>

        <!-- Collapsible Menu -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav d-flex align-items-center flex-row gap-2 w-100 justify-content-center justify-content-lg-end">

                <li class="nav-item">
                    <a href="/setDemoToast/setting" class="nav-link text-dark">
                        <i class="material-symbols-rounded">settings</i>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/headline" class="nav-link text-dark">
                        <i class="material-symbols-rounded">newsmode</i>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link text-dark" data-bs-toggle="dropdown">
                        <i class="material-symbols-rounded">notifications</i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-1 shadow-sm">
                        <li>
                            <a href="/setDemoToast/notify" class="dropdown-item d-flex align-items-center gap-2">
                                <i class="material-symbols-rounded text-orange">pin</i> Demo notification
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link border border-orange rounded px-2 py-1 d-flex align-items-center gap-2"
                        data-bs-toggle="dropdown">
                        <i class="material-symbols-rounded">person</i>
                        Guest
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-1 shadow-sm">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="/setDemoToast/about">
                                <i class="material-symbols-rounded text-orange">info</i> About
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="/setDemoToast/help">
                                <i class="material-symbols-rounded text-orange">help</i> Help
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->