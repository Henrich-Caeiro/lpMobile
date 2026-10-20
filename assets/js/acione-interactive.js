/**
 * Acione Mobile - Interactive Frontend Engine
 * Handles telemetry animations, tabs, accordion, odometer counter, and modal triggers.
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Floating Header Elevation on Scroll
  const header = document.querySelector('.site-header');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      header?.classList.add('scrolled');
    } else {
      header?.classList.remove('scrolled');
    }
  }, { passive: true });

  // 2. Mobile Drawer Toggle
  const mobileToggle = document.querySelector('.mobile-toggle-btn');
  const mobileMenu = document.querySelector('.mobile-menu-drawer-wrapper');
  const mobileClose = document.querySelector('.mobile-drawer-close');

  mobileToggle?.addEventListener('click', () => {
    mobileMenu?.classList.toggle('active');
    document.body.classList.toggle('menu-open');
  });

  mobileClose?.addEventListener('click', () => {
    mobileMenu?.classList.remove('active');
    document.body.classList.remove('menu-open');
  });

  // 3. Telemetry HUD Step Switcher
  const stepNavItems = document.querySelectorAll('.step-nav-item');
  const telemetryTitle = document.querySelector('.telemetry-step-title');
  const telemetryDesc = document.querySelector('.telemetry-step-desc');
  const telemetryVideo = document.querySelector('.telemetry-video-container video');
  const telemetrySource = telemetryVideo?.querySelector('source');

  const stepsData = [
    {
      title: "Detecção e Abertura em 15 Segundos",
      desc: "Motorista aciona pelo app ou central com 1 clique. O sistema capta geolocalização exata na rodovia (KM, sentido, acostamento) e categoriza a pane automaticamente.",
      video: "assets/videos/vid_3-1.mp4",
      coords: "LAT: -23.5489° S | LNG: -46.6388° W | KM 284 BR-116",
      status: "CHAMADO EM ABERTO • LOCALIZADO"
    },
    {
      title: "Roteamento Inteligente do Socorrista",
      desc: "O algoritmo rastreia os prestadores credenciados em um raio estratégico e despacha o profissional mais qualificado e disponível com menor tempo de deslocamento.",
      video: "assets/videos/vid_3-2.mp4",
      coords: "LAT: -23.5120° S | LNG: -46.6012° W | VEL: 68 KM/H",
      status: "SOCORRISTA EM ROTA • ETA 18 MIN"
    },
    {
      title: "Monitoramento em Tempo Real",
      desc: "A transportadora e o motorista acompanham o trajeto do prestador no mapa com telemetria viva, eliminando a angústia da espera e mantendo a carga segura.",
      video: "assets/videos/vid_3-3.mp4",
      coords: "LAT: -23.5480° S | LNG: -46.6375° W | NO LOCAL",
      status: "ATENDIMENTO EM ANDAMENTO"
    },
    {
      title: "Validação Digital & Faturamento Transparente",
      desc: "Checklist fotográfico do reparo, laudo digital e aprovação em tempo real. Sem cobranças abusivas na estrada: tabela padronizada e faturamento direto com a transportadora.",
      video: "assets/videos/vid_3-5.mp4",
      coords: "SERVIÇO HOMOLOGADO • CAMINHÃO LIBERADO",
      status: "CONCLUÍDO COM SUCESSO"
    }
  ];

  stepNavItems.forEach((item, index) => {
    item.addEventListener('click', () => {
      stepNavItems.forEach(i => i.classList.remove('active'));
      item.classList.add('active');

      const data = stepsData[index];
      if (data) {
        if (telemetryTitle) telemetryTitle.textContent = data.title;
        if (telemetryDesc) telemetryDesc.textContent = data.desc;
        
        const coordsEl = document.querySelector('.telemetry-live-coords');
        if (coordsEl) coordsEl.textContent = data.coords;

        const badgeEl = document.querySelector('.telemetry-top-bar .tech-title');
        if (badgeEl) {
          badgeEl.innerHTML = `<span class="pulse-dot lime"></span> ${data.status}`;
        }

        if (telemetryVideo && telemetrySource && window.AcioneThemeUri) {
          telemetrySource.src = `${window.AcioneThemeUri}/${data.video}`;
          telemetryVideo.load();
          telemetryVideo.play().catch(() => {});
        }
      }
    });
  });

  // Simulated live telemetry coordinate jitter for authenticity
  const coordsDisplay = document.querySelector('.telemetry-live-coords');
  if (coordsDisplay) {
    setInterval(() => {
      const activeIdx = Array.from(stepNavItems).findIndex(el => el.classList.contains('active'));
      if (activeIdx !== -1 && stepsData[activeIdx]) {
        const base = stepsData[activeIdx].coords;
        if (base.includes('LAT')) {
          const jitter = (Math.random() * 0.0006 - 0.0003).toFixed(4);
          coordsDisplay.textContent = base.replace(/-23\.5\d+/, `-23.54${Math.floor(Math.random() * 90 + 10)}`);
        }
      }
    }, 2500);
  }

  // 4. Platform Tabs Switcher
  const tabBtns = document.querySelectorAll('.tab-btn');
  const tabPanes = document.querySelectorAll('.tab-pane');

  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-tab');
      
      tabBtns.forEach(b => b.classList.remove('active'));
      tabPanes.forEach(p => p.classList.remove('active'));

      btn.classList.add('active');
      const activePane = document.getElementById(targetId);
      if (activePane) {
        activePane.classList.add('active');
      }
    });
  });

  // 5. FAQ Accordion
  const accordionItems = document.querySelectorAll('.accordion-item');

  accordionItems.forEach(item => {
    const trigger = item.querySelector('.accordion-trigger');
    trigger?.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      
      // Close all
      accordionItems.forEach(i => i.classList.remove('open'));
      
      // If it wasn't open, open it
      if (!isOpen) {
        item.classList.add('open');
      }
    });
  });

  // 6. Metrics Animated Counters
  const metricNumbers = document.querySelectorAll('.metric-number[data-target]');
  let hasAnimatedMetrics = false;

  const countUp = (el, target, duration = 1800) => {
    let start = 0;
    const stepTime = Math.abs(Math.floor(duration / target));
    const timer = setInterval(() => {
      start += Math.ceil(target / 40);
      if (start >= target) {
        el.textContent = target;
        clearInterval(timer);
      } else {
        el.textContent = start;
      }
    }, 30);
  };

  const metricsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !hasAnimatedMetrics) {
        hasAnimatedMetrics = true;
        metricNumbers.forEach(numEl => {
          const target = parseInt(numEl.getAttribute('data-target'), 10);
          if (!isNaN(target) && target > 0) {
            countUp(numEl, target);
          }
        });
      }
    });
  }, { threshold: 0.3 });

  const metricsSection = document.querySelector('.metrics-section');
  if (metricsSection) {
    metricsObserver.observe(metricsSection);
  }

  // 7. Modal Form Handler
  const modal = document.querySelector('.modal-backdrop');
  const openModalTriggers = document.querySelectorAll('.open-modal-trigger');
  const modalCloseBtn = document.querySelector('.modal-close');
  const demoForm = document.getElementById('demo-request-form');

  openModalTriggers.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      modal?.classList.add('open');
      document.body.style.overflow = 'hidden';
    });
  });

  const closeModal = () => {
    modal?.classList.remove('open');
    document.body.style.overflow = '';
  };

  modalCloseBtn?.addEventListener('click', closeModal);

  modal?.addEventListener('click', (e) => {
    if (e.target === modal) {
      closeModal();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal?.classList.contains('open')) {
      closeModal();
    }
  });

  demoForm?.addEventListener('submit', (e) => {
    e.preventDefault();
    const submitBtn = demoForm.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.textContent = 'Processando Chamado...';
      submitBtn.disabled = true;
      setTimeout(() => {
        demoForm.innerHTML = `
          <div style="text-align: center; padding: 2rem 1rem;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem;">✅</div>
            <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: #fff;">Solicitação Recebida!</h3>
            <p style="color: rgba(255,255,255,0.8); line-height: 1.5;">Nossa central 24h já recebeu seus dados e nossa equipe técnica entrará em contato em menos de 5 minutos.</p>
            <button type="button" class="btn-header btn-lime" style="margin-top: 1.5rem;" onclick="document.querySelector('.modal-backdrop').classList.remove('open'); document.body.style.overflow='';">Fechar</button>
          </div>
        `;
      }, 1000);
    }
  });
});

/* ==========================================================================
   HERO SEQUENCE — 410 frames WebP desenhados em <canvas> e controlados pelo
   scroll + títulos letra a letra (mesma lógica do "video-carousel" do
   Terminal Industries: progress -> frame index; chars lime -> branco -> fade).
   ========================================================================== */
