document.addEventListener('DOMContentLoaded', function () {
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
});