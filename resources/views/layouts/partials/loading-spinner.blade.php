<div class="loading-spinner-container show">
    <div class="loading-spinner-overlay"></div>
    <div class="loading-spinner-wrapper">
        <div class="loading-spinner">
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-ring"></div>
            <div class="spinner-logo">
                <img src="{{ asset('img/denr-logo.png') }}" alt="DENR Logo" class="logo-image">
            </div>
        </div>
        <div class="loading-text">
            <span class="loading-dot">L</span>
            <span class="loading-dot">o</span>
            <span class="loading-dot">a</span>
            <span class="loading-dot">d</span>
            <span class="loading-dot">i</span>
            <span class="loading-dot">n</span>
            <span class="loading-dot">g</span>
            <span class="loading-dots">...</span>
        </div>
    </div>
</div>

<style>
    .loading-spinner-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        animation: fadeIn 0.3s ease-in forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
        }
    }

    .loading-spinner-container.hide {
        animation: fadeOut 0.3s ease-out forwards;
    }

    @keyframes fadeOut {
        to {
            opacity: 0;
        }
    }

    .loading-spinner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        backdrop-filter: blur(50px);
        -webkit-backdrop-filter: blur(50px);
    }

    .loading-spinner-wrapper {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
    }

    .loading-spinner {
        position: relative;
        width: 140px;
        height: 140px;
    }

    .spinner-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: logoPulse 2s ease-in-out infinite;
        z-index: 10;
    }

    .logo-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
    }

    @keyframes logoPulse {
        0%, 100% {
            transform: translate(-50%, -50%) scale(1);
        }
        50% {
            transform: translate(-50%, -50%) scale(1.05);
        }
    }

    .spinner-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border: 3px solid transparent;
        border-radius: 50%;
        animation: spin 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }

    .spinner-ring:nth-child(1) {
        animation-delay: 0s;
        border-top-color: #10B981;
        border-right-color: rgba(16, 185, 129, 0.3);
        width: 100%;
        height: 100%;
    }

    .spinner-ring:nth-child(2) {
        animation-delay: 0.2s;
        border-top-color: #3B82F6;
        border-right-color: rgba(59, 130, 246, 0.3);
        width: 85%;
        height: 85%;
        top: 7.5%;
        left: 7.5%;
    }

    .spinner-ring:nth-child(3) {
        animation-delay: 0.4s;
        border-top-color: #059669;
        border-right-color: rgba(5, 150, 105, 0.3);
        width: 70%;
        height: 70%;
        top: 15%;
        left: 15%;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .loading-text {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        gap: 2px;
    }

    .loading-dot {
        animation: letterWave 1.5s ease-in-out infinite;
    }

    .loading-dot:nth-child(1) { animation-delay: 0s; }
    .loading-dot:nth-child(2) { animation-delay: 0.1s; }
    .loading-dot:nth-child(3) { animation-delay: 0.2s; }
    .loading-dot:nth-child(4) { animation-delay: 0.3s; }
    .loading-dot:nth-child(5) { animation-delay: 0.4s; }
    .loading-dot:nth-child(6) { animation-delay: 0.5s; }
    .loading-dot:nth-child(7) { animation-delay: 0.6s; }

    .loading-dots {
        animation: dotsPulse 1.5s ease-in-out infinite;
    }

    @keyframes letterWave {
        0%, 100% {
            transform: translateY(0);
            color: #374151;
        }
        50% {
            transform: translateY(-3px);
            color: #10B981;
        }
    }

    @keyframes dotsPulse {
        0%, 100% {
            opacity: 0.3;
        }
        50% {
            opacity: 1;
        }
    }

    /* Show/hide utility classes */
    .loading-spinner-container.show {
        display: flex !important;
    }

    .loading-spinner-container.hide {
        display: none !important;
    }
</style>

<script>
    // Loading spinner control functions
    function showLoadingSpinner() {
        const spinner = document.querySelector('.loading-spinner-container');
        if (spinner) {
            spinner.classList.add('show');
            spinner.classList.remove('hide');
        }
    }

    function hideLoadingSpinner() {
        const spinner = document.querySelector('.loading-spinner-container');
        if (spinner) {
            spinner.classList.remove('show');
            spinner.classList.add('hide');
        }
    }

    // Auto-hide spinner on page load after animation completes
    document.addEventListener('DOMContentLoaded', function() {
        const spinner = document.querySelector('.loading-spinner-container');
        if (spinner && spinner.classList.contains('show')) {
            // Wait for animation to complete (1.5s per rotation)
            setTimeout(hideLoadingSpinner, 1500);
        }
    });
</script>
