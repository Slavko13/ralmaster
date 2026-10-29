document.addEventListener('DOMContentLoaded', function () {
  initHomeFaq();
  labelLegacyControls();
  initLegacyBannerScaler();
});

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
