<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/icon-180.png') }}">
<meta name="theme-color" content="#16a4e5">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Absensi">

<style>
    #pwaInstallButton {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        z-index: 9999;
        border: 0;
        border-radius: 9999px;
        padding: 0.75rem 1rem;
        background: #16a4e5;
        color: #ffffff;
        font: 600 0.875rem/1.25rem system-ui, sans-serif;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25);
        cursor: pointer;
    }

    #pwaInstallButton[hidden] {
        display: none;
    }
</style>

<script>
    (() => {
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('service-worker.js') }}", { scope: '/' })
                    .catch((error) => console.error('Service worker gagal didaftarkan:', error));
            });
        }

        let installPrompt = null;
        let installButton = null;

        const createInstallButton = () => {
            installButton = document.createElement('button');
            installButton.type = 'button';
            installButton.id = 'pwaInstallButton';
            installButton.textContent = 'Pasang Aplikasi';
            installButton.hidden = installPrompt === null;
            document.body.appendChild(installButton);

            installButton.addEventListener('click', async () => {
                if (!installPrompt) return;

                installPrompt.prompt();
                await installPrompt.userChoice;
                installPrompt = null;
                installButton.hidden = true;
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', createInstallButton);
        } else {
            createInstallButton();
        }

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            installPrompt = event;
            if (installButton) installButton.hidden = false;
        });

        window.addEventListener('appinstalled', () => {
            installPrompt = null;
            if (installButton) installButton.hidden = true;
        });
    })();
</script>
