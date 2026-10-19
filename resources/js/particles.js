// Background particles: small grey dots drifting slowly behind the page.
// Near the pointer they move aside a little and draw faint lines to it.
// Colours come from the theme tokens (--muted, --rule), so light/dark just work.
// With prefers-reduced-motion the dots are drawn once and stay still.

const LINK_DISTANCE = 120; // px between pointer and dot for a connecting line
const PUSH_RADIUS = 90; // px around the pointer where dots move aside
const MAX_SPEED = 0.25; // px per frame at 60fps

export function startParticles(canvas) {
    const context = canvas.getContext('2d');
    if (!context) return;

    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const darkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    const pointer = { x: -9999, y: -9999, active: false };
    let particles = [];
    let colours = {};
    let width = 0;
    let height = 0;
    let frame = null;

    function readColours() {
        const styles = getComputedStyle(document.documentElement);
        colours = {
            dot: styles.getPropertyValue('--muted').trim() || '#6b6b6b',
            line: styles.getPropertyValue('--muted').trim() || '#6b6b6b',
        };
    }

    function resize() {
        const ratio = Math.min(window.devicePixelRatio || 1, 2);
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = Math.round(width * ratio);
        canvas.height = Math.round(height * ratio);
        context.setTransform(ratio, 0, 0, ratio, 0, 0);

        // About one dot per 16 000 px², between 20 and 90.
        const count = Math.max(20, Math.min(90, Math.round((width * height) / 16000)));
        particles = Array.from({ length: count }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * MAX_SPEED * 2,
            vy: (Math.random() - 0.5) * MAX_SPEED * 2,
            r: Math.random() * 1.2 + 0.6,
            alpha: Math.random() * 0.35 + 0.2,
        }));
        draw();
    }

    function step() {
        for (const p of particles) {
            if (pointer.active) {
                const dx = p.x - pointer.x;
                const dy = p.y - pointer.y;
                const distance = Math.hypot(dx, dy);
                if (distance < PUSH_RADIUS && distance > 0.01) {
                    const force = (1 - distance / PUSH_RADIUS) * 0.6;
                    p.x += (dx / distance) * force;
                    p.y += (dy / distance) * force;
                }
            }
            p.x += p.vx;
            p.y += p.vy;

            // Wrap around the edges so the density stays even.
            if (p.x < -10) p.x = width + 10;
            if (p.x > width + 10) p.x = -10;
            if (p.y < -10) p.y = height + 10;
            if (p.y > height + 10) p.y = -10;
        }
    }

    function draw() {
        context.clearRect(0, 0, width, height);

        if (pointer.active) {
            context.strokeStyle = colours.line;
            context.lineWidth = 0.6;
            for (const p of particles) {
                const distance = Math.hypot(p.x - pointer.x, p.y - pointer.y);
                if (distance < LINK_DISTANCE) {
                    context.globalAlpha = (1 - distance / LINK_DISTANCE) * 0.35;
                    context.beginPath();
                    context.moveTo(p.x, p.y);
                    context.lineTo(pointer.x, pointer.y);
                    context.stroke();
                }
            }
        }

        context.fillStyle = colours.dot;
        for (const p of particles) {
            context.globalAlpha = p.alpha;
            context.beginPath();
            context.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            context.fill();
        }
        context.globalAlpha = 1;
    }

    function loop() {
        step();
        draw();
        frame = requestAnimationFrame(loop);
    }

    function start() {
        if (frame === null && !motion.matches && !document.hidden) frame = requestAnimationFrame(loop);
    }

    function stop() {
        if (frame !== null) cancelAnimationFrame(frame);
        frame = null;
    }

    window.addEventListener('pointermove', (event) => {
        if (event.pointerType !== 'mouse') return; // no trails under a finger while scrolling
        pointer.x = event.clientX;
        pointer.y = event.clientY;
        pointer.active = !motion.matches;
    });
    document.documentElement.addEventListener('pointerleave', () => {
        pointer.active = false;
        if (frame === null) draw();
    });
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
    motion.addEventListener('change', () => {
        stop();
        pointer.active = false;
        draw();
        start();
    });

    // Theme changes: the toggle sets data-theme, the system can switch on its own.
    const refresh = () => {
        readColours();
        draw();
    };
    new MutationObserver(refresh).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    darkScheme.addEventListener('change', refresh);

    let resizeTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(resize, 150);
    });

    readColours();
    resize();
    start();
}
