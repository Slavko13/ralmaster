document.addEventListener('DOMContentLoaded', function () {
  initHomeFaq();
  initHomeProjectFilters();
  initRalmasterMotion();
  preserveDialogTriggerScroll();
  labelLegacyControls();
  initLegacyBannerScaler();
  initHeaderBasketCount();
});

function initHomeProjectFilters() {
  const toolbar = document.querySelector('.rm-home-projects__filters');
  if (!toolbar) return;

  const buttons = Array.from(toolbar.querySelectorAll('[data-rm-project-filter]'));
  const cards = Array.from(document.querySelectorAll('.rm-home-project-card[data-rm-project-categories]'));
  if (!buttons.length || !cards.length) return;

  buttons.forEach(function (button) {
    button.addEventListener('click', function () {
      const filter = button.getAttribute('data-rm-project-filter') || 'all';

      buttons.forEach(function (candidate) {
        const active = candidate === button;
        candidate.classList.toggle('is-active', active);
        candidate.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      cards.forEach(function (card) {
        const categories = (card.getAttribute('data-rm-project-categories') || '').split(/\s+/);
        card.hidden = filter !== 'all' && categories.indexOf(filter) === -1;
      });
    });
  });
}

function initHeaderBasketCount() {
  const syncCounts = function () {
    document.querySelectorAll('.rm-header__basket .count, #mobileheader .count').forEach(function (count) {
      const value = Number.parseInt(count.textContent.trim(), 10);
      count.hidden = !Number.isFinite(value) || value <= 0;
    });
  };

  syncCounts();

  const observer = new MutationObserver(syncCounts);
  document.querySelectorAll('.rm-header__basket, #mobileheader').forEach(function (basket) {
    observer.observe(basket, { childList: true, characterData: true, subtree: true });
  });
}

function preserveDialogTriggerScroll() {
  let pendingScroll = null;

  document.addEventListener('click', function (event) {
    const trigger = event.target.closest('[data-rm-discuss-open]');
    if (!trigger) return;

    pendingScroll = { left: window.scrollX, top: window.scrollY };
  }, true);

  document.addEventListener('click', function (event) {
    const trigger = event.target.closest('[data-rm-discuss-open]');
    if (!trigger || !pendingScroll) return;

    const saved = pendingScroll;
    pendingScroll = null;
    const restore = function () { window.scrollTo(saved.left, saved.top); };

    restore();
    window.requestAnimationFrame(function () {
      restore();
      window.requestAnimationFrame(restore);
    });
    window.setTimeout(restore, 0);
    window.setTimeout(restore, 220);
  });
}

function initRalmasterMotion() {
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const root = document.documentElement;
  const hero = document.querySelector('.rm-home-hero');
  const heroItems = hero
    ? Array.from(hero.querySelectorAll('.rm-home-hero__content > h1, .rm-home-hero__content > p, .rm-home-hero__actions, .rm-home-hero__feature'))
    : [];
  const revealGroups = Array.from(
    document.querySelectorAll(
      '[data-rm-section]:not([data-rm-section="hero"]), ' +
      '.rm-products-section, .rm-services-section, .rm-powder-section, ' +
      '.rm-production section, .rm-projects-page section, .rm-contacts-page section'
    )
  );
  const clickableCards = Array.from(
    document.querySelectorAll(
      'a.rm-home-split-card, a.rm-home-product-card, a.rm-home-service-card, ' +
      'a.rm-home-project-card, a.rm-product-card, a.rm-projects-product-card, ' +
      'a.rm-project-card, a.rm-content-card, a.rm-partner-card'
    )
  );

  clickableCards.forEach(function (card) {
    card.classList.add('rm-motion-card');
    if (card.className.indexOf('product') !== -1) card.classList.add('rm-motion-card--product');
  });

  if (reducedMotion || !('IntersectionObserver' in window)) {
    heroItems.concat(revealGroups).forEach(function (item) {
      item.classList.add('rm-reveal', 'is-visible');
    });
    return;
  }

  root.classList.add('rm-motion-ready');
  heroItems.forEach(function (item, index) {
    item.classList.add('rm-reveal', 'rm-reveal--hero');
    item.style.setProperty('--rm-reveal-delay', Math.min(index * 70, 210) + 'ms');
  });
  window.requestAnimationFrame(function () {
    heroItems.forEach(function (item) { item.classList.add('is-visible'); });
  });

  const observer = new IntersectionObserver(
    function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    },
    { threshold: 0.12 }
  );

  revealGroups.forEach(function (group) {
    group.classList.add('rm-reveal');
    const cards = Array.from(
      group.querySelectorAll(
        '.rm-home-split-card, .rm-home-product-card, .rm-home-service-card, ' +
        '.rm-home-project-card, .rm-product-card, .rm-project-card, .rm-content-card'
      )
    );
    cards.forEach(function (card, index) {
      card.style.setProperty('--rm-reveal-delay', Math.min(index * 60, 180) + 'ms');
    });
    observer.observe(group);
  });
}

function labelLegacyControls() {
  const applyLabel = function () {
    const inlineSearch = document.querySelector('.inline-search-block');
    if (inlineSearch) {
      inlineSearch.setAttribute('role', 'search');
      inlineSearch.setAttribute('aria-label', 'Поиск по сайту');
    }

    const scrollToTop = document.getElementById('scrollToTop');
    if (!scrollToTop) return false;

    scrollToTop.setAttribute('aria-label', 'Наверх');
    if (!scrollToTop.closest('.rm-scroll-navigation')) {
      const navigation = document.createElement('nav');
      navigation.className = 'rm-scroll-navigation';
      navigation.setAttribute('aria-label', 'Навигация по странице');
      scrollToTop.parentNode.insertBefore(navigation, scrollToTop);
      navigation.appendChild(scrollToTop);
    }
    return true;
  };

  if (applyLabel()) return;

  const observer = new MutationObserver(function () {
    if (applyLabel()) observer.disconnect();
  });
  observer.observe(document.body, { childList: true });
  window.setTimeout(function () {
    applyLabel();
    observer.disconnect();
  }, 5_000);
}

function initHomeFaq() {
  const items = Array.from(document.querySelectorAll('.rm-home-faq__item'));

  if (!items.length) return;

  items.forEach(function (item) {
    const button = item.querySelector(':scope > button[aria-controls]');
    if (!button) return;

    button.addEventListener('click', function () {
      const willOpen = button.getAttribute('aria-expanded') !== 'true';

      items.forEach(function (candidate) {
        const candidateButton = candidate.querySelector(':scope > button[aria-controls]');
        if (!candidateButton) return;

        const panelId = candidateButton.getAttribute('aria-controls');
        const panel = panelId ? document.getElementById(panelId) : null;
        const isCurrent = candidate === item && willOpen;

        candidate.classList.toggle('is-open', isCurrent);
        candidateButton.setAttribute('aria-expanded', isCurrent ? 'true' : 'false');
        if (panel) panel.hidden = !isCurrent;
      });
    });
  });
}

function initLegacyBannerScaler() {
  const BANNER_SELECTOR = '.banners-big';
  const BREAKPOINT = 768;
  const DIVIDER = 1135;     // граница между сжатием и расширением
  const MAX_SCALE = 1.6;    // потолок расширения (null = без потолка)

  const PRESETS = {
    desktop: { w: 1135, h: 500 },
    mobile:  { w: 768,  h: 1376 },
  };

  const banner = document.querySelector(BANNER_SELECTOR);
  if (!banner) return;

  const wrap = document.createElement('div');
  wrap.className = 'banner-scale-wrap';
  wrap.style.width = '100%';
  wrap.style.overflow = 'hidden';
  banner.parentNode.insertBefore(wrap, banner);
  wrap.appendChild(banner);

  banner.style.transformOrigin = 'top left';

  // общая часть: зафиксировать эталонные размеры под текущий режим
  function applyBase(base) {
    banner.style.width = base.w + 'px';
    banner.style.height = base.h + 'px';
  }

  // ЛОГИКА 1 — СЖАТИЕ (available <= DIVIDER). Без resizeSlider — это и убирает смещение.
  function fitShrink(base, available) {
    const scale = Math.min(available / base.w, 1);
    banner.style.transformOrigin = 'top left';
    banner.style.transform = 'scale(' + scale + ')';
    wrap.style.height = (base.h * scale) + 'px';
  }

  // ЛОГИКА 2 — РАСШИРЕНИЕ (available > DIVIDER).
  function fitExpand(base, available) {
    let scale = available / base.w;
    if (MAX_SCALE) scale = Math.min(scale, MAX_SCALE);
    // центрируем, если упёрлись в потолок и блок уже контейнера
    banner.style.transformOrigin = 'top center';
    banner.style.marginLeft = 'auto';
    banner.style.marginRight = 'auto';
    banner.style.transform = 'scale(' + scale + ')';
    wrap.style.height = (base.h * scale) + 'px';
  }

  function fit() {
    const mode = window.innerWidth <= BREAKPOINT ? 'mobile' : 'desktop';
    const base = PRESETS[mode];
    applyBase(base);

    const available = wrap.clientWidth;

    // разделитель: 1135px решает, какая логика отрабатывает
    if (available <= DIVIDER) {
      fitShrink(base, available);
    } else {
      fitExpand(base, available);
    }
  }

  fit();
  window.addEventListener('resize', fit);
  window.addEventListener('load', fit);
  if (window.ResizeObserver) new ResizeObserver(fit).observe(wrap.parentNode);
}
