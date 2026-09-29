/**
 * رفتار تعاملی اختصاصی صفحه «تبدیل شماره کارت به شبا» — فقط روی این قالب صفحه
 * بارگذاری می‌شود. نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال
 * درخواست تماس، دکمه‌های شناور تماس و بازگشت به بالا از هدر/فوتر مشترک سایت
 * (assets/js/main.js) می‌آیند — اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
 *
 * سیستم «فصل تاخوردنی + خواننده فصل» موبایل (accordion + نوار چسبان + دکمه‌های
 * قبلی/بعدی + نوار پیشرفت مطالعه) دقیقاً مثل assets/js/ekyc-liveness-page.js است.
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
  d.querySelectorAll('.uid-card-to-iban-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-card-to-iban-page .fld input, .uid-card-to-iban-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: کاربران شخصی را به جای صف فروش، به صفحه درست هدایت کن ---- */
  var sel = d.querySelector('.uid-card-to-iban-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  d.querySelectorAll('.uid-card-to-iban-page .rv, .uid-card-to-iban-page .fnl, .uid-card-to-iban-page [data-count]').forEach(function (el) {
    if (io) io.observe(el); else el.classList.add('is-in');
  });

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
  d.querySelectorAll('.uid-card-to-iban-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-card-to-iban-page .faq-q').forEach(function (q) {
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
  d.querySelectorAll('.uid-card-to-iban-page [data-rail]').forEach(function (rail) {
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
  var codeTabs = d.querySelectorAll('.uid-card-to-iban-page [data-code-tab]');
  codeTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      codeTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.codeTab;
      d.querySelectorAll('.uid-card-to-iban-page [data-code-pane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.codePane === key);
      });
    });
  });
  var copyBtn = d.querySelector('.uid-card-to-iban-page [data-copy]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var active = d.querySelector('.uid-card-to-iban-page .code-pane.on');
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

  /* ───────────── CARD DECK ─────────────
     Below 720px a grid of cards collapses into a physical stack: one card
     visible, two peeking behind it. */
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
  d.querySelectorAll('.uid-card-to-iban-page [data-deck]').forEach(function (dk) { DECKS.push(initDeck(dk)); });

  /* A deck measures its tallest card while the cards are still in normal flow.
     Inside a collapsed chapter that measurement runs against a zero-height box,
     so the fold system calls this the instant a chapter is opened. */
  w.__remeasureDecks = function (scope) {
    DECKS.forEach(function (fn) {
      if (!fn) return;
      if (scope && !scope.contains(fn.el)) return;
      fn.refresh();
    });
  };

  /* ───────────── INDUSTRY PICKER ───────────── */
  d.querySelectorAll('.uid-card-to-iban-page [data-pick]').forEach(function (pick) {
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
     چهار پله جابه‌جایی + سیستم فولد فصل و خواننده فصل
     ─────────────────────────────────────────────────────────────────────
     خودِ فولد همان مکانیزم صفحات قبلی است: زیر ۹۰۰px هر بخش [data-fold] پشت
     یک ردیف تک‌خطی جمع می‌شود. لایه‌ی «خواننده» روی آن اضافه شده:
       · accordion — همیشه فقط یک فصل باز است؛
       · نوار چسبان شماره/عنوان/دکمه بستن، هنگام خواندن فصل بالای صفحه می‌ماند؛
       · هر فصل با دکمه‌های قبلی/بعدی و CTA صفحه تمام می‌شود؛
       · نوار پیشرفت مطالعه (fold-meter) با باز شدن هر فصل جلو می‌رود.
     دسکتاپ کاملاً دست‌نخورده می‌ماند.
     ═════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('.uid-card-to-iban-page [data-fold]'));
  var meter = d.getElementById('ciFoldMeter');

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
    var bodyId = 'uid-ci-fold-body-' + (idx + 1);
    body.id = bodyId;
    btn.setAttribute('aria-controls', bodyId);
    btn.innerHTML =
      '<span class="fnum">' + fa(idx + 1) + '</span>' +
      '<span class="ftx"><b></b><span></span></span>' +
      (sec.hasAttribute('data-fold-hot') ? '<span class="fdot"></span>' : '') +
      '<svg class="fchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
      'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' +
      '<path d="M6 9l6 6 6-6"/></svg>';
    btn.querySelector('.ftx b').textContent = title;
    btn.querySelector('.ftx span').textContent = sec.dataset.foldTeaser || '';
    sec.insertBefore(btn, sec.firstChild);

    /* نوار چسبان خواننده — عمداً بیرون از .fold-body است چون آن overflow:hidden
       دارد (برای انیمیشن ارتفاع) و position:sticky داخلش کار نمی‌کند. */
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
        'درخواست فعال‌سازی وب‌سرویس</button>';
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

  var ciFoldAll = d.getElementById('ciFoldAll');
  if (ciFoldAll) {
    ciFoldAll.addEventListener('click', function () {
      var anyClosed = folds.some(function (s) { return !s.classList.contains('open'); });
      folds.forEach(function (s) {
        if (s._foldOpen) s._foldOpen(anyClosed, true);
        if (anyClosed) s.classList.add('read');
      });
      paintMeter();
      ciFoldAll.textContent = anyClosed ? 'بستن همه' : 'باز کردن همه';
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
      if (ciFoldAll) ciFoldAll.textContent = 'باز کردن همه';
    }
  }
  if (mqFold.addEventListener) mqFold.addEventListener('change', syncFold);
  else if (mqFold.addListener) mqFold.addListener(syncFold);
  syncFold();
  paintMeter();

  /* دکمه‌های CTA داخل فوتر فصل بعد از باند شدن [data-open-modal] مشترک اضافه
     می‌شوند، پس باید همین‌جا هم بایند شوند (سازگار با الگوی ekyc-liveness-page.js
     — روی این تم، این ویجت نمایشی هنوز به مودال واقعی سایت وصل نیست). */
  d.querySelectorAll('.uid-card-to-iban-page .chap-nav [data-open-modal]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      var m = d.getElementById('modal');
      if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
    });
  });

  /* ---- افست نرم لنگر داخلی (فصل بسته را ابتدا باز می‌کند) ---- */
  d.querySelectorAll('.uid-card-to-iban-page a[href^="#"]').forEach(function (a) {
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

  /* ---- کپسول ۳۰ ثانیه‌ای: پرش نرم به محاسبه‌گر (چه داخل فصل بسته باشد چه نباشد) ---- */
  d.querySelectorAll('.uid-card-to-iban-page [data-jump-soft]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = d.querySelector(b.dataset.jumpSoft);
      if (!t) return;
      if (w.__openFoldFor) w.__openFoldFor(t);
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 76, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ═════════════════════════════════════════════════════════════════════
     هیرو — ویجت زنده تبدیل کارت به شبا (شبیه‌سازی نمایشی، داده آن هاردکد است)
     ─────────────────────────────────────────────────────────────────────
     پیش‌شماره ۶ رقمی بانک صادرکننده را شناسایی می‌کند و یک شبای ۲۶ کاراکتری
     ساختاری معتبر (ISO 7064 mod-97) برای همان کارت می‌سازد — دقیقاً ساختار
     واقعی پاسخ سرویس inquiry/card، با داده نمونه.
     ═════════════════════════════════════════════════════════════════════ */
  var BINS = {
    '603799': ['بانک ملی ایران', '017'], '170019': ['بانک ملی ایران', '017'],
    '610433': ['بانک ملت', '012'], '991975': ['بانک ملت', '012'],
    '603769': ['بانک صادرات ایران', '019'],
    '627353': ['بانک تجارت', '018'], '585983': ['بانک تجارت', '018'],
    '589210': ['بانک سپه', '015'],
    '589463': ['بانک رفاه کارگران', '013'],
    '603770': ['بانک کشاورزی', '016'], '639217': ['بانک کشاورزی', '016'],
    '628023': ['بانک مسکن', '014'],
    '502229': ['بانک پاسارگاد', '057'], '639347': ['بانک پاسارگاد', '057'],
    '621986': ['بانک سامان', '056'],
    '622106': ['بانک پارسیان', '054'], '639194': ['بانک پارسیان', '054'],
    '627412': ['بانک اقتصاد نوین', '055'],
    '627488': ['بانک کارآفرین', '053'],
    '502806': ['بانک شهر', '061'], '504706': ['بانک شهر', '061'],
    '502938': ['بانک دی', '066'],
    '639346': ['بانک سینا', '059'],
    '639607': ['بانک سرمایه', '058'],
    '636214': ['بانک آینده', '062'],
    '505785': ['بانک ایران زمین', '069'],
    '505416': ['بانک گردشگری', '064'],
    '606373': ['بانک قرض‌الحسنه مهر ایران', '060'],
    '606256': ['موسسه اعتباری ملل', '075'],
    '504172': ['بانک رسالت', '070'],
    '627760': ['پست بانک ایران', '021'],
    '502908': ['بانک توسعه تعاون', '022'],
    '627961': ['بانک صنعت و معدن', '011'],
    '627648': ['بانک توسعه صادرات', '020'],
    '505809': ['بانک خاورمیانه', '078']
  };

  function mod97(s) {
    var rem = 0;
    for (var i = 0; i < s.length; i++) { rem = (rem * 10 + (s.charCodeAt(i) - 48)) % 97; }
    return rem;
  }
  function sampleIban(card, bankCode) {
    var acct = '0' + card.slice(6) + '00000000';
    var bban = bankCode + acct;
    var chk = 98 - mod97(bban + '182700');
    return 'IR' + (chk < 10 ? '0' : '') + chk + bban;
  }
  function groupIban(ib) {
    var g = ib.replace(/(.{4})/g, '$1 ').trim();
    return g.replace(/^IR/, '<span class="ir">IR</span>');
  }
  function digitsOnly(s) {
    return String(s).replace(/[۰-۹]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(x); })
      .replace(/[^0-9]/g, '');
  }

  var cdIn = d.getElementById('cd_in');
  if (cdIn) {
    var cdCard = d.getElementById('cd_card'),
        cdCnt = d.getElementById('cd_cnt'),
        cdUl = d.getElementById('cd_ul'),
        cdBank = d.getElementById('cd_bank'),
        cdBank2 = d.getElementById('cd_bank2'),
        cdIban = d.getElementById('cd_iban'),
        cdRun = d.getElementById('cd_run'),
        cdCopy = d.getElementById('cd_copy'),
        cdBadge = d.getElementById('cd_badge'),
        cdErr = d.getElementById('cd_err'),
        cdLead = d.getElementById('cdwLead'),
        cdJson = d.getElementById('c_json'),
        outC = {
          iban: d.getElementById('c_iban'), card: d.getElementById('c_card'),
          bank: d.getElementById('c_bank'), code: d.getElementById('c_code'),
          msg: d.getElementById('c_msg')
        };
    var lastIban = '', busy = false;

    function bankFor(raw) {
      if (raw.length < 6) return null;
      return BINS[raw.slice(0, 6)] || null;
    }

    function paintCard() {
      var raw = digitsOnly(cdIn.value).slice(0, 16);
      var pretty = raw.replace(/(.{4})/g, '$1 ').trim();
      if (cdIn.value !== pretty) cdIn.value = pretty;

      cdCnt.textContent = fa(raw.length) + '/' + fa(16);
      cdCnt.classList.toggle('full', raw.length === 16);
      cdUl.style.width = (raw.length / 16 * 100) + '%';

      var bank = bankFor(raw);
      if (bank) { cdBank.textContent = bank[0]; cdBank.classList.remove('mut'); }
      else if (raw.length >= 6) { cdBank.textContent = 'بانک شناسایی نشد'; cdBank.classList.add('mut'); }
      else { cdBank.textContent = 'با ورود ۶ رقم اول شناسایی می‌شود'; cdBank.classList.add('mut'); }

      if (cdCard.classList.contains('flip')) {
        cdCard.classList.remove('flip');
        cdRun.innerHTML = runLabel('تبدیل به شبا');
      }
      cdCard.classList.remove('bad');
      if (!busy) {
        cdBadge.className = 'idbadge wait';
        cdBadge.textContent = raw.length === 16 ? 'آماده استعلام' : 'در انتظار ورودی';
      }
    }

    function runLabel(tx) {
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
        'stroke-linecap="round" stroke-linejoin="round"><path d="M4 4v6h6M20 20v-6h-6"/>' +
        '<path d="M20 9a8 8 0 00-14-3L4 8M4 15a8 8 0 0014 3l2-2"/></svg>' + tx;
    }

    function setRows(state) {
      var rows = d.querySelectorAll('#c_grid .idrow');
      rows.forEach(function (r, i) {
        r.classList.remove('in');
        setTimeout(function () { r.classList.add('in'); }, state ? 70 * i : 0);
      });
    }

    function reject(msg) {
      cdCard.classList.add('bad');
      cdErr.textContent = msg;
      cdBadge.className = 'idbadge blocked';
      cdBadge.textContent = 'استعلام ناموفق — رایگان';
      outC.iban.textContent = '—'; outC.iban.className = 'v mut';
      outC.card.textContent = digitsOnly(cdIn.value).slice(0, 16) || '—';
      outC.card.className = 'v';
      outC.bank.textContent = '—'; outC.bank.className = 'v mut';
      outC.code.textContent = '-1'; outC.code.className = 'v bad';
      outC.msg.textContent = 'INVALID_CARD_NUMBER.'; outC.msg.className = 'v bad';
      cdJson.textContent =
        '{\n  "responseContext": {\n    "status": {\n      "code": -1,\n' +
        '      "message": "INVALID_CARD_NUMBER.",\n      "details": []\n    }\n  },\n' +
        '  "cardNumber": "' + (digitsOnly(cdIn.value).slice(0, 16)) + '",\n  "iban": null\n}';
      setRows(true);
    }

    function resolve(raw, bank) {
      var ib = sampleIban(raw, bank[1]);
      lastIban = ib;
      cdIban.innerHTML = groupIban(ib);
      cdBank2.textContent = bank[0];
      cdCard.classList.add('flip');
      cdBadge.className = 'idbadge ok';
      cdBadge.textContent = 'پاسخ موفق';
      outC.iban.textContent = ib; outC.iban.className = 'v good';
      outC.card.textContent = raw; outC.card.className = 'v';
      outC.bank.textContent = bank[0]; outC.bank.className = 'v fa';
      outC.code.textContent = '0'; outC.code.className = 'v good';
      outC.msg.textContent = 'SUCCESS.'; outC.msg.className = 'v';
      cdJson.textContent =
        '{\n  "responseContext": {\n    "status": {\n      "code": 0,\n' +
        '      "message": "SUCCESS.",\n      "details": []\n    }\n  },\n' +
        '  "cardNumber": "' + raw + '",\n  "iban": "' + ib + '"\n}';
      setRows(true);
      cdRun.innerHTML = runLabel('تبدیل یک کارت دیگر');
      if (cdLead) cdLead.classList.add('show');
    }

    function run() {
      if (busy) return;
      var raw = digitsOnly(cdIn.value).slice(0, 16);
      if (raw.length < 16) {
        cdIn.focus();
        cdBadge.className = 'idbadge blocked';
        cdBadge.textContent = 'شماره کارت باید ۱۶ رقم باشد';
        return;
      }
      var bank = bankFor(raw);
      busy = true;
      cdCard.classList.add('busy');
      cdCard.classList.remove('bad', 'flip');
      cdBadge.className = 'idbadge load';
      cdBadge.textContent = 'در حال استعلام از شبکه بانکی…';
      setTimeout(function () {
        cdCard.classList.remove('busy');
        busy = false;
        if (bank) resolve(raw, bank);
        else reject('این پیش‌شماره متعلق به هیچ بانک عضو شتابی نیست.');
      }, reduce ? 120 : 1150);
    }

    cdIn.addEventListener('input', paintCard);
    cdRun.addEventListener('click', run);
    cdIn.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); run(); } });

    d.querySelectorAll('#cd_samples .bsample').forEach(function (b) {
      b.addEventListener('click', function () {
        cdIn.value = b.dataset.card;
        paintCard();
        run();
      });
    });

    if (cdCopy) {
      cdCopy.addEventListener('click', function () {
        if (!lastIban) return;
        var done = function () {
          cdCopy.classList.add('done');
          cdCopy.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
            'stroke-width="2.6" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>کپی شد';
          setTimeout(function () {
            cdCopy.classList.remove('done');
            cdCopy.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
              'stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
              '<rect x="9" y="9" width="12" height="12" rx="2"/>' +
              '<path d="M5 15V5a2 2 0 012-2h10"/></svg>کپی شبا';
          }, 1700);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(lastIban).then(done).catch(done);
        else done();
      });
    }

    d.querySelectorAll('.uid-card-to-iban-page [data-rview]').forEach(function (b) {
      b.addEventListener('click', function () {
        d.querySelectorAll('.uid-card-to-iban-page [data-rview]').forEach(function (x) { x.classList.remove('on'); });
        b.classList.add('on');
        d.querySelectorAll('.uid-card-to-iban-page [data-rpane]').forEach(function (p) {
          p.classList.toggle('on', p.dataset.rpane === b.dataset.rview);
        });
      });
    });

    setRows(false);
  }

  /* ═════════════════════════════════════════════════════════════════════
     نحوه کار سرویس — پله تعاملی چهارمرحله‌ای
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
     محاسبه‌گر ریزش فیلد شبا — هر عدد، عدد خودِ بازدیدکننده است
     ═════════════════════════════════════════════════════════════════════ */
  var lsTx = d.getElementById('ls_tx');
  if (lsTx) {
    var lsRate = d.getElementById('ls_rate'), lsCost = d.getElementById('ls_cost'),
        txV = d.getElementById('ls_tx_v'), rateV = d.getElementById('ls_rate_v'),
        costV = d.getElementById('ls_cost_v'), failEl = d.getElementById('ls_fail'),
        saveEl = d.getElementById('ls_save'), totEl = d.getElementById('ls_total'),
        dayEl = d.getElementById('ls_daily'), lossVol = d.getElementById('lossVolume');

    /* سهمی از ریزش «رفتن برای پیدا کردن شبا» که تبدیل خودکار برمی‌گرداند —
       عمداً محافظه‌کارانه؛ فقط همین یک ثابت را عوض کنید. */
    var RECOVERY = 0.70;

    function lsPaint() {
      var tx = parseInt(lsTx.value, 10);
      var rate = parseInt(lsRate.value, 10) / 10;
      var cost = parseInt(lsCost.value, 10);

      var fail = Math.round(tx * rate / 100);
      var save = Math.round(fail * RECOVERY);
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
     محاسبه‌گر تعرفه ماهانه
     ─────────────────────────────────────────────────────────────────────
     ⚠ تعرفه‌ها نمایشی است. برای انتشار نهایی فقط همین دو ثابت را با تعرفه
       واقعی یوآیدی جایگزین کنید — بقیه صفحه نیازی به تغییر ندارد.
         BASE_UNIT → قیمت پایه هر تبدیل موفق کارت به شبا (تومان)
         TIERS     → پله‌های تخفیف بر اساس حجم ماهانه
     ═════════════════════════════════════════════════════════════════════ */
  var BASE_UNIT = 2500;
  var TIERS = [
    { max: 5000, name: 'پلن پایه', off: 0 },
    { max: 25000, name: 'پلن رشد', off: 0.10 },
    { max: 100000, name: 'پلن کسب‌وکار', off: 0.18 },
    { max: 500000, name: 'پلن سازمانی', off: 0.28 },
    { max: Infinity, name: 'پلن اختصاصی', off: null }
  ];

  var range = d.getElementById('cl_range');
  if (range) {
    var qtyEl = d.getElementById('cl_qty'), planEl = d.getElementById('cl_plan'),
        offEl = d.getElementById('cl_off'), nEl = d.getElementById('cl_n'),
        unitEl = d.getElementById('cl_unit'), grossEl = d.getElementById('cl_gross'),
        totalEl = d.getElementById('cl_total'), presets = d.getElementById('cl_presets'),
        volField = d.getElementById('leadVolume');

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
      planEl.textContent = t.name;
      if (volField) volField.value = q;

      if (t.off === null) {
        offEl.textContent = 'تعرفه قراردادی';
        unitEl.textContent = 'اختصاصی';
        grossEl.textContent = '—';
        totalEl.textContent = 'استعلام قیمت';
      } else {
        var unit = Math.round(BASE_UNIT * (1 - t.off));
        offEl.textContent = t.off > 0 ? fa(Math.round(t.off * 100)) + '٪ تخفیف پلکانی' : 'بدون پله تخفیف';
        unitEl.textContent = faNum(unit) + ' تومان';
        grossEl.textContent = faNum(q * BASE_UNIT) + ' تومان';
        totalEl.textContent = faNum(q * unit) + ' تومان';
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
