<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-3 mb-4 border-radius-sm"
    style="background-color: #ffffff; backdrop-filter: blur(10px);">

    <div class="container-fluid py-1 px-2 d-flex justify-content-between align-items-center">

        <!-- Left-side Probid logo -->
        <div class="d-flex align-items-center">
            <a href="/">
                <img src="<?= ROOT; ?>/assets/images/probid_image.png" alt="Probid Logo" width="120">
            </a>
        </div>

        <div class="animated-banner">
            <span id="bannerText"></span>
        </div>

        <!-- Right-side Menu -->
        <div class="d-flex align-items-center gap-2">
            <ul class="navbar-nav d-flex flex-row align-items-center gap-2 mb-0">

                <!-- Settings -->
                <li class="nav-item">
                    <a href="/setDemoToast/setting" class="nav-link text-dark p-1 d-flex align-items-center nav-hover">
                        <i class="material-symbols-rounded">settings</i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/headline" class="nav-link text-dark p-1 d-flex align-items-center nav-hover">
                        <i class="material-symbols-rounded">newsmode</i>
                    </a>
                </li>

                <!-- Notifications -->
                <li class="nav-item dropdown">
                    <a href="javascript:;" class="nav-link text-dark p-1 d-flex align-items-center nav-hover"
                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="material-symbols-rounded">notifications</i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-1 shadow-sm border-1 border-secondary"
                        aria-labelledby="dropdownMenuButton">
                        <li>
                            <a href="/setDemoToast/notify" class="dropdown-item border-radius-md d-flex align-items-center gap-2">
                                <i class="material-symbols-rounded text-orange">pin</i> Demo notification
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Simple User Menu -->
                <li class="nav-item dropdown">
                    <a href="#" id="dropdownInfoButton" data-bs-toggle="dropdown" aria-expanded="false"
                        class="nav-link d-flex align-items-center px-2 py-1 btn-sm rounded-2 border border-orange gap-2 mb-0">
                        <i class="material-symbols-rounded">person</i>
                        Guest
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-1 border-1 shadow border-secondary"
                        aria-labelledby="dropdownInfoButton">
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