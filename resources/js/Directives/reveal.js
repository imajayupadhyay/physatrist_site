// v-reveal: fades an element up the first time it scrolls into view.
// Styles for .reveal / .reveal.in live in resources/css/app.css.
let observer;

function getObserver() {
    observer ??= new IntersectionObserver(
        (entries) =>
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in');
                    observer.unobserve(entry.target);
                }
            }),
        { threshold: 0.12 },
    );

    return observer;
}

export default {
    mounted(el) {
        el.classList.add('reveal');
        getObserver().observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};
