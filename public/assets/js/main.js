document.addEventListener('DOMContentLoaded', function () {
    initFlashToast();
});

// Toast Auto-Hide Function
function initFlashToast() {
    const toast = document.getElementById('flashToast');

    if (!toast) return;

    const progressBar = toast.querySelector('.toast-progress-bar');
    const TOAST_DURATION = 5000; // 10 seconds

    // Start progress bar animation
    if (progressBar) {
        // Force reflow to ensure transition works
        progressBar.offsetHeight;
        progressBar.style.transition = `transform ${TOAST_DURATION}ms ease-out`;
        progressBar.style.transform = 'scaleX(0)';
    }

    // Function to hide toast with animation
    const hideToast = (duration = 500) => {
        toast.style.transition = `opacity ${duration}ms ease-out, transform ${duration}ms ease-out`;
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';

        setTimeout(() => {
            const toastContainer = toast.closest('.toast-container-custom');
            if (toastContainer) {
                toastContainer.remove();
            }
        }, duration);
    };

    // Auto-hide after exactly 10 seconds
    setTimeout(() => hideToast(500), TOAST_DURATION);

    // Handle manual close button
    const closeBtn = toast.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            hideToast(300);
        });
    }
}

// Also try to initialize immediately in case DOM is already loaded
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    initFlashToast();
}

// Lottie animation
let lottieContainer = document.querySelector('.lottie-animation');
if (lottieContainer) {
    let animation = lottie.loadAnimation({
        container: lottieContainer,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'https://lottie.host/d987597c-7676-4424-8817-7fca6dc1a33e/BVrFXsaeui.json'
    });
    animation.setSpeed(0.8);
}

let alertHideTimeout = null;

function showTopAlert(message, type = 'info', withConfirm = false, onConfirm = null, duration = 3000) {
    const alertBox = document.getElementById('topAlert');
    if (!alertBox) return;
    const alertInner = alertBox.querySelector('.alert');
    const msg = document.getElementById('topAlertMessage');
    const icon = document.getElementById('topAlertIcon');
    const actions = document.getElementById('topAlertActions');
    const divider = document.getElementById('alertDivider');
    const confirmBtn = document.getElementById('alertConfirmBtn');
    const cancelBtn = document.getElementById('alertCancelBtn');

    // clear previous timer
    if (alertHideTimeout) {
        clearTimeout(alertHideTimeout);
        alertHideTimeout = null;
    }

    // reset classes & animation
    alertInner.className = 'alert rounded-4 border-0 text-center d-flex flex-column align-items-center p-4 mb-0 animate-slide-middle';
    alertInner.style.animation = '';
    alertInner.classList.remove('animate-slide-out');
    icon.className = 'material-symbols-rounded text-6xl m-0';


    // IMPORTANT: use innerHTML so HTML (icons/spinner) renders
    msg.innerHTML = message;

    // type config
    const typeConfig = {
        success: { class: 'alert-success-custom', icon: 'check_circle' },
        error: { class: 'alert-error-custom', icon: 'error' },
        warning: { class: 'alert-warning-custom', icon: 'warning' },
        info: { class: 'alert-info-custom', icon: 'info' }
    };

    const config = typeConfig[type] || typeConfig.info;
    alertInner.classList.add(config.class);
    icon.textContent = config.icon;

    // confirmation mode
    if (withConfirm) {
        actions.classList.remove('d-none');
        actions.classList.add('d-flex', 'justify-content-end', 'gap-2');
        divider?.classList.remove('d-none');
        confirmBtn.classList.remove('bg-light');
        confirmBtn.classList.add('bg-' + type, 'text-white', 'border-white', 'border-1', 'border');

        confirmBtn.onclick = () => {
            hideTopAlert(() => { if (typeof onConfirm === 'function') onConfirm(); });
        };
        cancelBtn.onclick = () => hideTopAlert();
    } else {
        actions.classList.add('d-none');
        actions.classList.remove('d-flex', 'justify-content-end', 'gap-2');
        divider?.classList.add('d-none');
        confirmBtn.classList.add('bg-light');
        confirmBtn.classList.remove('bg-' + type, 'text-white', 'border-white', 'border-1', 'border');
        confirmBtn.onclick = null;
        cancelBtn.onclick = null;
        if (duration > 0) {
            alertHideTimeout = setTimeout(() => { hideTopAlert(); alertHideTimeout = null; }, duration);
        }
    }

    alertBox.classList.remove('d-none');
}

function hideTopAlert(onComplete = null) {
    const alertBox = document.getElementById('topAlert');
    if (!alertBox) return;
    const alertInner = alertBox.querySelector('.alert');

    // clear any pending timer
    if (alertHideTimeout) {
        clearTimeout(alertHideTimeout);
        alertHideTimeout = null;
    }

    alertInner.classList.add('animate-slide-out');
    setTimeout(() => {
        alertBox.classList.add('d-none');
        alertInner.classList.remove('animate-slide-out');
        alertInner.style.animation = '';
        if (typeof onComplete === 'function') onComplete();
    }, 400);
}


