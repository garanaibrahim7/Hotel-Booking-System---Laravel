document.addEventListener('DOMContentLoaded', () => {

    // =========================
    // COUNTER SETUP
    // =========================
    const counters = [
        { el: document.getElementById('projects'), target: 84000 },
        { el: document.getElementById('backers'), target: 10000000 },
        { el: document.getElementById('categories'), target: 2000 },
        { el: document.getElementById('funds'), target: 100000000 }
    ].filter(item => item.el !== null); // 🔥 prevents crashes

    let started = false;

    function formatNumber(num) {
        if (num >= 1_000_000) return Math.floor(num / 1_000_000) + 'M';
        if (num >= 1_000) return Math.floor(num / 1_000) + 'K';
        return num;
    }

    function startCounter() {
        if (started || counters.length === 0) return;
        started = true;

        const interval = setInterval(() => {
            let completed = true;

            counters.forEach(({ el, target }) => {
                let current = Number(el.dataset.count || 0);
                let step = Math.ceil(target / 100);

                if (current < target) {
                    current = Math.min(current + step, target);
                    el.dataset.count = current;
                    el.innerText = formatNumber(current);
                    completed = false;
                }
            });

            if (completed) clearInterval(interval);

        }, 20);
    }

    // =========================
    // INTERSECTION OBSERVER
    // =========================
    const achievementSection = document.querySelector('.achievement');

    if (achievementSection) {
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                startCounter();
                observer.disconnect(); // 🔥 run only once (best practice)
            }
        });

        observer.observe(achievementSection);
    }

    // =========================
    // TESTIMONIAL SLIDER
    // =========================
    const slider = document.querySelector('.testimonial-row');

    if (slider) {
        const row = slider.querySelector('.row');

        if (row && row.children.length > 0) {

            let index = 0;

            function getCardWidth() {
                const card = row.children[0];
                return card.getBoundingClientRect().width;
            }

            function autoSlide() {
                const cardWidth = getCardWidth();
                const visibleWidth = slider.clientWidth;
                const totalWidth = row.scrollWidth;

                index++;

                let nextScroll = index * cardWidth;

                if (nextScroll + visibleWidth > totalWidth) {
                    index = 0;
                    nextScroll = 0;
                }

                slider.scrollTo({
                    left: nextScroll,
                    behavior: "smooth"
                });
            }

            setInterval(autoSlide, 3000);
        }
    }

});