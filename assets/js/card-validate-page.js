/**
 * رفتار تعاملی اختصاصی صفحه «تطبیق شماره کارت با کد ملی» — فقط روی این قالب
 * صفحه بارگذاری می‌شود. نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل،
 * مودال درخواست تماس، دکمه‌های شناور تماس و بازگشت به بالا از هدر/فوتر مشترک
 * سایت (assets/js/main.js) می‌آیند — اینجا عمداً تکرار نشده‌اند.
 *
 * سیستم «فصل تاخوردنی + کتاب‌خوان فصل» موبایل دقیقاً مثل
 * assets/js/ekyc-liveness-page.js است؛ علاوه بر آن، این صفحه یک فیلتر
 * «فقط بخش‌های ضروری» هم دارد که فصل‌های data-fold-hot را از بقیه جدا می‌کند.
 */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mqMobile = w.matchMedia('(max-width:720px)');
  var mqFold = w.matchMedia('(max-width:900px)');

  function fa(s) { return String(s).replace(/[0-9]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'[x]; }); }
  function grp(n) { return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '٬'); }
  function faNum(n) { return fa(grp(n)); }
  function faDec(n, p) { return fa(Number(n).toFixed(p).replace('.', '٫')); }

  /* ---- اعتبارسنجی و ارسال فرم‌های این صفحه ---- */
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
  d.querySelectorAll('.uid-card-validate-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-card-validate-page .fld input, .uid-card-validate-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: کاربران شخصی را به جای صف فروش، به صفحه درست هدایت کن ---- */
  var sel = d.querySelector('.uid-card-validate-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  var revealEls = d.querySelectorAll('.uid-card-validate-page .rv, .uid-card-validate-page .fnl, .uid-card-validate-page [data-count]');
  revealEls.forEach(function (el) {
    if (!io) { el.classList.add('is-in'); return; }
    var rect = el.getBoundingClientRect();
    if (rect.top < w.innerHeight && rect.bottom > 0) { el.classList.add('is-in'); } else { io.observe(el); }
  });
  if (io) { setTimeout(function () { io.disconnect(); revealEls.forEach(function (el) { el.classList.add('is-in'); }); }, 1200); }

  /* ---- شمارش صعودی اعداد ---- */
  var cio = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) {
      if (!e.isIntersecting) return; cio.unobserve(e.target);
      var el = e.target, to = parseFloat(el.dataset.count), dec = parseInt(el.dataset.dec || '0', 10);
      if (reduce) { el.textContent = fa(dec ? to.toFixed(dec) : grp(to)); return; }
      var t0 = null, dur = 1400;
      function step(t) {
        if (!t0) t0 = t;
        var p = Math.min((t - t0) / dur, 1);
        var v = to * (1 - Math.pow(1 - p, 3));
        el.textContent = fa(dec ? v.toFixed(dec) : grp(v));
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  }, { threshold: .4 }) : null;
  d.querySelectorAll('.uid-card-validate-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-card-validate-page .faq-q').forEach(function (q) {
    q.addEventListener('click', function () {
      var item = q.parentElement, a = item.querySelector('.faq-a'), open = item.classList.contains('open');
      var group = item.closest('.faq');
      group.querySelectorAll('.faq-i.open').forEach(function (o) {
        o.classList.remove('open'); o.querySelector('.faq-a').style.maxHeight = null;
        o.querySelector('.faq-q').setAttribute('aria-expanded', 'false');
      });
      if (!open) { item.classList.add('open'); a.style.maxHeight = a.scrollHeight + 'px'; q.setAttribute('aria-expanded', 'true'); }
    });
  });

  /* ---- دات‌های کاروسل موبایل ---- */
  d.querySelectorAll('.uid-card-validate-page [data-rail]').forEach(function (rail) {
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
    ds.forEach(function (x, i) { x.addEventListener('click', function () { kids[i].scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', inline: 'center', block: 'nearest' }); }); });
  });

  /* ---- تب‌های نمونه‌کد + کپی ---- */
  var codeTabs = d.querySelectorAll('.uid-card-validate-page [data-code-tab]');
  codeTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      codeTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.codeTab;
      d.querySelectorAll('.uid-card-validate-page [data-code-pane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.codePane === key);
      });
    });
  });
  var copyBtn = d.querySelector('.uid-card-validate-page [data-copy]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var active = d.querySelector('.uid-card-validate-page .code-pane.on');
      if (!active) return;
      var text = active.innerText;
      var done = function () {
        copyBtn.textContent = 'کپی شد!'; copyBtn.classList.add('copied');
        setTimeout(function () { copyBtn.textContent = 'کپی'; copyBtn.classList.remove('copied'); }, 1600);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(text).then(done).catch(done);
      else done();
    });
  }

  /* ───────────── CARD DECK ───────────── */
  function initDeck(deck) {
    var key = deck.dataset.deck;
    var ui = d.querySelector('[data-deck-ui="' + key + '"]');
    var cards = Array.prototype.slice.call(deck.children);
    var n = cards.length, i = 0;
    if (!n) return;

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
  d.querySelectorAll('.uid-card-validate-page [data-deck]').forEach(function (dk) { DECKS.push(initDeck(dk)); });

  w.__remeasureDecks = function (scope) {
    DECKS.forEach(function (fn) {
      if (!fn) return;
      if (scope && !scope.contains(fn.el)) return;
      fn.refresh();
    });
  };

  /* ───────────── INDUSTRY PICKER ───────────── */
  d.querySelectorAll('.uid-card-validate-page [data-pick]').forEach(function (pick) {
    var key = pick.dataset.pick;
    var strip = d.querySelector('[data-pick-chips="' + key + '"]');
    var cards = Array.prototype.slice.call(pick.children);
    var n = cards.length, i = 0;
    if (!n || !strip) return;

    strip.innerHTML = cards.map(function (c, idx) {
      return '<button class="pick-chip' + (idx === 0 ? ' on' : '') + '" type="button" role="tab" ' +
        'aria-selected="' + (idx === 0 ? 'true' : 'false') + '">' +
        (c.dataset.label || ('گزینه ' + fa(idx + 1))) + '</button>';
    }).join('');
    var chips = Array.prototype.slice.call(strip.children);

    function paint() {
      cards.forEach(function (c, idx) { c.classList.toggle('on', idx === i); });
      chips.forEach(function (c, idx) {
        c.classList.toggle('on', idx === i);
        c.setAttribute('aria-selected', idx === i ? 'true' : 'false');
      });
      var el = chips[i];
      if (el && mqMobile.matches) {
        var er = el.getBoundingClientRect(), rr = strip.getBoundingClientRect();
        if (er.left < rr.left || er.right > rr.right) {
          strip.scrollTo({
            left: el.offsetLeft - strip.clientWidth / 2 + el.offsetWidth / 2,
            behavior: reduce ? 'auto' : 'smooth'
          });
        }
      }
    }
    chips.forEach(function (c, idx) {
      c.addEventListener('click', function () { i = idx; paint(); });
    });

    var x0 = null, y0 = null, lock = false;
    pick.addEventListener('touchstart', function (e) {
      if (!mqMobile.matches) return;
      x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; lock = false;
    }, { passive: true });
    pick.addEventListener('touchmove', function (e) {
      if (x0 === null) return;
      var dx = e.touches[0].clientX - x0, dy = e.touches[0].clientY - y0;
      if (!lock && Math.abs(dx) > Math.abs(dy) + 6) lock = true;
    }, { passive: true });
    pick.addEventListener('touchend', function (e) {
      if (x0 === null) return;
      var dx = e.changedTouches[0].clientX - x0;
      if (lock && Math.abs(dx) > 44) { i = dx > 0 ? (i + 1) % n : (i - 1 + n) % n; paint(); }
      x0 = null; lock = false;
    }, { passive: true });

    paint();
  });

  /* ═════════════════════════════════════════════════════════════════════
     سیستم فولد فصل + خواننده فصل + فیلتر «فقط بخش‌های ضروری»
     ═════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('.uid-card-validate-page [data-fold]'));
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
    var bodyId = 'uid-cv-fold-body-' + (idx + 1);
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
      w.scrollTo({
        top: sec.getBoundingClientRect().top + w.scrollY - 108,
        behavior: reduce ? 'auto' : 'smooth'
      });
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
        '<button class="cn-btn cn-cta" type="button" data-open-modal>' +
        'درخواست فعال‌سازی وب‌سرویس تطبیق کارت</button>';
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
        w.scrollTo({
          top: target.getBoundingClientRect().top + w.scrollY - 100,
          behavior: reduce ? 'auto' : 'smooth'
        });
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

  /* ── فیلتر «فقط بخش‌های ضروری» ──────────────────────────────────────
     چهار فصلی که data-fold-hot دارند (نقطه نارنجی روی ردیف تاخورده) پایه‌ی
     همین فیلتر هم هستند: با فعال شدنش، پنج فصل دیگر مخفی می‌شوند و فهرست
     نُه‌ردیفی به فهرست چهارردیفی تبدیل می‌شود. */
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

  d.querySelectorAll('.uid-card-validate-page .chap-nav [data-open-modal]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      var m = d.getElementById('modal');
      if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
    });
  });

  /* ---- افست نرم لنگر داخلی (فصل بسته را ابتدا باز می‌کند) ---- */
  d.querySelectorAll('.uid-card-validate-page a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault();
      var opened = w.__openFoldFor ? w.__openFoldFor(t) : false;
      var go = function () {
        w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
      };
      if (opened) requestAnimationFrame(function () { requestAnimationFrame(go); });
      else go();
    });
  });

  /* ---- کپسول ۳۰ ثانیه‌ای: پرش نرم به محاسبه‌گر ---- */
  d.querySelectorAll('.uid-card-validate-page [data-jump-soft]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = d.querySelector(b.dataset.jumpSoft);
      if (!t) return;
      if (w.__openFoldFor) w.__openFoldFor(t);
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 76, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ═════════════════════════════════════════════════════════════════════
     هیرو — کنسول زنده تطبیق مالکیت کارت (شبیه‌سازی نمایشی، داده آن هاردکد است)
     ─────────────────────────────────────────────────────────────────────
     شش رقم اول کارت (BIN) بانک صادرکننده را شناسایی می‌کند، شماره کارت طبق
     الگوریتم Luhn (ISO/IEC 7812) اعتبارسنجی می‌شود — دقیقاً همان بررسی
     «اعتبارسنجی ساختار داده‌ها»ی مستندشده — و پاسخ در قالب مستندِ
     validate/card/ownership (فیلد بولین isMatched) نشان داده می‌شود.
     ═════════════════════════════════════════════════════════════════════ */
  var CARD_BINS = {
    '603799': ['بانک ملی ایران', '#1E5C3A'], '170019': ['بانک ملی ایران', '#1E5C3A'],
    '610433': ['بانک ملت', '#B02A37'], '991975': ['بانک ملت', '#B02A37'],
    '603769': ['بانک صادرات ایران', '#1B4F8C'],
    '627353': ['بانک تجارت', '#0F6B5C'], '585983': ['بانک تجارت', '#0F6B5C'],
    '589210': ['بانک سپه', '#1F3D7A'],
    '603770': ['بانک کشاورزی', '#2F6B2F'], '639217': ['بانک کشاورزی', '#2F6B2F'],
    '628023': ['بانک مسکن', '#1D4E89'],
    '589463': ['بانک رفاه کارگران', '#245A8D'],
    '502229': ['بانک پاسارگاد', '#7A1F3D'], '639347': ['بانک پاسارگاد', '#7A1F3D'],
    '621986': ['بانک سامان', '#1B6AA5'],
    '622106': ['بانک پارسیان', '#8C1D3F'], '639194': ['بانک پارسیان', '#8C1D3F'],
    '627412': ['بانک اقتصاد نوین', '#1E4C8A'],
    '627488': ['بانک کارآفرین', '#2A5CA8'], '502910': ['بانک کارآفرین', '#2A5CA8'],
    '502806': ['بانک شهر', '#B23A2E'], '504706': ['بانک شهر', '#B23A2E'],
    '502938': ['بانک دی', '#1F6F6B'],
    '639346': ['بانک سینا', '#245C93'],
    '636214': ['بانک آینده', '#7A3C8C'],
    '627760': ['پست بانک ایران', '#1F5C4A'],
    '639607': ['بانک سرمایه', '#2E4A7A'],
    '505785': ['بانک ایران زمین', '#1B5E7A'],
    '505416': ['بانک گردشگری', '#B5651D'],
    '606373': ['بانک قرض‌الحسنه مهر ایران', '#2F6B4F'],
    '504172': ['بانک رسالت', '#1E6B52'],
    '502908': ['بانک توسعه تعاون', '#2B5F8C'],
    '627961': ['بانک صنعت و معدن', '#3A4A6B'],
    '207177': ['بانک توسعه صادرات', '#1F4E7A'],
    '585949': ['بانک خاورمیانه', '#2C5E8A'],
    '606256': ['موسسه اعتباری ملل', '#1F5E4C'],
    '628157': ['موسسه اعتباری توسعه', '#4A4A7A'],
    '636949': ['بانک حکمت ایرانیان', '#2E5B7C'],
    '505801': ['بانک کوثر', '#1E6B5C']
  };

  var VTESTS = {
    ok: { card: '6037997599999993', nid: '0079845612', bd: '1370/05/17', match: true },
    no: { card: '6037997599999993', nid: '1288394576', bd: '1365/11/02', match: false }
  };
  (function applyVtestOverrides(){
    var ov = w.UID_CV_VTESTS_OVERRIDE;
    if(!ov) return;
    ['ok','no'].forEach(function(k){
      if(!ov[k]) return;
      if(ov[k].card) VTESTS[k].card = String(ov[k].card);
      if(ov[k].nid) VTESTS[k].nid = String(ov[k].nid);
      if(ov[k].bd) VTESTS[k].bd = String(ov[k].bd);
    });
  })();

  function onlyDigits(s) {
    return String(s).replace(/[۰-۹]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(x); })
      .replace(/[^0-9]/g, '');
  }
  function luhnOk(c16) {
    if (c16.length !== 16) return false;
    var sum = 0;
    for (var i = 0; i < 16; i++) {
      var n = +c16[15 - i];
      if (i % 2 === 1) { n *= 2; if (n > 9) n -= 9; }
      sum += n;
    }
    return sum % 10 === 0;
  }

  var vCard = d.getElementById('v_card');
  if (vCard) {
    var vNid = d.getElementById('v_nid'),
        vBd = d.getElementById('v_bd'),
        vStage = d.getElementById('v_stage'),
        vBank = d.getElementById('v_bank'),
        vCCnt = d.getElementById('v_ccnt'),
        vNCnt = d.getElementById('v_ncnt'),
        vLockT = d.getElementById('v_lockt'),
        vRun = d.getElementById('v_run'),
        vBadge = d.getElementById('v_badge'),
        vVerd = d.getElementById('v_verdict'),
        vVic = d.getElementById('v_vic'),
        vVtx = d.getElementById('v_vtx'),
        vVlat = d.getElementById('v_vlat'),
        vVsub = d.getElementById('v_vsub'),
        vLead = d.getElementById('vwLead'),
        vJson = d.getElementById('o_json'),
        face = d.getElementById('v_face'),
        cfBank = d.getElementById('cf_bank'),
        cfFlag = d.getElementById('cf_flag'),
        cfNum = d.getElementById('cf_num'),
        cfLuhn = d.getElementById('cf_luhn'),
        outV = {
          match: d.getElementById('o_match'), bank: d.getElementById('o_bank'),
          nid: d.getElementById('o_nid'), code: d.getElementById('o_code'),
          msg: d.getElementById('o_msg')
        };
    var vBusy = false;
    var cfCells = Array.prototype.slice.call(cfNum.children);

    var ICON_OK = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
    var ICON_NO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
    var ICON_WRN = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';

    function issuerOf(c) {
      if (c.length < 6) return null;
      return CARD_BINS[c.slice(0, 6)] || null;
    }

    function paintCard() {
      var raw = onlyDigits(vCard.value).slice(0, 16);
      var pretty = raw.replace(/(.{4})/g, '$1 ').trim();
      if (vCard.value !== pretty) vCard.value = pretty;

      vCCnt.textContent = fa(raw.length) + '/' + fa(16);
      vCCnt.classList.toggle('full', raw.length === 16);

      for (var g = 0; g < 4; g++) {
        var part = raw.slice(g * 4, g * 4 + 4);
        var cell = cfCells[g];
        if (part.length) {
          cell.textContent = part + '••••'.slice(part.length);
          cell.classList.toggle('empty', part.length < 4);
        } else {
          cell.textContent = '••••';
          cell.classList.add('empty');
        }
      }

      var iss = issuerOf(raw);
      if (iss) {
        vBank.textContent = iss[0];
        cfBank.textContent = iss[0];
        face.style.setProperty('--bk', iss[1]);
      } else if (raw.length >= 6) {
        vBank.textContent = 'بانک ناشناخته';
        cfBank.textContent = 'بانک شناسایی نشده';
        face.style.setProperty('--bk', '#2A4A86');
      } else {
        vBank.textContent = 'بانک شناسایی نشده';
        cfBank.textContent = 'در انتظار شماره کارت';
        face.style.setProperty('--bk', '#2A4A86');
      }
      cfFlag.textContent = raw.length >= 6 ? ('BIN ' + raw.slice(0, 6)) : 'BIN —';

      if (raw.length === 16) {
        var ok = luhnOk(raw);
        cfLuhn.textContent = ok ? 'ساختار: معتبر' : 'ساختار: نامعتبر';
        cfLuhn.className = 'luhn ' + (ok ? 'ok' : 'no');
      } else {
        cfLuhn.textContent = 'ساختار: —';
        cfLuhn.className = 'luhn';
      }
      softReset();
    }
    function paintNid() {
      var raw = onlyDigits(vNid.value).slice(0, 10);
      if (vNid.value !== raw) vNid.value = raw;
      vNCnt.textContent = fa(raw.length) + '/' + fa(10);
      vNCnt.classList.toggle('full', raw.length === 10);
      softReset();
    }
    function paintBd() {
      var raw = onlyDigits(vBd.value).slice(0, 8);
      var out = raw;
      if (raw.length > 6) out = raw.slice(0, 4) + '/' + raw.slice(4, 6) + '/' + raw.slice(6);
      else if (raw.length > 4) out = raw.slice(0, 4) + '/' + raw.slice(4);
      if (vBd.value !== out) vBd.value = out;
      softReset();
    }

    function softReset() {
      if (vBusy) return;
      vStage.classList.remove('ok', 'no', 'busy');
      vVerd.classList.remove('show', 'good', 'fail');
      vLockT.textContent = 'تطبیق مالکیت انجام نشده';
      var ready = onlyDigits(vCard.value).length === 16 &&
        onlyDigits(vNid.value).length === 10 &&
        onlyDigits(vBd.value).length === 8;
      vBadge.className = 'idbadge wait';
      vBadge.textContent = ready ? 'آماده تطبیق' : 'در انتظار ورودی';
    }

    function setRows() {
      d.querySelectorAll('#v_grid .idrow').forEach(function (r, i) {
        r.classList.remove('in');
        setTimeout(function () { r.classList.add('in'); }, 70 * i);
      });
    }

    function verdict(kind, c16, nid, bank, ms) {
      var bankTx = bank || 'نامشخص';
      if (kind === 'match') {
        vStage.classList.add('ok');
        vLockT.textContent = 'مالکیت کارت تایید شد';
        vVerd.className = 'vverdict good show';
        vVic.innerHTML = ICON_OK;
        vVtx.textContent = 'مالکیت تایید شد (Matched) — ' + bankTx;
        vVlat.textContent = ms + ' ms';
        vVsub.textContent = 'شماره کارت واردشده به همین کد ملی و تاریخ تولد تعلق دارد. ' +
          'سامانه شما می‌تواند ثبت کارت یا تراکنش را با اطمینان ادامه دهد.';
        vBadge.className = 'idbadge ok'; vBadge.textContent = 'پاسخ موفق';
        outV.match.textContent = 'true'; outV.match.className = 'v good';
        outV.code.textContent = '0'; outV.code.className = 'v good';
        outV.msg.textContent = 'SUCCESS.'; outV.msg.className = 'v';
        vJson.textContent =
          '{\n  "responseContext": {\n    "status": {\n      "code": 0,\n' +
          '      "message": "SUCCESS.",\n      "details": []\n    },\n' +
          '    "requestId": "",\n    "correlationId": ""\n  },\n' +
          '  "isMatched": true\n}';
      } else if (kind === 'mismatch') {
        vStage.classList.add('no');
        vLockT.textContent = 'مالکیت کارت تایید نشد';
        vVerd.className = 'vverdict fail show';
        vVic.innerHTML = ICON_NO;
        vVtx.textContent = 'عدم تطابق شماره کارت با کد ملی (Mismatched)';
        vVlat.textContent = ms + ' ms';
        vVsub.textContent = 'این کارت به کد ملی واردشده تعلق ندارد. ثبت کارت باید متوقف و پرونده ' +
          'به بررسی دستی هدایت شود — دقیقاً همان‌جایی که اجاره کارت متوقف می‌شود.';
        vBadge.className = 'idbadge ok'; vBadge.textContent = 'پاسخ موفق';
        outV.match.textContent = 'false'; outV.match.className = 'v bad';
        outV.code.textContent = '0'; outV.code.className = 'v good';
        outV.msg.textContent = 'SUCCESS.'; outV.msg.className = 'v';
        vJson.textContent =
          '{\n  "responseContext": {\n    "status": {\n      "code": 0,\n' +
          '      "message": "SUCCESS.",\n      "details": []\n    },\n' +
          '    "requestId": "",\n    "correlationId": ""\n  },\n' +
          '  "isMatched": false\n}';
      } else {
        vStage.classList.add('no');
        vLockT.textContent = 'ساختار شماره کارت نامعتبر است';
        vVerd.className = 'vverdict fail show';
        vVic.innerHTML = ICON_WRN;
        vVtx.textContent = 'خطای ساختار کارت (Invalid Card Number)';
        vVlat.textContent = ms + ' ms';
        vVsub.textContent = 'رقم کنترلی این شماره کارت معتبر نیست، بنابراین اصلاً به شبکه بانکی ' +
          'ارسال نمی‌شود. برای دیدن پاسخ واقعی سرویس از دو دکمه تست بالای همین کادر استفاده کنید.';
        vBadge.className = 'idbadge blocked'; vBadge.textContent = 'خطای ورودی — رایگان';
        outV.match.textContent = '—'; outV.match.className = 'v mut';
        outV.code.textContent = '-1'; outV.code.className = 'v bad';
        outV.msg.textContent = 'INVALID_CARD_NUMBER.'; outV.msg.className = 'v bad';
        vJson.textContent =
          '{\n  "responseContext": {\n    "status": {\n      "code": -1,\n' +
          '      "message": "INVALID_CARD_NUMBER.",\n      "details": []\n    }\n  },\n' +
          '  "isMatched": null\n}';
      }
      outV.bank.textContent = bankTx; outV.bank.className = bank ? 'v fa' : 'v mut';
      outV.nid.textContent = nid || '—'; outV.nid.className = nid ? 'v' : 'v mut';
      setRows();
      if (vLead) vLead.classList.add('show');
    }

    function run(forced) {
      if (vBusy) return;
      var c16 = onlyDigits(vCard.value).slice(0, 16),
          nid = onlyDigits(vNid.value).slice(0, 10),
          bd = onlyDigits(vBd.value).slice(0, 8);

      var miss = null;
      if (c16.length !== 16) miss = ['f_card', vCard, 'شماره کارت باید ۱۶ رقم باشد'];
      else if (nid.length !== 10) miss = ['f_nid', vNid, 'کد ملی باید ۱۰ رقم باشد'];
      else if (bd.length !== 8) miss = ['f_bd', vBd, 'تاریخ تولد را کامل وارد کنید'];
      if (miss) {
        var box = d.getElementById(miss[0]);
        box.classList.add('bad');
        setTimeout(function () { box.classList.remove('bad'); }, 2200);
        miss[1].focus();
        vBadge.className = 'idbadge blocked';
        vBadge.textContent = miss[2];
        return;
      }

      var iss = issuerOf(c16);
      var bank = iss ? iss[0] : null;
      var kind;
      if (forced === 'ok') kind = 'match';
      else if (forced === 'no') kind = 'mismatch';
      else if (!luhnOk(c16)) kind = 'invalid';
      else {
        var sum = 0, i;
        for (i = 0; i < c16.length; i++) sum += +c16[i];
        for (i = 0; i < nid.length; i++) sum += +nid[i];
        kind = (sum % 2 === 0) ? 'match' : 'mismatch';
      }

      vBusy = true;
      vStage.classList.remove('ok', 'no');
      vVerd.classList.remove('show', 'good', 'fail');
      vStage.classList.add('busy');
      vLockT.textContent = 'در حال استعلام برخط مالکیت کارت…';
      vBadge.className = 'idbadge load';
      vBadge.textContent = 'در حال تطبیق با اطلاعات صاحب کارت…';
      var ms = kind === 'invalid' ? 11 : (150 + Math.round(Math.random() * 60));
      setTimeout(function () {
        vStage.classList.remove('busy');
        vBusy = false;
        verdict(kind, c16, nid, bank, ms);
      }, reduce ? 120 : 1100);
    }

    vCard.addEventListener('input', paintCard);
    vNid.addEventListener('input', paintNid);
    vBd.addEventListener('input', paintBd);
    [vCard, vNid, vBd].forEach(function (el) {
      el.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); run(); } });
    });
    vRun.addEventListener('click', function () { run(); });

    d.querySelectorAll('#v_tests [data-vtest]').forEach(function (b) {
      b.addEventListener('click', function () {
        var t = VTESTS[b.dataset.vtest];
        vCard.value = t.card; vNid.value = t.nid; vBd.value = t.bd;
        paintCard(); paintNid(); paintBd();
        run(b.dataset.vtest);
      });
    });

    d.querySelectorAll('.uid-card-validate-page [data-rview]').forEach(function (b) {
      b.addEventListener('click', function () {
        d.querySelectorAll('.uid-card-validate-page [data-rview]').forEach(function (x) { x.classList.remove('on'); });
        b.classList.add('on');
        d.querySelectorAll('.uid-card-validate-page [data-rpane]').forEach(function (p) {
          p.classList.toggle('on', p.dataset.rpane === b.dataset.rview);
        });
      });
    });
  }

  /* ═════════════════════════════════════════════════════════════════════
     نحوه کار سرویس — استپر چهارمرحله‌ای
     ═════════════════════════════════════════════════════════════════════ */
  var stTrack = d.getElementById('st_track');
  if (stTrack) {
    var stNodes = Array.prototype.slice.call(stTrack.querySelectorAll('.st-node')),
        stPanes = Array.prototype.slice.call(d.querySelectorAll('[data-st-pane]')),
        stPrev = d.getElementById('st_prev'),
        stNext = d.getElementById('st_next'),
        stCount = d.getElementById('st_count'),
        stI = 0;

    function stPaint() {
      stNodes.forEach(function (n, i) {
        n.classList.toggle('on', i === stI);
        n.classList.toggle('done', i <= stI);
        n.setAttribute('aria-selected', i === stI ? 'true' : 'false');
      });
      stPanes.forEach(function (p, i) { p.hidden = (i !== stI); });
      stCount.textContent = fa(stI + 1) + ' / ' + fa(stNodes.length);
      stPrev.disabled = stI === 0;
      stNext.disabled = stI === stNodes.length - 1;
      var el = stNodes[stI];
      if (el) {
        var er = el.getBoundingClientRect(), tr = stTrack.getBoundingClientRect();
        if (er.left < tr.left || er.right > tr.right) {
          stTrack.scrollTo({ left: el.offsetLeft - stTrack.clientWidth / 2 + el.offsetWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
        }
      }
    }
    stNodes.forEach(function (n, i) { n.addEventListener('click', function () { stI = i; stPaint(); }); });
    stPrev.addEventListener('click', function () { if (stI > 0) { stI--; stPaint(); } });
    stNext.addEventListener('click', function () { if (stI < stNodes.length - 1) { stI++; stPaint(); } });
    stPaint();
  }

  /* ═════════════════════════════════════════════════════════════════════
     محاسبه‌گر ریسک کارت تاییدنشده — هر عدد، عدد خودِ بازدیدکننده است
     ═════════════════════════════════════════════════════════════════════ */
  var lsTx = d.getElementById('ls_tx');
  if (lsTx) {
    var lsRate = d.getElementById('ls_rate'), lsCost = d.getElementById('ls_cost'),
        txV = d.getElementById('ls_tx_v'), rateV = d.getElementById('ls_rate_v'),
        costV = d.getElementById('ls_cost_v'), failEl = d.getElementById('ls_fail'),
        saveEl = d.getElementById('ls_save'), totEl = d.getElementById('ls_total'),
        dayEl = d.getElementById('ls_daily'), lossVol = d.getElementById('lossVolume');

    var INTERCEPT = 0.95;

    function lsPaint() {
      var tx = parseInt(lsTx.value, 10);
      var rate = parseInt(lsRate.value, 10) / 10;
      var cost = parseInt(lsCost.value, 10);

      var fail = Math.round(tx * rate / 100);
      var save = Math.round(fail * INTERCEPT);
      var total = save * cost;

      txV.textContent = faNum(tx);
      rateV.innerHTML = faDec(rate, 1) + '<small>درصد</small>';
      costV.textContent = faNum(cost);
      failEl.textContent = faNum(fail);
      saveEl.textContent = faNum(save);
      totEl.textContent = faNum(total);
      dayEl.textContent = faNum(total / 30);
      if (lossVol) lossVol.value = tx;
    }
    [lsTx, lsRate, lsCost].forEach(function (el) { el.addEventListener('input', lsPaint); });
    lsPaint();
  }

  /* ═════════════════════════════════════════════════════════════════════
     محاسبه‌گر هزینه ماهانه
     ═════════════════════════════════════════════════════════════════════ */
  var BASE_UNIT = 2900;
  var TIERS = [
    { max: 5000, plan: 'پلن استارتاپ', off: 0 },
    { max: 15000, plan: 'پلن رشد', off: 0.08 },
    { max: 30000, plan: 'پلن رشد', off: 0.14 },
    { max: 50000, plan: 'پلن رشد', off: 0.20 },
    { max: Infinity, plan: 'پلن سازمانی', off: null }
  ];

  var range = d.getElementById('cl_range');
  if (range) {
    var qtyEl = d.getElementById('cl_qty'), planEl = d.getElementById('cl_plan'),
        offEl = d.getElementById('cl_off'), nEl = d.getElementById('cl_n'),
        unitEl = d.getElementById('cl_unit'), grossEl = d.getElementById('cl_gross'),
        totalEl = d.getElementById('cl_total'), presets = d.getElementById('cl_presets'),
        hintEl = d.getElementById('cl_hint'), volField = d.getElementById('leadVolume');

    var MIN = 1000, MAX = 500000;
    function posToQty(p) {
      var q = MIN * Math.pow(MAX / MIN, p / 1000);
      if (q < 5000) return Math.round(q / 250) * 250;
      if (q < 50000) return Math.round(q / 500) * 500;
      if (q < 200000) return Math.round(q / 2500) * 2500;
      return Math.round(q / 10000) * 10000;
    }
    function qtyToPos(q) {
      return Math.round(1000 * Math.log(Math.max(MIN, Math.min(MAX, q)) / MIN) / Math.log(MAX / MIN));
    }
    function tierFor(q) {
      for (var t = 0; t < TIERS.length; t++) { if (q <= TIERS[t].max) return TIERS[t]; }
      return TIERS[TIERS.length - 1];
    }

    function paint() {
      var q = posToQty(parseInt(range.value, 10));
      var t = tierFor(q);
      qtyEl.textContent = faNum(q);
      nEl.textContent = faNum(q);
      planEl.textContent = t.plan;
      if (volField) volField.value = q;

      if (t.off === null) {
        offEl.textContent = 'تعرفه اختصاصی';
        unitEl.textContent = 'قراردادی';
        grossEl.textContent = '—';
        totalEl.textContent = 'استعلام قیمت';
        if (hintEl) hintEl.textContent = 'در پلن سازمانی، تعرفه اختصاصی و پشتیبانی ۲۴ ساعته اختصاصی برای شما تعریف می‌شود.';
      } else {
        var unit = Math.round(BASE_UNIT * (1 - t.off));
        offEl.textContent = t.off > 0 ? fa(Math.round(t.off * 100)) + '٪ تخفیف پلکانی' : 'بدون پله تخفیف';
        unitEl.textContent = faNum(unit) + ' تومان';
        grossEl.textContent = faNum(q * BASE_UNIT) + ' تومان';
        totalEl.textContent = faNum(q * unit) + ' تومان';
        if (hintEl) hintEl.textContent = 'استعلام‌های ناموفق و کارت‌های نامعتبر در این محاسبه لحاظ نمی‌شوند.';
      }

      if (presets) {
        presets.querySelectorAll('.calc-preset').forEach(function (b) {
          b.classList.toggle('on', Math.abs(parseInt(b.dataset.qty, 10) - q) < q * 0.06);
        });
      }
    }

    range.addEventListener('input', paint);
    if (presets) {
      presets.querySelectorAll('.calc-preset').forEach(function (b) {
        b.addEventListener('click', function () {
          range.value = qtyToPos(parseInt(b.dataset.qty, 10)); paint();
        });
      });
    }
    paint();
  }
})();
