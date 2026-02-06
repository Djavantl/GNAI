window.closeToast = function() {
    const el = document.getElementById('toast-element');
    const container = document.getElementById('toast-container');
    if (el) {
        el.classList.add('animate-slide-out');
        setTimeout(() => {
            if (container) container.remove();
        }, 400);
    }
};

window.addEventListener('load', function() {
    const el = document.getElementById('toast-element');
    if (el) {
        setTimeout(() => {
            el.classList.add('animate-slide-in');
            setTimeout(() => {
                if (document.getElementById('toast-element')) {
                    window.closeToast();
                }
            }, 5000); 
        }, 300);
    }
});
