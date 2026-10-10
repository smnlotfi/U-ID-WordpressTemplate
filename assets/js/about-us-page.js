/**
 * رفتار تعاملی اختصاصی صفحه «درباره ما» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: منوی هدر/مگامنو، شیت موبایل، مودال درخواست تماس (باز/بسته‌شدن)، دکمه‌های
 * شناور تماس، بازگشت به بالا، نوار پیشرفت اسکرول و رفتار عمومی فرم‌ها/[data-submit]
 * از assets/js/main.js می‌آیند — اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
 *
 * سیستم «فصل تاخوردنی + فیلتر بخش‌های ضروری» دقیقاً مثل assets/js/glossary-page.js
 * است (شش فصل: مأموریت، هویت برند، ارزش‌ها، نقشه توانمندی، کارنامه، تعهدها).
 */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mqFold = w.matchMedia('(max-width:900px)');
  var mqMobile = w.matchMedia('(max-width:720px)');

  function fa(s) { return String(s).replace(/[0-9]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'[x]; }); }

  /* ---- اعتبارسنجی و ارسال فرم‌های این صفحه (bandForm در پایین صفحه + modalForm) ---- */
  function validate(form) {
    var ok = true;
    form.querySelectorAll('[data-req]').forEach(function (f) {
      var wrap = f.closest('.fld'), bad = !f.value.trim();
      if (!bad && f.hasAttribute('data-tel')) bad = !/^09\d{9}$/.test(f.value.replace(/[^0-9]/g, ''));
      if (wrap) wrap.classList.toggle('bad', bad);
      if (bad) ok = false;
    });
    return ok;
  }
  d.querySelectorAll('.uid-about-us-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-about-us-page .fld input, .uid-about-us-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: افراد حقیقی را به جای فرم فروش، به صفحه ثنا هدایت کن ---- */
  var sel = d.querySelector('.uid-about-us-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  var revealEls = d.querySelectorAll('.uid-about-us-page .rv');
  revealEls.forEach(function (el) {
    if (!io) { el.classList.add('is-in'); return; }
    var rect = el.getBoundingClientRect();
    if (rect.top < w.innerHeight && rect.bottom > 0) { el.classList.add('is-in'); } else { io.observe(el); }
  });
  if (io) { setTimeout(function () { io.disconnect(); revealEls.forEach(function (el) { el.classList.add('is-in'); }); }, 1200); }

  /* ═══════════════════════════════════════════════════════════════════════
   * کارت‌های دسته (values) — روی موبایل، شبکه کارت‌ها به یک استک فیزیکی تبدیل
   * می‌شود که با کشیدن، ورق می‌خورد.
   * ═══════════════════════════════════════════════════════════════════════ */
  function initDeck(deck) {
    var key = deck.dataset.deck;
    var ui = d.querySelector('[data-deck-ui="' + key + '"]');
    var cards = Array.prototype.slice.call(deck.children);
    var n = cards.length, i = 0;
    if (!n) return null;

    var prevBtn = ui && ui.querySelector('[data-deck-prev]');
    var nextBtn = ui && ui.querySelector('[data-deck-next]');
    var bar = ui && ui.querySelector('.deck-bar i');
    var count = ui && ui.querySelector('.deck-count');

    function measure() {
      if (!mqMobile.matches) { deck.style.removeProperty('--deck-h'); return; }
      var prev = deck.getAttribute('style') || '';
      deck.style.height = 'auto';
      cards.forEach(function (c) { c.style.position = 'static'; c.style.height = 'auto'; });
      var h = 0;
      cards.forEach(function (c) { h = Math.max(h, c.offsetHeight); });
      cards.forEach(function (c) { c.style.position = ''; c.style.height = ''; });
      deck.setAttribute('style', prev);
      deck.style.setProperty('--deck-h', (h + 34) + 'px');
    }

    function paint() {
      cards.forEach(function (c, idx) {
        c.classList.remove('dk-0', 'dk-1', 'dk-2', 'dk-h', 'dk-out');
        var off = idx - i;
        if (off < 0) c.classList.add('dk-out');
        else if (off === 0) c.classList.add('dk-0');
        else if (off === 1) c.classList.add('dk-1');
        else if (off === 2) c.classList.add('dk-2');
        else c.classList.add('dk-h');
        c.setAttribute('aria-hidden', off === 0 ? 'false' : 'true');
      });
      if (bar) bar.style.width = (((i + 1) / n) * 100) + '%';
      if (count) count.textContent = fa(i + 1) + ' / ' + fa(n);
      if (prevBtn) prevBtn.disabled = (i === 0);
      if (nextBtn) nextBtn.disabled = (i === n - 1);
    }

    function go(next) { i = Math.max(0, Math.min(n - 1, next)); paint(); }

    function enable() { measure(); paint(); deck.dataset.deckOn = '1'; }
    function disable() {
      cards.forEach(function (c) {
        c.classList.remove('dk-0', 'dk-1', 'dk-2', 'dk-h', 'dk-out');
        c.removeAttribute('aria-hidden');
      });
      deck.style.removeProperty('--deck-h');
      deck.dataset.deckOn = '0';
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { go(i - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { go(i + 1); });

    deck.addEventListener('click', function (e) {
      if (deck.dataset.deckOn !== '1') return;
      if (e.target.closest('a,button')) return;
      var card = e.target.closest('.deck > *');
      if (!card || card !== cards[i]) return;
      go(i === n - 1 ? 0 : i + 1);
    });

    var x0 = null, y0 = null, locked = false;
    deck.addEventListener('touchstart', function (e) {
      if (deck.dataset.deckOn !== '1') return;
      x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; locked = false;
    }, { passive: true });
    deck.addEventListener('touchmove', function (e) {
      if (x0 === null) return;
      var dx = e.touches[0].clientX - x0, dy = e.touches[0].clientY - y0;
      if (!locked && Math.abs(dx) > Math.abs(dy) + 6) locked = true;
    }, { passive: true });
    deck.addEventListener('touchend', function (e) {
      if (x0 === null) return;
      var dx = e.changedTouches[0].clientX - x0;
      if (locked && Math.abs(dx) > 44) go(dx > 0 ? i + 1 : i - 1);
      x0 = null; y0 = null; locked = false;
    }, { passive: true });

    deck.setAttribute('tabindex', '0');
    deck.addEventListener('keydown', function (e) {
      if (deck.dataset.deckOn !== '1') return;
      if (e.key === 'ArrowLeft') { e.preventDefault(); go(i + 1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); go(i - 1); }
    });

    function sync() { if (mqMobile.matches) enable(); else disable(); }
    sync();
    var handle = { el: deck, refresh: function () { if (mqMobile.matches) { measure(); paint(); } } };
    if (mqMobile.addEventListener) mqMobile.addEventListener('change', sync);
    else if (mqMobile.addListener) mqMobile.addListener(sync);
    var rt; w.addEventListener('resize', function () {
      clearTimeout(rt); rt = setTimeout(function () { if (mqMobile.matches) measure(); }, 180);
    }, { passive: true });
    if (d.fonts && d.fonts.ready) d.fonts.ready.then(function () { if (mqMobile.matches) measure(); });
    w.addEventListener('load', function () { if (mqMobile.matches) measure(); });
    return handle;
  }
  var DECKS = [];
  d.querySelectorAll('.uid-about-us-page [data-deck]').forEach(function (dk) { DECKS.push(initDeck(dk)); });

  /* دسته کارت‌ها تا وقتی فصلش بسته است، ارتفاع صفر اندازه‌گیری می‌شود؛ سیستم فصل
     همین را لحظه باز شدن دوباره صدا می‌زند. */
  w.__remeasureDecks = function (scope) {
    DECKS.forEach(function (fn) {
      if (!fn) return;
      if (scope && !scope.contains(fn.el)) return;
      fn.refresh();
    });
  };

  /* ═══════════════════════════════════════════════════════════════════════
   * نوار «پرش سریع به بخش‌ها» (موبایل)
   * ═══════════════════════════════════════════════════════════════════════ */
  var jb = d.getElementById('jumpbar');
  if (jb) {
    var jchips = Array.prototype.slice.call(jb.querySelectorAll('[data-jump]'));
    var targets = jchips.map(function (c) { return d.querySelector(c.dataset.jump); });
    var skip = jchips.map(function (c) { return c.classList.contains('top'); });

    jchips.forEach(function (c) {
      c.addEventListener('click', function () {
        var t = d.querySelector(c.dataset.jump);
        if (!t) return;
        var opened = w.__openFoldFor ? w.__openFoldFor(t) : false;
        var go = function () {
          w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 96, behavior: reduce ? 'auto' : 'smooth' });
        };
        if (opened) requestAnimationFrame(function () { requestAnimationFrame(go); });
        else go();
      });
    });

    var jTick = false;
    function onJump() {
      var on = w.scrollY > 560;
      jb.classList.toggle('show', on);
      d.body.classList.toggle('jump-on', on);
      var active = -1;
      targets.forEach(function (t, idx) {
        if (!t || skip[idx]) return;
        if (t.getBoundingClientRect().top <= w.innerHeight * 0.34) active = idx;
      });
      jchips.forEach(function (c, idx) { c.classList.toggle('on', idx === active); });
      if (active > -1) {
        var el = jchips[active], rail = d.getElementById('jumpRail');
        if (rail && el) {
          var er = el.getBoundingClientRect(), rr = rail.getBoundingClientRect();
          if (er.left < rr.left || er.right > rr.right) {
            rail.scrollTo({ left: el.offsetLeft - rail.clientWidth / 2 + el.offsetWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
          }
        }
      }
      jTick = false;
    }
    w.addEventListener('scroll', function () {
      if (!jTick) { jTick = true; requestAnimationFrame(onJump); }
    }, { passive: true });
    onJump();
  }

  /* پرش نرم کپسول («نقشه توانمندی پلتفرم» در هیرو) به #platform */
  d.querySelectorAll('.uid-about-us-page [data-jump-soft]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = d.querySelector(b.dataset.jumpSoft);
      if (!t) return;
      if (w.__openFoldFor) w.__openFoldFor(t);
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 76, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ═══════════════════════════════════════════════════════════════════════
   * سیستم «فصل تاخوردنی» + خواننده فصل + فیلتر «فقط بخش‌های ضروری»
   * (دقیقاً مثل assets/js/glossary-page.js)
   * ═══════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('.uid-about-us-page [data-fold]'));
  var meter = d.getElementById('foldMeter');

  function paintMeter() {
    if (!meter) return;
    var read = folds.filter(function (s) { return s.classList.contains('read'); }).length;
    meter.style.width = (folds.length ? (read / folds.length) * 100 : 0) + '%';
  }

  function chevron(dir) {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" ' +
      'stroke-linecap="round" stroke-linejoin="round"><path d="' +
      (dir === 'next' ? 'M15 18l-6-6 6-6' : 'M9 18l6-6-6-6') + '"/></svg>';
  }

  folds.forEach(function (sec, idx) {
    var body = sec.querySelector('.fold-body');
    if (!body) return;
    var title = sec.dataset.foldTitle || '';

    var btn = d.createElement('button');
    btn.className = 'fold-btn';
    btn.type = 'button';
    btn.setAttribute('aria-expanded', 'false');
    var bodyId = 'ab-fold-body-' + (idx + 1);
    body.id = bodyId;
    btn.setAttribute('aria-controls', bodyId);
    btn.innerHTML =
      '<span class="fnum">' + fa(idx + 1) + '</span>' +
      '<span class="ftx"><b></b><span></span></span>' +
      (sec.dataset.foldMin ? '<span class="fmin">' + sec.dataset.foldMin + '</span>' : '') +
      (sec.hasAttribute('data-fold-hot') ? '<span class="fdot"></span>' : '') +
      '<svg class="fchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
      'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' +
      '<path d="M6 9l6 6 6-6"/></svg>';
    btn.querySelector('.ftx b').textContent = title;
    btn.querySelector('.ftx span').textContent = sec.dataset.foldTeaser || '';
    sec.insertBefore(btn, sec.firstChild);

    var sticky = d.createElement('div');
    sticky.className = 'chap-sticky';
    sticky.innerHTML =
      '<span class="cs-n">' + fa(idx + 1) + ' / ' + fa(folds.length) + '</span>' +
      '<span class="cs-t"></span>' +
      '<button class="cs-x" type="button" aria-label="بستن این بخش">' +
      '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" ' +
      'stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>';
    sticky.querySelector('.cs-t').textContent = title;
    sec.insertBefore(sticky, body);
    sticky.querySelector('.cs-x').addEventListener('click', function () {
      sec._foldOpen(false);
      w.scrollTo({ top: sec.getBoundingClientRect().top + w.scrollY - 108, behavior: reduce ? 'auto' : 'smooth' });
    });

    var inner = sec.querySelector('.fold-inner');
    if (inner) {
      var nav = d.createElement('div');
      nav.className = 'chap-nav';
      var prevTitle = idx > 0 ? (folds[idx - 1].dataset.foldTitle || 'بخش قبلی') : '';
      var nextTitle = idx < folds.length - 1 ? (folds[idx + 1].dataset.foldTitle || 'بخش بعدی') : '';
      nav.innerHTML =
        '<button class="cn-btn" type="button" data-cn="prev"' + (idx === 0 ? ' disabled' : '') + '>' +
        chevron('prev') + '<span>' + (prevTitle || 'ابتدای فهرست') + '</span></button>' +
        '<button class="cn-btn" type="button" data-cn="next"' + (idx === folds.length - 1 ? ' disabled' : '') + '>' +
        '<span>' + (nextTitle || 'پایان فهرست') + '</span>' + chevron('next') + '</button>' +
        '<button class="cn-btn cn-cta" type="button" data-open-modal>گفت‌وگو با کارشناس یوآیدی</button>';
      inner.appendChild(nav);
      nav.querySelector('[data-cn="prev"]').addEventListener('click', function () {
        if (idx > 0) openChapter(idx - 1);
      });
      nav.querySelector('[data-cn="next"]').addEventListener('click', function () {
        if (idx < folds.length - 1) openChapter(idx + 1);
      });
    }

    sec._foldOpen = function (open, instant) {
      if (!mqFold.matches) return;
      var isOpen = sec.classList.contains('open');
      if (open === isOpen) return;
      if (open) {
        sec.classList.add('open', 'read');
        btn.setAttribute('aria-expanded', 'true');
        body.style.visibility = 'visible';
        paintMeter();
        if (instant || reduce) { body.style.height = 'auto'; afterOpen(); return; }
        body.style.height = body.scrollHeight + 'px';
        var done = function () {
          body.removeEventListener('transitionend', done);
          if (sec.classList.contains('open')) body.style.height = 'auto';
          afterOpen();
        };
        body.addEventListener('transitionend', done);
      } else {
        sec.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
        if (instant || reduce) { body.style.height = '0px'; return; }
        body.style.height = body.scrollHeight + 'px';
        void body.offsetHeight;
        body.style.height = '0px';
      }
    };

    function afterOpen() {
      if (w.__remeasureDecks) w.__remeasureDecks(sec);
      sec.querySelectorAll('.rv,.fnl').forEach(function (el) { el.classList.add('is-in'); });
    }

    btn.addEventListener('click', function () {
      if (sec.classList.contains('open')) sec._foldOpen(false);
      else openChapter(idx);
    });
  });

  function openChapter(i, noScroll) {
    var target = folds[i];
    if (!target || !target._foldOpen) return;
    folds.forEach(function (s, j) {
      if (j !== i && s.classList.contains('open') && s._foldOpen) s._foldOpen(false, true);
    });
    target._foldOpen(true, true);
    paintMeter();
    if (noScroll) return;
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        w.scrollTo({ top: target.getBoundingClientRect().top + w.scrollY - 100, behavior: reduce ? 'auto' : 'smooth' });
      });
    });
  }

  w.__openFoldFor = function (target) {
    if (!target || !mqFold.matches) return false;
    var sec = target.closest ? target.closest('[data-fold]') : null;
    if (!sec) sec = (target.hasAttribute && target.hasAttribute('data-fold')) ? target : null;
    if (sec && sec._foldOpen && !sec.classList.contains('open')) {
      var i = folds.indexOf(sec);
      if (i > -1) openChapter(i, true); else sec._foldOpen(true, true);
      return true;
    }
    return false;
  };

  /* فیلتر «فقط بخش‌های ضروری» — فقط چهار فصل دارای data-fold-hot را نشان می‌دهد */
  var chapters = d.getElementById('chapters');
  var filterBtn = d.getElementById('foldFilter');
  var fbTx = d.querySelector('#foldbar .fb-tx');
  if (chapters && filterBtn && fbTx) {
    var hotCount = folds.filter(function (s) { return s.hasAttribute('data-fold-hot'); }).length;
    var fullTx = fbTx.innerHTML;
    var hotTx = '<b>' + fa(hotCount) + '</b> بخش کلیدی — بقیه بخش‌ها موقتاً پنهان شده‌اند.';
    filterBtn.addEventListener('click', function () {
      var on = filterBtn.getAttribute('aria-pressed') !== 'true';
      if (on) {
        folds.forEach(function (s) {
          if (!s.hasAttribute('data-fold-hot') && s.classList.contains('open') && s._foldOpen) {
            s._foldOpen(false, true);
          }
        });
      }
      filterBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
      chapters.classList.toggle('only-hot', on);
      filterBtn.querySelector('span').textContent = on ? 'نمایش همه بخش‌ها' : 'فقط بخش‌های ضروری';
      fbTx.innerHTML = on ? hotTx : fullTx;
    });
  }

  var allBtn = d.getElementById('foldAll');
  if (allBtn) {
    allBtn.addEventListener('click', function () {
      var anyClosed = folds.some(function (s) { return !s.classList.contains('open'); });
      folds.forEach(function (s) {
        if (s._foldOpen) s._foldOpen(anyClosed, true);
        if (anyClosed) s.classList.add('read');
      });
      paintMeter();
      allBtn.textContent = anyClosed ? 'بستن همه' : 'باز کردن همه';
    });
  }

  function syncFold() {
    if (mqFold.matches) {
      folds.forEach(function (s) {
        var b = s.querySelector('.fold-body');
        if (b && !s.classList.contains('open')) b.style.height = '0px';
      });
    } else {
      folds.forEach(function (s) {
        var b = s.querySelector('.fold-body');
        if (b) { b.style.height = ''; b.style.visibility = ''; }
        s.classList.remove('open');
        var fb = s.querySelector('.fold-btn');
        if (fb) fb.setAttribute('aria-expanded', 'false');
      });
      if (allBtn) allBtn.textContent = 'باز کردن همه';
    }
  }
  if (mqFold.addEventListener) mqFold.addEventListener('change', syncFold);
  else if (mqFold.addListener) mqFold.addListener(syncFold);
  syncFold();
  paintMeter();

  /* دکمه‌های CTA داخل فوتر فصل بعد از باند شدن [data-open-modal] مشترک
     (assets/js/main.js) اضافه می‌شوند، پس باید همین‌جا هم بایند شوند. */
  d.querySelectorAll('.uid-about-us-page .chap-nav [data-open-modal]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      var m = d.getElementById('modal');
      if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
    });
  });

  /* ═══════════════════════════════════════════════════════════════════════
   * کاروسل نقطه‌ای (تعهدها)
   * ═══════════════════════════════════════════════════════════════════════ */
  d.querySelectorAll('.uid-about-us-page [data-rail]').forEach(function (rail) {
    var dots = d.querySelector('[data-dots="' + rail.dataset.rail + '"]');
    if (!dots) return;
    var kids = Array.prototype.slice.call(rail.children);
    dots.innerHTML = kids.map(function (_, i) { return '<i' + (i === 0 ? ' class="on"' : '') + ' role="button" tabindex="0" aria-label="اسلاید ' + (i + 1) + '"></i>'; }).join('');
    var ds = dots.querySelectorAll('i');
    rail.addEventListener('scroll', function () {
      var idx = Math.round(rail.scrollLeft / (rail.scrollWidth / kids.length));
      idx = Math.min(Math.abs(idx), kids.length - 1);
      ds.forEach(function (x, i) { x.classList.toggle('on', i === idx); });
    }, { passive: true });
    ds.forEach(function (x, i) {
      x.addEventListener('click', function () {
        kids[i].scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', inline: 'center', block: 'nearest' });
      });
    });
  });

  /* ═══════════════════════════════════════════════════════════════════════
   * مسیریابی مخاطب (تب‌های ۴گانه)
   * ═══════════════════════════════════════════════════════════════════════ */
  (function () {
    var tabs = Array.prototype.slice.call(d.querySelectorAll('.uid-about-us-page .ab-rt-tab'));
    var panes = Array.prototype.slice.call(d.querySelectorAll('.uid-about-us-page [data-rt-pane]'));
    if (!tabs.length) return;

    function show(i) {
      tabs.forEach(function (t, k) {
        t.classList.toggle('on', k === i);
        t.setAttribute('aria-selected', k === i ? 'true' : 'false');
      });
      panes.forEach(function (p, k) { p.classList.toggle('on', k === i); });
      var el = tabs[i], bar = el.parentNode;
      if (bar.scrollWidth > bar.clientWidth) {
        bar.scrollTo({ left: el.offsetLeft - bar.clientWidth / 2 + el.offsetWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
      }
    }
    tabs.forEach(function (t, i) { t.addEventListener('click', function () { show(i); }); });
  })();

  /* ═══════════════════════════════════════════════════════════════════════
   * شکل هویت برند (سه شش‌ضلعی)
   * ═══════════════════════════════════════════════════════════════════════ */
  (function () {
    var fig = d.getElementById('archFig');
    var panel = d.getElementById('archPanel');
    if (!fig || !panel) return;
    var shapes = Array.prototype.slice.call(fig.querySelectorAll('.hxb'));
    var panes = Array.prototype.slice.call(panel.querySelectorAll('[data-arch-pane]'));

    function pick(i) {
      fig.classList.add('picked');
      shapes.forEach(function (s, k) { s.classList.toggle('dim', k !== i); });
      panes.forEach(function (p, k) { p.hidden = (k !== i); });
      panel.dataset.on = String(i);
    }
    shapes.forEach(function (s, i) {
      s.addEventListener('click', function () { pick(i); });
      s.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(i); }
      });
    });
  })();

  /* ═══════════════════════════════════════════════════════════════════════
   * نقشه توانمندی پلتفرم — این آرایه باید دقیقاً با uid_ab_platform_bands() در
   * inc/about-us-page.php هماهنگ بماند (عنوان/شمار کارت‌ها همان‌جا رندر می‌شود؛
   * این‌جا فقط متن پنل جزئیات و فهرست سرویس‌های هر لایه است).
   * ═══════════════════════════════════════════════════════════════════════ */
  (function () {
    var stack = d.getElementById('cmapStack');
    var detail = d.getElementById('cmapDetail');
    if (!stack || !detail) return;

    var LAYERS = [
      { t: 'سطح تحویل',
        d: 'دو راه برای اتصال به همین یک زیرساخت: یا کل فرآیند را به یوآیدی بسپارید، یا سرویس‌ها را مستقیم در سامانه خودتان بنشانید. هر دو به یک هسته وصل می‌شوند.',
        s: [['یوآیدی‌پلاس (PWA)', '/uid-plus/'],
          ['وب‌سرویس احراز هویت تصویری', '/api/'],
          ['احراز هویت صرافی ارز دیجیتال', '/authentication-digital-currency-exchange/']] },
      { t: 'اعتبارسنجی مالی',
        d: 'پیش از آنکه پولی جابه‌جا شود، این لایه مشخص می‌کند حساب یا کارت واقعاً به همان شخصی تعلق دارد که احراز هویتش کرده‌اید.',
        s: [['استعلام اطلاعات مالی (شبا)', '/api-inquiry-iban/'],
          ['تبدیل شماره کارت به شبا', '/api-inquiry-card/'],
          ['تطبیق شبا با کد ملی', '/api-validate-iban/'],
          ['تطبیق شماره کارت با کد ملی', '/api-validate-card/']] },
      { t: 'سرویس‌های هویتی',
        d: 'اتصال مستقیم به سامانه‌های رسمی کشور. یوآیدی کارگزار مورد تایید سامانه‌های سجام و ثناست و استعلام‌های هویتی از منابع رسمی انجام می‌شود.',
        s: [['احراز هویت ثنا', '/sana/'],
          ['احراز هویت سجام', '/sejamauthentication/'],
          ['وب‌سرویس ثبت احوال', '/api-inquiry-person/'],
          ['استعلام کد پستی و آدرس', '/address-postcode-docs/'],
          ['ثنا ویژه ایرانیان خارج از کشور', '/sana-register-foreign-form/']] },
      { t: 'موتورهای زیستی و پردازش تصویر',
        d: 'پایه‌ای که سه لایه بالا روی آن ایستاده‌اند. هر احراز هویت تصویری در نهایت به همین سه موتور می‌رسد؛ بقیه سرویس‌ها استعلام‌اند، این‌ها تشخیص.',
        s: [['تشخیص زنده‌بودن', '/liveness-detection/'],
          ['تطبیق و تشخیص چهره', '/face-detection/'],
          ['استخراج اطلاعات از مدارک (OCR)', '/docs/']] }
    ];

    var CH = '<svg class="ch" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
      'stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">' +
      '<path d="M15 6l-6 6 6 6"/></svg>';

    var bands = Array.prototype.slice.call(stack.querySelectorAll('.ab-band'));

    function esc(s) {
      return String(s).replace(/[&<>"]/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
      });
    }

    function render(i) {
      var L = LAYERS[i];
      detail.innerHTML =
        '<h3>' + esc(L.t) + '</h3><p>' + esc(L.d) + '</p><div class="ab-clist">' +
        L.s.map(function (x) {
          return '<a class="ab-cserv" href="' + esc(x[1]) + '">' +
            '<span class="d" aria-hidden="true"></span><b>' + esc(x[0]) + '</b>' + CH + '</a>';
        }).join('') + '</div>';
    }

    function pick(i) {
      bands.forEach(function (b, k) {
        b.classList.toggle('on', k === i);
        b.setAttribute('aria-selected', k === i ? 'true' : 'false');
      });
      render(i);
    }

    bands.forEach(function (b, i) { b.addEventListener('click', function () { pick(i); }); });
    pick(0);
  })();
})();
