(function () {
    const sections = document.querySelectorAll('.scroll-reveal');
    if (!sections.length) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion || !('IntersectionObserver' in window)) {
        sections.forEach((section) => section.classList.add('is-visible'));
        return;
    }

    sections.forEach((section) => section.classList.add('reveal-ready'));

    const style = document.createElement('style');
    style.textContent = `
        .scroll-reveal.reveal-ready {
            opacity: 0;
            transform: translate3d(0, 24px, 0);
            transition: opacity 800ms ease, transform 800ms cubic-bezier(.2, .65, .3, 1);
        }
        .scroll-reveal.reveal-ready.is-visible {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    `;
    document.head.appendChild(style);

    const observer = new IntersectionObserver((entries, currentObserver) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('is-visible');
            currentObserver.unobserve(entry.target);
        });
    }, {
        // Large sections can be taller than the viewport, so a percentage
        // threshold may never be reached. Reveal as soon as any part enters.
        threshold: 0,
        rootMargin: '0px'
    });

    sections.forEach((section) => observer.observe(section));
})();