(function () {
  const hero = document.getElementById('hero-seq');
  const canvas = document.getElementById('hero-canvas');
  if (!hero || !canvas) return;

  const ctx = canvas.getContext('2d');
  const media = hero.querySelector('.hero-seq-media');
  const stage = hero.querySelector('.hero-seq-stage');
  const cta = hero.querySelector('.hero-seq-cta');
  const bar = document.getElementById('hero-progress-bar');
  const hint = hero.querySelector('.hero-seq-scroll-hint');
  const titles = Array.from(hero.querySelectorAll('.hero-seq-title')).map((el) => ({
    el,
    chars: Array.from(el.querySelectorAll('.hs-ch')),
    active: false,
  }));

  const clamp = (n, a = 0, b = 1) => Math.min(b, Math.max(a, n));
  const isMobile = window.matchMedia('(max-width: 768px)').matches;

  const base = isMobile
    ? (canvas.dataset.mobileBase || (canvas.dataset.mobile || '').replace('{index}.webp', ''))
    : (canvas.dataset.desktopBase || (canvas.dataset.desktop || '').replace('{index}.webp', ''));
  const total = parseInt(isMobile ? canvas.dataset.mobileCount : canvas.dataset.desktopCount, 10) || (isMobile ? 409 : 410);

  const getUrl = (i) => {
    if (base) return `${base}${i}.webp`;
    const tpl = isMobile ? canvas.dataset.mobile : canvas.dataset.desktop;
    return (tpl || '').replace('{index}', i);
  };

  /* ---------- Frames: pré-carregamento progressivo inteligente ---------- */
  const frames = new Array(total);
  const loading = new Set();
  let loadedCount = 0;

  const load = (i) => {
    if (i < 0 || i >= total || frames[i] || loading.has(i)) return Promise.resolve(frames[i] || null);
    loading.add(i);
    return new Promise((res) => {
      const img = new Image();
      img.onload = () => {
        frames[i] = img;
        loading.delete(i);
        loadedCount++;
        // Se este frame é muito próximo do frame atual na tela, redesenha
        const curIdx = Math.floor(clamp(progress) * (total - 1));
        if (Math.abs(curIdx - i) <= 1) {
          draw(true);
        }
        res(img);
      };
      img.onerror = () => {
        loading.delete(i);
        res(null);
      };
      img.src = getUrl(i);
    });
  };

  // Carrega frame 0 imediatamente para exibição instantânea
  load(0).then(() => {
    draw(true);
    startProgressiveLoading();
  });

  const startProgressiveLoading = async () => {
    // Passada 1: Stride 8 (apenas ~51 frames) - permite navegação completa em frações de segundo
    for (let i = 0; i < total; i += 8) {
      await load(i);
    }
    // Passada 2: Stride 4
    for (let i = 4; i < total; i += 8) {
      await load(i);
    }
    // Passada 3: Stride 2
    for (let i = 2; i < total; i += 4) {
      await load(i);
    }
    // Passada 4: Todos os restantes
    for (let i = 1; i < total; i += 2) {
      await load(i);
    }
  };

  /* ---------- Canvas e Viewport ---------- */
  let cw = 0, ch = 0, lastIdx = -1;
  const resize = () => {
    const dpr = Math.min(window.devicePixelRatio || 1, 2);
    cw = stage.offsetWidth;
    ch = stage.offsetHeight;
    canvas.width = Math.round(cw * dpr);
    canvas.height = Math.round(ch * dpr);
    canvas.style.width = cw + 'px';
    canvas.style.height = ch + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    ctx.imageSmoothingEnabled = true;
    lastIdx = -1;
    draw(true);
  };

  const nearest = (idx) => {
    if (frames[idx]) return frames[idx];
    // Prioriza download do frame faltante sob demanda
    load(idx);
    if (idx + 1 < total) load(idx + 1);
    if (idx - 1 >= 0) load(idx - 1);

    for (let d = 1; d < total; d++) {
      if (idx - d >= 0 && frames[idx - d]) return frames[idx - d];
      if (idx + d < total && frames[idx + d]) return frames[idx + d];
    }
    return null;
  };

  let progress = 0;
  const MAX_FRAME = Math.min(total - 1, isMobile ? 375 : 380);

  function draw(force) {
    if (!cw || !ch) return;
    const idx = Math.floor(clamp(progress) * MAX_FRAME);
    if (idx === lastIdx && !force) return;
    const img = nearest(idx);
    if (!img) return;
    lastIdx = idx;

    const imgW = img.naturalWidth || 1920;
    const imgH = img.naturalHeight || 1080;
    const scale = Math.max(cw / imgW, ch / imgH);
    const w = imgW * scale;
    const h = imgH * scale;
    const x = (cw - w) / 2;
    const y = (ch - h) / 2;

    ctx.clearRect(0, 0, cw, ch);
    ctx.drawImage(img, x, y, w, h);
  }

  /* ---------- Títulos: Revelação estilo SplitText Terminal Industries ---------- */
  const powOut = (x) => 1 - (1 - x) * (1 - x);
  const powIn = (x) => x * x;
  const TITLES_SPAN = 0.92;

  const renderTitles = (p) => {
    const t = (p - 0.03) / TITLES_SPAN;
    const d = Math.floor(t * titles.length);
    const C = t * titles.length - d;

    titles.forEach((tt, i) => {
      if (i === d && t >= 0 && d < titles.length) return;
      if (tt.active) {
        tt.active = false;
        tt.chars.forEach((c) => {
          c.style.opacity = '0';
          c.style.color = 'var(--c-lime)';
        });
      }
    });

    if (t < 0 || d >= titles.length) return;
    const cur = titles[d];
    if (!cur) return;
    cur.active = true;

    const gap = 0.1;
    const half = 0.5;
    const A = half + gap;
    const G = Math.min(C / half, 1);
    let L = (C - A) / (1 - A);
    L *= 2;
    const Y = clamp(L);
    const n = cur.chars.length || 1;

    for (let i = 0; i < n; i++) {
      const c = cur.chars[i];
      const q = i / n, o = (i + 1) / n;
      const B = clamp((G - q) / (o - q));
      const Z = clamp((Y - q) / (o - q));
      const op = powOut(B) * (1 - powIn(Z));
      c.style.opacity = op.toFixed(3);
      c.style.color = B < 1 ? 'var(--c-lime)' : '#ffffff';
    }
  };

  /* ---------- Scroll loop de alta performance sem Layout Thrashing ---------- */
  let heroTop = 0;
  let heroHeight = 0;
  let winHeight = window.innerHeight;

  const measure = () => {
    const rect = hero.getBoundingClientRect();
    heroTop = rect.top + window.scrollY;
    heroHeight = hero.offsetHeight;
    winHeight = window.innerHeight;
  };

  const getProgress = () => {
    const span = heroHeight - winHeight;
    if (span <= 0) return 0;
    return clamp((window.scrollY - heroTop) / span);
  };

  let target = 0;
  let cur = 0;
  let raf = null;

  const render = () => {
    progress = cur;
    draw();
    renderTitles(cur);

    const c = clamp((cur - 0.75) / 0.15);
    cta.style.opacity = c.toFixed(3);
    cta.style.transform = `translateY(${((1 - c) * 20).toFixed(1)}px)`;
    cta.classList.toggle('visible', c > 0.4);

    if (bar) bar.style.transform = `scaleY(${cur.toFixed(4)})`;
    hero.classList.toggle('is-scrolled', cur > 0.01);

    // Parallax de saída sutil (sobe junto com o bloco seguinte sem expor o fundo preto)
    const scrollPastHero = (window.scrollY + winHeight) - (heroTop + heroHeight);
    const out = clamp(scrollPastHero / winHeight);
    media.style.transform = out > 0 ? `translate3d(0, ${(-out * 20).toFixed(2)}%, 0)` : 'translate3d(0, 0, 0)';
  };

  const tick = () => {
    cur += (target - cur) * 0.25;
    if (Math.abs(target - cur) < 0.0004) cur = target;
    render();
    raf = cur === target ? null : requestAnimationFrame(tick);
  };

  const onScroll = () => {
    target = getProgress();
    if (!raf) raf = requestAnimationFrame(tick);
  };

  /* ---------- Indicador "scroll to explore" que segue o mouse ---------- */
  if (hint && window.matchMedia('(pointer: fine)').matches) {
    let mx = window.innerWidth / 2, my = window.innerHeight * 0.8, hx = mx, hy = my;
    window.addEventListener('mousemove', (e) => {
      mx = e.clientX;
      my = e.clientY;
      hint.style.opacity = '';
    }, { passive: true });
    const follow = () => {
      hx += (mx + 24 - hx) * 0.12;
      hy += (my - hy) * 0.12;
      hint.style.transform = `translate(${hx.toFixed(1)}px, ${hy.toFixed(1)}px)`;
      requestAnimationFrame(follow);
    };
    follow();
  } else if (hint) {
    hint.style.transform = `translate(1.5rem, ${window.innerHeight - 90}px)`;
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', () => {
    measure();
    resize();
    onScroll();
  }, { passive: true });

  measure();
  resize();
  target = getProgress();
  cur = target;
  render();
})();

