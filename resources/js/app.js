import Alpine from 'alpinejs';

const startAlpine = () => {
    if (window.Alpine) {
        return;
    }

    window.Alpine = Alpine;
    Alpine.start();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', startAlpine, { once: true });
} else {
    startAlpine();
}
