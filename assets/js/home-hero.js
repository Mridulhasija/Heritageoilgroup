(() => {
  const hero = document.querySelector('[aria-label="Heritage Drop Oil introduction"]');
  if (hero) {
  const slides = [...hero.querySelectorAll('.oil-hero-slide')];
  if (slides.length >= 2) {
  const dots = [...hero.querySelectorAll('[data-go-to]')];
  const count = hero.querySelector('[data-slide-count]');
  const quote = hero.querySelector('[data-hero-quote]');
  const quotes = [
    'Pure oils. Thoughtfully brought home.',
    'Goodness grown close to nature.',
    'A richer story in every drop.',
    'From trusted hands to your table.',
    'Rooted in heritage. Made for today.'
  ];
  let active = 0;
  let timer;
  const show = (index) => {
    active = (index + slides.length) % slides.length;
    slides.forEach((slide, i) => {
      slide.classList.toggle('opacity-100', i === active);
      slide.classList.toggle('scale-100', i === active);
      slide.classList.toggle('opacity-0', i !== active);
      slide.classList.toggle('scale-[1.03]', i !== active);
    });
    dots.forEach((dot, i) => dot.setAttribute('aria-current', i === active ? 'true' : 'false'));
    if (quote) quote.textContent = quotes[active];
    if (count) count.textContent = `${String(active + 1).padStart(2, '0')} / ${String(slides.length).padStart(2, '0')}`;
  };
  const restart = () => {
    window.clearInterval(timer);
    timer = window.setInterval(() => show(active + 1), 5000);
  };

  hero.querySelector('.oil-hero-previous')?.addEventListener('click', () => { show(active - 1); restart(); });
  hero.querySelector('.oil-hero-next')?.addEventListener('click', () => { show(active + 1); restart(); });
  dots.forEach((dot, index) => dot.addEventListener('click', () => { show(index); restart(); }));
  show(0);
  restart();
  }
  }

  const testimonialCarousel = document.querySelector('[data-testimonial-carousel]');
  if (!testimonialCarousel) return;

  const testimonialSlides = [...testimonialCarousel.querySelectorAll('[data-testimonial-slide]')];
  const testimonialDots = [...testimonialCarousel.querySelectorAll('[data-testimonial-to]')];
  if (testimonialSlides.length < 2) return;

  let testimonialActive = 0;
  let testimonialTimer;
  const showTestimonial = (index) => {
    testimonialActive = (index + testimonialSlides.length) % testimonialSlides.length;
    testimonialSlides.forEach((slide, slideIndex) => {
      const selected = slideIndex === testimonialActive;
      slide.classList.toggle('translate-y-0', selected);
      slide.classList.toggle('opacity-100', selected);
      slide.classList.toggle('pointer-events-none', !selected);
      slide.classList.toggle('translate-y-2', !selected);
      slide.classList.toggle('opacity-0', !selected);
      slide.setAttribute('aria-hidden', selected ? 'false' : 'true');
    });
    testimonialDots.forEach((dot, dotIndex) => dot.setAttribute('aria-current', dotIndex === testimonialActive ? 'true' : 'false'));
  };
  const restartTestimonials = () => {
    window.clearInterval(testimonialTimer);
    testimonialTimer = window.setInterval(() => showTestimonial(testimonialActive + 1), 5000);
  };

  testimonialCarousel.querySelector('[data-testimonial-prev]')?.addEventListener('click', () => {
    showTestimonial(testimonialActive - 1);
    restartTestimonials();
  });
  testimonialCarousel.querySelector('[data-testimonial-next]')?.addEventListener('click', () => {
    showTestimonial(testimonialActive + 1);
    restartTestimonials();
  });
  testimonialDots.forEach((dot, index) => dot.addEventListener('click', () => {
    showTestimonial(index);
    restartTestimonials();
  }));
  showTestimonial(0);
  restartTestimonials();
})();
