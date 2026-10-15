(() => {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('.nav-links');

  // Mobile menu toggle
  if (toggle && nav) {
    toggle.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(isOpen));
    });

    nav.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });

    document.addEventListener('click', (e) => {
      if (nav.classList.contains('is-open') && !nav.contains(e.target) && e.target !== toggle) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const targetId = link.getAttribute('href');
      if (targetId && targetId !== '#') {
        const target = document.querySelector(targetId);
        if (target) {
          event.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          history.pushState(null, '', targetId);
        }
      }
    });
  });

  // Active scrollspy
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-links a:not(.nav-cta)');

  if (sections.length && navLinks.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const currentId = '#' + entry.target.getAttribute('id');
          navLinks.forEach((link) => {
            if (link.getAttribute('href') === currentId) {
              link.classList.add('active');
            } else {
              link.classList.remove('active');
            }
          });
        }
      });
    }, {
      rootMargin: '-20% 0px -70% 0px'
    });

    sections.forEach((section) => observer.observe(section));
  }

  // AJAX Form Submission
  const contactForm = document.getElementById('contact-form');
  if (contactForm) {
    const alertBox = document.getElementById('form-alert');
    const submitBtn = document.getElementById('submit-btn');

    contactForm.addEventListener('submit', async (e) => {
      const action = contactForm.getAttribute('action') || '';
      if (action.includes('formsubmit.co')) {
        e.preventDefault();

        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = 'Sending message... <span>⏳</span>';
        }

        try {
          const formData = new FormData(contactForm);
          const response = await fetch('https://formsubmit.co/ajax/modidipen3@gmail.com', {
            method: 'POST',
            headers: {
              'Accept': 'application/json'
            },
            body: formData
          });

          const data = await response.json();

          if (alertBox) {
            alertBox.style.display = 'block';
            if (response.ok && (data.success === 'true' || data.success === true)) {
              alertBox.className = 'form-alert success';
              alertBox.textContent = 'Thanks for reaching out! Your message has been delivered directly to Dipen.';
              contactForm.reset();
            } else {
              alertBox.className = 'form-alert error';
              alertBox.textContent = 'Message could not be sent. Please contact directly on WhatsApp or email.';
            }
          }
        } catch (err) {
          if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.className = 'form-alert error';
            alertBox.textContent = 'Network connection issue. Please email modidipen3@gmail.com or message via WhatsApp.';
          }
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Send message <span>↗</span>';
          }
        }
      }
    });
  }
})();