/* ==========================================================================
   FOOTER — reveal on scroll
   ========================================================================== */
(function () {
  const els = document.querySelectorAll('.footer-reveal, [data-reveal]');
  if (!els.length) return;
  if (!('IntersectionObserver' in window)) {
    els.forEach((e) => e.classList.add('in-view'));
    return;
  }
  const io = new IntersectionObserver((entries) => {
    entries.forEach((en, i) => {
      if (en.isIntersecting) {
        setTimeout(() => en.target.classList.add('in-view'), i * 120);
        io.unobserve(en.target);
      }
    });
  }, { threshold: 0.15 });
  els.forEach((e) => io.observe(e));
})();

/* ==========================================================================
   BUTTON-TILT — character scramble flicker wave + 3D subtle magnetic tilt
   (Terminal Industries exact CTA behavior)
   ========================================================================== */
(function () {
  const initButtons = () => {
    document.querySelectorAll('.button-tilt').forEach((btn) => {
      const textEl = btn.querySelector('.button-tilt-text');
      if (!textEl || btn.dataset.tiltInited) return;
      btn.dataset.tiltInited = 'true';

      const raw = textEl.textContent.trim();
      textEl.innerHTML = '';
      const lineSpan = document.createElement('span');
      lineSpan.className = 'bt-line';

      const chars = [];
      for (const ch of raw) {
        const span = document.createElement('span');
        span.className = 'bt-char';
        span.textContent = ch === ' ' ? '\u00A0' : ch;
        lineSpan.appendChild(span);
        chars.push(span);
      }
      textEl.appendChild(lineSpan);

      let timeouts = [];
      const clearTimeouts = () => {
        timeouts.forEach(clearTimeout);
        timeouts = [];
        chars.forEach((c) => c.classList.remove('is-flash'));
      };

      btn.addEventListener('mouseenter', () => {
        clearTimeouts();
        chars.forEach((c, i) => {
          if (i % 2 === 0) {
            const t1 = setTimeout(() => {
              c.classList.add('is-flash');
              const t2 = setTimeout(() => c.classList.remove('is-flash'), 160);
              timeouts.push(t2);
            }, i * 22);
            timeouts.push(t1);
          }
        });
      });

      btn.addEventListener('mouseleave', () => {
        clearTimeouts();
        btn.style.transform = '';
        const rev = [...chars].map((c, i) => ({ c, i })).filter((x) => x.i % 2 === 1).reverse();
        rev.forEach(({ c }, n) => {
          const t1 = setTimeout(() => {
            c.classList.add('is-flash');
            const t2 = setTimeout(() => c.classList.remove('is-flash'), 160);
            timeouts.push(t2);
          }, n * 22);
          timeouts.push(t1);
        });
      });

      // Subtle magnetic tilt on desktop
      if (window.matchMedia('(pointer: fine)').matches) {
        btn.addEventListener('mousemove', (e) => {
          const rect = btn.getBoundingClientRect();
          const relX = e.clientX - (rect.left + rect.width / 2);
          const relY = e.clientY - (rect.top + rect.height / 2);
          btn.style.transform = `translate(${(relX * 0.12).toFixed(1)}px, ${(relY * 0.12).toFixed(1)}px)`;
        });
      }
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initButtons);
  } else {
    initButtons();
  }
})();

// ==========================================================================
// 10. Background Circuit Lines with Moving Points of Light (Terminal Industries signature)
// ==========================================================================
(function() {
  function roundedPathString(pts) {
    if (!pts || pts.length < 2) return '';
    let d = `M ${pts[0].x.toFixed(1)},${pts[0].y.toFixed(1)}`;
    for (let i = 1; i < pts.length; i++) {
      const prev = pts[i - 1];
      const curr = pts[i];
      const next = pts[i + 1];
      const r = curr.r || 0;
      if (!next || r <= 0) {
        d += ` L ${curr.x.toFixed(1)},${curr.y.toFixed(1)}`;
        continue;
      }
      const v1 = { x: curr.x - prev.x, y: curr.y - prev.y };
      const v2 = { x: next.x - curr.x, y: next.y - curr.y };
      const len1 = Math.hypot(v1.x, v1.y);
      const len2 = Math.hypot(v2.x, v2.y);
      const actualR = Math.min(r, len1 / 2, len2 / 2);
      const startX = curr.x - (v1.x / len1) * actualR;
      const startY = curr.y - (v1.y / len1) * actualR;
      const endX = curr.x + (v2.x / len2) * actualR;
      const endY = curr.y + (v2.y / len2) * actualR;
      d += ` L ${startX.toFixed(1)},${startY.toFixed(1)} Q ${curr.x.toFixed(1)},${curr.y.toFixed(1)} ${endX.toFixed(1)},${endY.toFixed(1)}`;
    }
    return d;
  }

  function initPathBg() {
    const containers = document.querySelectorAll('.path-background');
    if (!containers.length) return;

    containers.forEach(container => {
      let svg = container.querySelector('svg.path-bg-svg');
      if (!svg) {
        svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('class', 'path-bg-svg');
        svg.setAttribute('aria-hidden', 'true');
        svg.innerHTML = `
          <defs>
            <filter id="bg-point-glow" x="-100%" y="-100%" width="300%" height="300%">
              <feGaussianBlur stdDeviation="5" result="blur1" />
              <feGaussianBlur stdDeviation="12" result="blur2" />
              <feMerge>
                <feMergeNode in="blur2" />
                <feMergeNode in="blur1" />
                <feMergeNode in="SourceGraphic" />
              </feMerge>
            </filter>
            <filter id="bg-point-halo" x="-100%" y="-100%" width="300%" height="300%">
              <feGaussianBlur stdDeviation="16" />
            </filter>
            <linearGradient id="truck-headlight-beam" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#abff02" stop-opacity="0.85" />
              <stop offset="35%" stop-color="#abff02" stop-opacity="0.4" />
              <stop offset="100%" stop-color="#abff02" stop-opacity="0" />
            </linearGradient>
          </defs>
          <g class="paths-layer"></g>
          <g class="trails-layer"></g>
          <g class="trucks-layer"></g>
        `;
        container.appendChild(svg);
      }

      const pathsLayer = svg.querySelector('.paths-layer');
      const trailsLayer = svg.querySelector('.trails-layer');
      const trucksLayer = svg.querySelector('.trucks-layer');

      let tracks = [];
      let animId = null;
      let isVisible = false;

      function buildPaths() {
        const W = container.offsetWidth || window.innerWidth;
        const H = container.offsetHeight || 600;

        svg.setAttribute('viewBox', `0 0 ${W} ${H}`);

        const pathDefs = [
          // Path 0: Left-center drop and lateral turn
          [
            { x: W * 0.38, y: 0 },
            { x: W * 0.38, y: H * 0.16, r: 45 },
            { x: W * 0.12, y: H * 0.16, r: 45 },
            { x: W * 0.12, y: H * 0.52, r: 60 },
            { x: 0, y: H * 0.52 }
          ],
          // Path 1: Right-center drop and right exit
          [
            { x: W * 0.62, y: 0 },
            { x: W * 0.62, y: H * 0.22, r: 45 },
            { x: W * 0.88, y: H * 0.22, r: 45 },
            { x: W * 0.88, y: H * 0.48, r: 50 },
            { x: W, y: H * 0.48 }
          ],
          // Path 2: Bottom-right swoop
          [
            { x: W, y: H * 0.78 },
            { x: W * 0.68, y: H * 0.78, r: 45 },
            { x: W * 0.68, y: H }
          ],
          // Path 3: Bottom-left swoop
          [
            { x: 0, y: H * 0.68 },
            { x: W * 0.28, y: H * 0.68, r: 55 },
            { x: W * 0.28, y: H }
          ],
          // Path 4: Middle transverse circuit line
          [
            { x: 0, y: H * 0.34 },
            { x: W * 0.48, y: H * 0.34, r: 40 },
            { x: W * 0.48, y: H * 0.82, r: 40 },
            { x: W * 0.56, y: H * 0.82, r: 40 },
            { x: W * 0.56, y: H }
          ],
          // Path 5: Top-right diagonal circuit
          [
            { x: W * 0.82, y: 0 },
            { x: W * 0.82, y: H * 0.38, r: 45 },
            { x: W * 0.42, y: H * 0.38, r: 45 },
            { x: W * 0.42, y: H * 0.72, r: 45 },
            { x: 0, y: H * 0.72 }
          ]
        ];

        pathsLayer.innerHTML = '';
        trailsLayer.innerHTML = '';
        trucksLayer.innerHTML = '';
        tracks = [];

        pathDefs.forEach((pts, idx) => {
          const d = roundedPathString(pts);

          // Base highway track
          const basePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
          basePath.setAttribute('class', 'path-base');
          basePath.setAttribute('d', d);
          pathsLayer.appendChild(basePath);

          // Trail path (illuminated highway lane)
          const trailPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
          trailPath.setAttribute('class', 'path-trail');
          trailPath.setAttribute('d', d);
          trailsLayer.appendChild(trailPath);

          // Truck Silhouette Group (Satellite GPS View)
          const truckGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
          truckGroup.setAttribute('class', 'truck-marker');
          truckGroup.innerHTML = `
            <!-- Headlight beams projecting on road -->
            <path d="M 12 -4 L 38 -11 L 38 11 L 12 4 Z" fill="url(#truck-headlight-beam)" opacity="0.8" />
            
            <!-- Semi-Trailer Body -->
            <rect x="-19" y="-6" width="20" height="12" rx="2" fill="#061e18" stroke="#abff02" stroke-width="0.9" stroke-opacity="0.6" />
            <!-- Cargo ribs -->
            <line x1="-14" y1="-6" x2="-14" y2="6" stroke="#abff02" stroke-width="0.5" stroke-opacity="0.35" />
            <line x1="-9" y1="-6" x2="-9" y2="6" stroke="#abff02" stroke-width="0.5" stroke-opacity="0.35" />
            <line x1="-4" y1="-6" x2="-4" y2="6" stroke="#abff02" stroke-width="0.5" stroke-opacity="0.35" />
            
            <!-- Red Tail lights -->
            <rect x="-19.5" y="-5" width="1.2" height="2.5" fill="#ff3344" />
            <rect x="-19.5" y="2.5" width="1.2" height="2.5" fill="#ff3344" />
            
            <!-- Tractor Cab (Branded Lime) -->
            <rect x="3" y="-5.5" width="10" height="11" rx="2" fill="#abff02" />
            <!-- Windshield -->
            <path d="M 9 -4 L 12 -2 L 12 2 L 9 4 Z" fill="#021313" />
            <!-- GPS Telemetry Beacon / Roof Light -->
            <circle cx="6.5" cy="0" r="2.2" fill="#ffffff" filter="url(#bg-point-glow)" />
          `;
          trucksLayer.appendChild(truckGroup);

          const totalLen = basePath.getTotalLength() || 1000;
          const trailLen = Math.min(140, totalLen * 0.32);
          trailPath.style.strokeDasharray = `${trailLen} ${totalLen * 2}`;

          const durations = [5.2, 5.8, 4.6, 5.4, 6.2, 5.0];
          const delays = [0, 1.6, 3.2, 0.9, 4.0, 2.4];

          tracks.push({
            basePath,
            trailPath,
            truckGroup,
            totalLen,
            trailLen,
            duration: durations[idx % durations.length] * 1000,
            delay: delays[idx % delays.length] * 1000
          });
        });
      }

      function render(time) {
        if (!isVisible) return;

        tracks.forEach(tr => {
          const t = ((time + tr.delay) % tr.duration) / tr.duration;
          const dist = t * tr.totalLen;
          const pt = tr.basePath.getPointAtLength(dist);

          // Calculate rotation angle along road curve
          let angle = 0;
          if (dist + 2 <= tr.totalLen) {
            const ptNext = tr.basePath.getPointAtLength(dist + 2);
            angle = Math.atan2(ptNext.y - pt.y, ptNext.x - pt.x) * (180 / Math.PI);
          } else {
            const ptPrev = tr.basePath.getPointAtLength(Math.max(0, dist - 2));
            angle = Math.atan2(pt.y - ptPrev.y, pt.x - ptPrev.x) * (180 / Math.PI);
          }

          let opacity = 1;
          if (t < 0.08) {
            opacity = t / 0.08;
          } else if (t > 0.90) {
            opacity = Math.max(0, (1 - t) / 0.10);
          }

          tr.truckGroup.setAttribute('transform', `translate(${pt.x.toFixed(1)}, ${pt.y.toFixed(1)}) rotate(${angle.toFixed(1)})`);
          tr.truckGroup.style.opacity = opacity.toFixed(3);

          tr.trailPath.style.strokeDashoffset = `${-(dist - tr.trailLen).toFixed(1)}`;
          tr.trailPath.style.opacity = (opacity * 0.92).toFixed(3);
        });

        animId = requestAnimationFrame(render);
      }

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            isVisible = true;
            if (!animId) animId = requestAnimationFrame(render);
          } else {
            isVisible = false;
            if (animId) {
              cancelAnimationFrame(animId);
              animId = null;
            }
          }
        });
      }, { threshold: 0.02 });

      observer.observe(container);

      let resizeTimer;
      const ro = new ResizeObserver(() => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(buildPaths, 60);
      });
      ro.observe(container);

      buildPaths();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPathBg);
  } else {
    initPathBg();
  }
})();

