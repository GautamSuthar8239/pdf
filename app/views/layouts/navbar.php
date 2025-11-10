 <style>
     /* .material-symbols-rounded {
         font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
         font-size: 24px;
     } */

     .navbar-main {
         background-color: #ffffff;
         backdrop-filter: blur(10px);
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
     }

     /* .navbar-brand {
         outline: none;
         flex-shrink: 0;
     } */

     .navbar-brand img {
         width: 100px;
         /* max-width: 40vw; */
         pointer-events: none;
         user-select: none;
     }

     .navbar-toggler {
         padding: 4px 7px;
     }

     .navbar-toggler:focus {
         box-shadow: none;
     }


     /* Responsive Layout */
     @media (max-width: 992px) {
         .navbar-brand img {
             width: 85px;
             pointer-events: none;
             user-select: none;
         }

         .navbar-nav {
             flex-direction: row !important;
             flex-wrap: wrap !important;
             gap: 5px !important;
             justify-content: center;
         }

     }

     @media (min-width: 992px) {
         .navbar-content {
             display: flex;
             align-items: center;
             width: 100%;
             gap: 1rem;
         }
     }
 </style>

 <!-- Navbar -->
 <nav class="navbar navbar-main navbar-expand-lg py-2 px-1">
     <div class="container-fluid">
         <!-- Logo (Left) -->
         <a class="navbar-brand user-select-none" href="/" draggable="false"
             style="outline: none;">
             <img class="img-fluid" src="<?= ROOT; ?>/assets/images/probid_image.png" alt="Probid Logo" draggable="false">
         </a>

         <div class="animated-banner user-select-none 
             d-flex align-items-center justify-content-center flex-grow-1
             <?= isset($headlineEnabled) && $headlineEnabled === 'off' ? 'd-none' : '' ?>">
             <span id="bannerText"></span>
         </div>

         <!-- Mobile Toggle Button -->
         <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
             <i class="material-symbols-rounded text-orange text-3xl">menu</i>
         </button>

         <!-- Navbar Content -->
         <div class="collapse navbar-collapse" id="mainNavbar">
             <ul class="navbar-nav d-flex flex-row align-items-center gap-1 mb-0 navbar-content">
                 <!-- Right Menu Items (wraps on mobile) -->
                 <ul class="navbar-nav d-flex align-items-md-center gap-2">
                     <!-- Settings -->
                     <li class="nav-item">
                         <!-- <a href="/setting" class="nav-link"> -->
                         <a href="/setDemoToast/setting" class="nav-link">
                             <i class="material-symbols-rounded">settings</i>
                             <span class="d-md-none">Settings</span>
                         </a>
                     </li>

                     <!-- News -->
                     <li class="nav-item">
                         <!-- <a href="/headline" class="nav-link"> -->
                         <a href="/setDemoToast/hello" class="nav-link">
                             <i class="material-symbols-rounded">newsmode</i>
                             <span class="d-md-none">Headlines</span>
                         </a>
                     </li>

                     <!-- Notifications -->
                     <li class="nav-item dropdown">
                         <a href="#" class="nav-link text-dark p-1 d-flex align-items-center nav-hover mb-0"
                             data-bs-toggle="dropdown" aria-expanded="false">
                             <i class="material-symbols-rounded">notifications</i>
                             <span class="d-md-none">Notifications</span>
                         </a>
                         <ul class="dropdown-menu dropdown-menu-end px-1 border-1 shadow border-secondary
                         z-index-1000 position-absolute bg-white overflow-auto border-lavender" aria-labelledby="dropdownMenuButton">

                             <li>
                                 <a href="/setDemoToast/notify" class="dropdown-item border-radius-md d-flex align-items-center gap-2">
                                     <i class="material-symbols-rounded text-orange">pin</i>
                                     Demo notification
                                 </a>
                             </li>
                         </ul>
                     </li>

                     <!-- User Profile -->
                     <li class="nav-item dropdown">
                         <a href="#" id="dropdownInfoButton" data-bs-toggle="dropdown" aria-expanded="false" role="button"
                             class="nav-link d-flex align-items-center px-2 py-1 btn-sm rounded-2 border border-orange gap-2 mb-0">
                             <i class="material-symbols-rounded">person</i>
                             Guest
                         </a>
                         <ul class="dropdown-menu dropdown-menu-end px-1 border-1 shadow border-secondary
                         z-index-1000 position-absolute bg-white overflow-auto border-lavender"
                             aria-labelledby="dropdownInfoButton">
                             <li>
                                 <a class="dropdown-item d-flex align-items-center gap-2 border-radius-md" href="/setDemoToast/about">
                                     <i class="material-symbols-rounded text-orange">info</i> About
                                 </a>
                             </li>
                             <li>
                                 <a class="dropdown-item d-flex align-items-center gap-2 border-radius-md" href="/setDemoToast/help">
                                     <i class="material-symbols-rounded text-orange">help</i> Help
                                 </a>
                             </li>
                         </ul>
                     </li>
                 </ul>
         </div>
     </div>
     </div>
 </nav>