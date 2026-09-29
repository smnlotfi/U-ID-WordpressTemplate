/**
 * رفتار تعاملی اختصاصی صفحه «ثبت‌نام ثنا ویژه ایرانیان خارج از کشور» — فقط روی
 * این قالب صفحه بارگذاری می‌شود. نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو،
 * شیت موبایل، مودال درخواست تماس، دکمه‌های شناور تماس و بازگشت به بالا از
 * هدر/فوتر مشترک سایت (assets/js/main.js) می‌آیند — اینجا عمداً تکرار نشده‌اند.
 *
 * سیستم «فصل تاخوردنی + کتاب‌خوان فصل» موبایل دقیقاً مثل
 * assets/js/ekyc-liveness-page.js است؛ علاوه بر آن، این صفحه یک فیلتر
 * «فقط بخش‌های ضروری» هم دارد که فصل‌های data-fold-hot را از بقیه جدا می‌کند
 * (شناسه‌ها بدون پیشوند: foldbar/foldMeter/foldAll/foldFilter — دقیقاً مطابق
 * خروجی uid_render_sa_foldbar()).
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
  d.querySelectorAll('.uid-sana-abroad-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-sana-abroad-page .fld input, .uid-sana-abroad-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var wr = f.closest('.fld'); if (wr) wr.classList.remove('bad'); });
    f.addEventListener('change', function () { var wr = f.closest('.fld'); if (wr) wr.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: کاربران داخل ایران را به جای صف فروش، به صفحه درست هدایت کن ---- */
  var sel = d.querySelector('.uid-sana-abroad-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  d.querySelectorAll('.uid-sana-abroad-page .rv, .uid-sana-abroad-page .fnl, .uid-sana-abroad-page [data-count]').forEach(function (el) {
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
  d.querySelectorAll('.uid-sana-abroad-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-sana-abroad-page .faq-q').forEach(function (q) {
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
  d.querySelectorAll('.uid-sana-abroad-page [data-rail]').forEach(function (rail) {
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
  d.querySelectorAll('.uid-sana-abroad-page [data-deck]').forEach(function (dk) { DECKS.push(initDeck(dk)); });

  w.__remeasureDecks = function (scope) {
    DECKS.forEach(function (fn) {
      if (!fn) return;
      if (scope && !scope.contains(fn.el)) return;
      fn.refresh();
    });
  };

  /* ───────────── INDUSTRY PICKER ───────────── */
  d.querySelectorAll('.uid-sana-abroad-page [data-pick]').forEach(function (pick) {
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
     (شناسه‌ها بدون پیشوند: foldbar/foldMeter/foldAll/foldFilter/chapters)
     ═════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('.uid-sana-abroad-page [data-fold]'));
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
    var bodyId = 'fold-body-' + (idx + 1);
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
        'ثبت سفارش ثنا ویژه ایرانیان خارج از کشور</button>';
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

  /* ── فیلتر «فقط بخش‌های ضروری» ────────────────────────────────────── */
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

  d.querySelectorAll('.uid-sana-abroad-page .chap-nav [data-open-modal]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      var m = d.getElementById('modal');
      if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
    });
  });

  /* ---- افست نرم لنگر داخلی (فصل بسته را ابتدا باز می‌کند) ---- */
  d.querySelectorAll('.uid-sana-abroad-page a[href^="#"]').forEach(function (a) {
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

  /* ═════════════════════════════════════════════════════════════════════
     ساعت تهران — ابزار فوریت صادقانه (شبیه‌سازی نمایشی نیست: ساعت واقعی
     بازدیدکننده به وقت تهران تبدیل می‌شود). ⚠ ساعات کاری قابل تنظیم: فقط
     همین دو ثابت را تغییر دهید.
     ═════════════════════════════════════════════════════════════════════ */
  var OPEN_H = 9, CLOSE_H = 17, WEEKEND = [5]; /* پنجشنبه نیمه‌وقت درنظر گرفته نشده */

  (function () {
    var box = d.getElementById('thclock');
    if (!box) return;
    var txEl = d.getElementById('thclockTx'), nowEl = d.getElementById('thclockNow');

    function tehran() {
      var n = new Date();
      return new Date(n.getTime() + n.getTimezoneOffset() * 60000 + 3.5 * 3600000);
    }
    function two(n) { return (n < 10 ? '0' : '') + n; }

    function paint() {
      var t = tehran();
      var h = t.getHours(), m = t.getMinutes();
      var isWeekend = WEEKEND.indexOf(t.getDay()) > -1;
      var open = !isWeekend && h >= OPEN_H && h < CLOSE_H;

      nowEl.textContent = fa(two(h) + ':' + two(m));
      box.classList.toggle('shut', !open);

      if (open) {
        var left = (CLOSE_H - h - 1) * 60 + (60 - m);
        txEl.innerHTML = '<b>باجه پرداخت ریالی یوآیدی باز است</b> — ' +
          'تا پایان ساعت کاری امروز ' + fa(Math.floor(left / 60)) + ' ساعت و ' +
          fa(left % 60) + ' دقیقه فرصت دارید.';
      } else {
        var wait;
        if (isWeekend || h >= CLOSE_H) { wait = (24 - h + OPEN_H) * 60 - m; }
        else { wait = (OPEN_H - h) * 60 - m; }
        if (wait < 0) wait += 24 * 60;
        txEl.innerHTML = '<b>خارج از ساعت کاری ایران</b> — سفارشتان را همین حالا ثبت کنید؛ ' +
          'حدود ' + fa(Math.floor(wait / 60)) + ' ساعت دیگر در نوبت اول بررسی می‌شود.';
      }
    }
    paint();
    setInterval(paint, 30000);
  })();

  /* ═════════════════════════════════════════════════════════════════════
     کنسول کد ۱۶ رقمی (مرحله ۱۴ راهنما) — فقط دو بررسی صادقانه: عددی بودن و
     ۱۶ رقمی بودن؛ هیچ اعتبارسنجی واقعی با ثنا انجام نمی‌شود.
     ═════════════════════════════════════════════════════════════════════ */
  (function () {
    var input = d.getElementById('sanaCode');
    if (!input) return;
    var cells = d.getElementById('codeCells'),
        field = d.getElementById('codeField'),
        cnt = d.getElementById('codeCnt'),
        vd = d.getElementById('codeVerdict'),
        pasteBtn = d.getElementById('codePaste'),
        leadCode = d.getElementById('l_code');

    var html = '';
    for (var g = 0; g < 4; g++) {
      html += '<span class="code-grp">';
      for (var c = 0; c < 4; c++) html += '<span class="code-cell"></span>';
      html += '</span>';
    }
    cells.innerHTML = html;
    var boxes = cells.querySelectorAll('.code-cell');

    function digits() { return input.value.replace(/[^0-9]/g, '').slice(0, 16); }

    function paint() {
      var v = digits(), n = v.length, focused = d.activeElement === input;
      for (var i = 0; i < 16; i++) {
        var b = boxes[i];
        b.textContent = v[i] ? fa(v[i]) : '';
        b.classList.toggle('has', !!v[i]);
        b.classList.toggle('cur', focused && i === n && n < 16);
      }
      cnt.textContent = fa(n);
      field.classList.toggle('ok', n === 16);
      if (leadCode && n) leadCode.value = v;

      vd.classList.remove('ready', 'warn');
      var ic = vd.querySelector('.vic'), tx = vd.querySelector('.vtx');
      if (n === 0) {
        tx.innerHTML = '<b>منتظر کد شما هستیم</b><span>اگر هنوز به مرحله پرداخت نرسیده‌اید هم ' +
          'اشکالی ندارد — دکمه پایین را بزنید تا از ابتدا راهنمایی‌تان کنیم.</span>';
        ic.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 8v5M12 16.5h.01"/></svg>';
      } else if (n < 16) {
        vd.classList.add('warn');
        tx.innerHTML = '<b>' + fa(16 - n) + ' رقم دیگر مانده</b><span>کد صفحه پرداخت ثنا دقیقاً ۱۶ رقم ' +
          'است. اگر رقم‌ها را کامل ندارید، صفحه پرداخت ثنا را دوباره باز کنید.</span>';
        ic.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5M12 16.5h.01"/><path d="M10.3 3.9L2.4 18a2 2 0 001.7 3h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z"/></svg>';
      } else {
        vd.classList.add('ready');
        tx.innerHTML = '<b>کد شما کامل است — آماده ثبت سفارش</b><span>همین کد را در فرم ثبت سفارش ' +
          'وارد کنید تا پرداخت ریالی آن، داخل ایران توسط یوآیدی انجام شود.</span>';
        ic.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
      }
    }

    function reformat() {
      var v = digits();
      input.value = v.replace(/(.{4})/g, '$1 ').trim();
    }

    input.addEventListener('input', function () { reformat(); paint(); });
    input.addEventListener('focus', paint);
    input.addEventListener('blur', paint);
    field.addEventListener('click', function () { input.focus(); });

    if (pasteBtn) {
      pasteBtn.addEventListener('click', function () {
        if (navigator.clipboard && navigator.clipboard.readText) {
          navigator.clipboard.readText().then(function (t) {
            input.value = String(t || '').replace(/[^0-9]/g, '').slice(0, 16);
            reformat(); paint(); input.focus();
          }).catch(function () { input.focus(); });
        } else { input.focus(); }
      });
    }

    d.querySelectorAll('[data-code-demo]').forEach(function (b) {
      b.addEventListener('click', function () {
        if (b.dataset.codeDemo === 'ok') { input.value = '1234567890123456'; }
        else { input.value = ''; }
        reformat(); paint();
        if (b.dataset.codeDemo === 'ok') input.focus();
      });
    });

    paint();
  })();

  /* ═════════════════════════════════════════════════════════════════════
     ریل ۲۱ مرحله — متن هر مرحله عیناً از راهنمای ثبت‌نام یوآیدی گرفته شده
     ═════════════════════════════════════════════════════════════════════ */
  (function () {
    var reel = d.getElementById('reel');
    if (!reel) return;

    var STEPS = [
      { t: 'وارد سامانه ثنا شوید',
        x: 'در مرحله اول باید وارد سامانه ثنا شوید. ورود به سامانه ثنا برای ایرانیان خارج از کشور از طریق آدرس اختصاصی ثبت‌نام الکترونیک قضایی امکان‌پذیر است.' },
      { t: '«سامانه ثبت نام الکترونیک قضایی» را انتخاب کنید',
        x: 'از میان انتخاب‌های نمایش داده شده، گزینه «سامانه ثبت نام الکترونیک قضایی» را انتخاب کنید.' },
      { t: 'روی «سامانه اصلی ابلاغ الکترونیک قضایی» بزنید',
        x: 'در پنجره باز شده، روی «سامانه اصلی ابلاغ الکترونیک قضایی» کلیک کنید.' },
      { t: '«ثبت نام برخط شخص حقیقی» را بزنید',
        x: 'وارد سامانه شوید و روی گزینه «ثبت نام برخط شخص حقیقی» کلیک کنید.' },
      { t: 'اطلاعات شخصی خود را وارد کنید',
        x: 'اطلاعات شخصی خود از جمله کد ملی، تاریخ تولد و شماره سریال شناسنامه را وارد کنید و سپس کد امنیتی را درج کنید.' },
      { t: 'گزینه «ادامه» را بزنید',
        x: 'گزینه «ادامه» را فشار دهید و وارد مرحله بعد شوید.' },
      { t: 'شرایط استفاده را تایید کنید',
        x: 'شرایط استفاده از سامانه ثنا را مطالعه کنید و سپس روی گزینه تایید کلیک کنید و به مرحله بعد بروید.' },
      { t: 'اطلاعات شناسنامه‌ای را وارد کنید',
        x: 'در این مرحله، اطلاعات شناسنامه‌ای خود را وارد کنید و روی گزینه «مرحله بعد» کلیک کنید.' },
      { t: 'آیدی آی‌گپ خود را وارد کنید',
        x: 'در نسخه جدید international.adliran.ir، شما می‌توانید به جای شماره تلفن همراه و ثابت، آیدی پیام‌رسان آی‌گپ خود را وارد نمایید. برای دریافت آیدی آی‌گپ، کافی است که در این پلتفرم ثبت نام کرده باشید.',
        n: 'همین مرحله است که ثبت‌نام از خارج از کشور را ممکن می‌کند: <b>شماره تلفن ایرانی لازم نیست</b>.' },
      { t: 'آدرس محل سکونت یا محل کار را وارد کنید',
        x: 'اطلاعات مربوط به محل سکونت یا محل کار خود را وارد کنید. توجه داشته باشید که آدرس محل سکونت شما همان آدرسی است که اکنون در خارج از کشور در آن سکونت دارید.' },
      { t: 'تحصیلات و شغل را تعیین کنید',
        x: 'میزان تحصیلات و شغل خود را تعیین کنید و سپس روی دکمه «ثبت اطلاعات اولیه» کلیک کنید.' },
      { t: 'کد ارسال‌شده را وارد کنید',
        x: 'یک کد از طرف ثنا به آیدی که درج کرده‌اید ارسال می‌شود. کد را در قسمت مربوطه وارد نمایید.' },
      { t: 'احراز هویت آنلاین انجام می‌شود',
        x: 'در این مرحله باید احراز هویت آنلاین انجام شود.' },
      { t: 'کد ۱۶ رقمی صفحه پرداخت ثنا را کپی کنید', pay: true,
        x: 'برای انجام مراحل احراز هویت ثنا، بایستی هزینه‌ای پرداخت شود. جهت انجام پرداخت، وارد صفحه پرداخت سامانه ثنا شده و کد ۱۶ رقمی نمایش داده شده را کپی کنید.',
        n: '<b>اینجا همان جایی است که بدون کارت بانکی ایرانی متوقف می‌شوید</b> — و دقیقاً همین مرحله را یوآیدی برایتان انجام می‌دهد.' },
      { t: 'وارد صفحه پرداخت یوآیدی شوید', pay: true,
        x: 'بعد وارد صفحه پرداخت یوآیدی شوید؛ همان درگاه پرداخت ارزی که با ویزا کارت، مستر کارت و سایر کارت‌های معتبر بین‌المللی کار می‌کند.' },
      { t: 'فرم ثبت سفارش را تکمیل کنید', pay: true,
        x: 'در این صفحه، کلیه اطلاعات درخواست شده را وارد نمایید تا عملیات پرداخت ریالی شما به صورت کامل در داخل خاک ایران توسط یوآیدی انجام و به شما اطلاع رسانی شود.',
        n: 'کد ۱۶ رقمی را در همین فرم وارد می‌کنید. <b>باقی کار با یوآیدی است.</b>' },
      { t: 'لینک را در ایمیل دریافت کنید', pay: true,
        x: 'بعد از صورت گرفتن پرداخت، لینکی از طریق ایمیل در اختیارتان قرار داده می‌شود.' },
      { t: 'روی لینک بزنید و به ثنا برگردید',
        x: 'روی آن کلیک کنید و بعد از هدایت شدن به صفحه سامانه ثنا، اطلاعات اولیه را وارد نمایید. به این ترتیب به بخش پرداخت هدایت خواهید شد.' },
      { t: '«به‌روزرسانی شرایط پرداخت» را بزنید',
        x: 'در این بخش، کافی است روی گزینه «به‌روزرسانی شرایط پرداخت» کلیک کنید.' },
      { t: 'پیغام تایید پرداخت را ببینید',
        x: 'بعد از زدن روی این دکمه، پیغامی به شما نمایش داده می‌شود که نشان می‌دهد هزینه مورد نیاز جهت احراز هویت، پرداخت شده است.' },
      { t: 'ثبت‌نام را تا انتها تمام کنید',
        x: 'سپس می‌توانید باقی مراحل احراز هویت را انجام داده و روند ثبت نام در سامانه ثنا را به صورت کامل، به اتمام برسانید.' }
    ];

    var scrub = d.getElementById('reelScrub'),
        eye = d.getElementById('reelEye'),
        ttl = d.getElementById('reelTitle'),
        txt = d.getElementById('reelText'),
        note = d.getElementById('reelNote'),
        cntEl = d.getElementById('reelCount'),
        prev = d.getElementById('reelPrev'),
        next = d.getElementById('reelNext'),
        phBtns = Array.prototype.slice.call(d.querySelectorAll('#reelPh .reel-ph'));

    var i = 0, N = STEPS.length;

    scrub.innerHTML = STEPS.map(function (s, idx) {
      return '<button type="button" class="' + (s.pay ? 'pay' : '') + '" tabindex="-1" ' +
        'aria-label="مرحله ' + fa(idx + 1) + '"></button>';
    }).join('');
    var bars = Array.prototype.slice.call(scrub.children);
    bars.forEach(function (b, idx) { b.addEventListener('click', function () { i = idx; paint(); }); });

    function phaseOf(idx) { return idx < 4 ? 0 : idx < 13 ? 1 : idx < 17 ? 2 : 3; }

    function paint() {
      var s = STEPS[i];
      eye.textContent = 'STEP ' + (i + 1 < 10 ? '0' : '') + (i + 1);
      eye.classList.toggle('pay', !!s.pay);
      ttl.textContent = s.t;
      txt.textContent = s.x;
      note.innerHTML = s.n
        ? '<div class="reel-note"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
          'stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/>' +
          '<circle cx="12" cy="12" r="9.5"/></svg><span>' + s.n + '</span></div>'
        : '';
      cntEl.textContent = fa(i + 1) + ' / ' + fa(N);
      prev.disabled = i === 0;
      next.disabled = i === N - 1;
      bars.forEach(function (b, idx) {
        b.classList.toggle('on', idx === i);
        b.classList.toggle('done', idx < i);
      });
      var ph = phaseOf(i);
      phBtns.forEach(function (b, idx) {
        b.classList.toggle('on', idx === ph);
        b.setAttribute('aria-selected', idx === ph ? 'true' : 'false');
      });
    }

    prev.addEventListener('click', function () { if (i > 0) { i--; paint(); } });
    next.addEventListener('click', function () { if (i < N - 1) { i++; paint(); } });
    phBtns.forEach(function (b) {
      b.addEventListener('click', function () { i = parseInt(b.dataset.ph, 10); paint(); });
    });

    var x0 = null, y0 = null, lock = false;
    reel.addEventListener('touchstart', function (e) {
      x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; lock = false;
    }, { passive: true });
    reel.addEventListener('touchmove', function (e) {
      if (x0 === null) return;
      var dx = e.touches[0].clientX - x0, dy = e.touches[0].clientY - y0;
      if (!lock && Math.abs(dx) > Math.abs(dy) + 6) lock = true;
    }, { passive: true });
    reel.addEventListener('touchend', function (e) {
      if (x0 === null) return;
      var dx = e.changedTouches[0].clientX - x0;
      if (lock && Math.abs(dx) > 44) {
        if (dx > 0 && i < N - 1) i++;
        else if (dx < 0 && i > 0) i--;
        paint();
      }
      x0 = null; lock = false;
    }, { passive: true });

    reel.setAttribute('tabindex', '0');
    reel.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') { e.preventDefault(); if (i < N - 1) { i++; paint(); } }
      if (e.key === 'ArrowRight') { e.preventDefault(); if (i > 0) { i--; paint(); } }
    });

    w.__reelGo = function (idx) { i = Math.max(0, Math.min(N - 1, idx)); paint(); };

    paint();
  })();

  /* ═════════════════════════════════════════════════════════════════════
     مسیریاب یک‌سوالی
     ═════════════════════════════════════════════════════════════════════ */
  (function () {
    var opts = d.getElementById('routerOpts');
    if (!opts) return;
    var out = d.getElementById('routerOut'),
        outT = d.getElementById('routerOutT'),
        outP = d.getElementById('routerOutP'),
        acts = d.getElementById('routerActs');

    var MAP = {
      new: {
        t: 'از اینجا شروع کنید',
        p: 'ابتدا مدارک لازم را کنار دستتان بگذارید، بعد راهنمای ۲۱ مرحله را از قدم اول دنبال کنید. ' +
          'اگر جایی گیر کردید، کارشناس یوآیدی همراهتان است.',
        a: [['ghost', 'مدارک لازم را ببینم', '#docs'],
            ['navy', 'شروع راهنمای ۲۱ مرحله', '#steps', 0]]
      },
      stuck: {
        t: 'دقیقاً همان جایی هستید که ما کار می‌کنیم',
        p: 'کد ۱۶ رقمی را در فرم ثبت سفارش وارد کنید تا با کارت بین‌المللی بپردازید و ' +
          'پرداخت ریالی آن، داخل ایران توسط یوآیدی انجام شود.',
        a: [['cta', 'ثبت سفارش پرداخت ارزی', 'modal'],
            ['ghost', 'مرحله ۱۴ را ببینم', '#steps', 13]]
      },
      done: {
        t: 'پیگیری و استعلام برگه ثنا',
        p: 'برای استعلام برگه ثنا به سامانه بین‌المللی برگردید و در قسمت «تغییر اطلاعات»، ' +
          'گزینه «چاپ اطلاعات ثبت نام» را انتخاب کنید.',
        a: [['ghost', 'مراحل پیگیری را ببینم', '#track'],
            ['navy', 'سوال دارم، تماس بگیرم', 'tel']]
      }
    };

    opts.querySelectorAll('.ropt').forEach(function (b) {
      b.addEventListener('click', function () {
        opts.querySelectorAll('.ropt').forEach(function (o) { o.classList.remove('on'); });
        b.classList.add('on');
        var cfg = MAP[b.dataset.r];
        if (!cfg) return;
        outT.textContent = cfg.t;
        outP.textContent = cfg.p;
        acts.innerHTML = '';
        cfg.a.forEach(function (a) {
          var kind = a[0], label = a[1], target = a[2], step = a[3];
          var cls = 'btn btn-sm ' + (kind === 'cta' ? 'btn-cta' : kind === 'navy' ? 'btn-white' : 'btn-ghost-d');
          var el;
          if (target === 'tel') {
            el = d.createElement('a'); el.href = 'tel:02166123290';
            el.innerHTML = '<span class="mono">02166123290</span>';
          } else {
            el = d.createElement('button'); el.type = 'button'; el.textContent = label;
            if (target === 'modal') { el.setAttribute('data-open-modal', ''); }
            el.addEventListener('click', function () {
              if (target === 'modal') {
                var m = d.getElementById('modal');
                if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
                return;
              }
              var t = d.querySelector(target);
              if (!t) return;
              if (w.__openFoldFor) w.__openFoldFor(t);
              if (typeof step === 'number' && w.__reelGo) w.__reelGo(step);
              requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                  w.scrollTo({
                    top: t.getBoundingClientRect().top + w.scrollY - 96,
                    behavior: reduce ? 'auto' : 'smooth'
                  });
                });
              });
            });
          }
          el.className = cls;
          acts.appendChild(el);
        });
        out.classList.add('show');
      });
    });
  })();

  /* ═════════════════════════════════════════════════════════════════════
     محاسبه‌گر هزینه سفر — چهار اسلایدری که بازدیدکننده خودش تعیین می‌کند
     ═════════════════════════════════════════════════════════════════════ */
  (function () {
    var fly = d.getElementById('tc_fly');
    if (!fly) return;
    var days = d.getElementById('tc_days'),
        stay = d.getElementById('tc_stay'),
        wage = d.getElementById('tc_wage'),
        flyV = d.getElementById('tc_fly_v'),
        daysV = d.getElementById('tc_days_v'),
        stayV = d.getElementById('tc_stay_v'),
        wageV = d.getElementById('tc_wage_v'),
        totEl = d.getElementById('tc_total'),
        pdEl = d.getElementById('tc_perday'),
        tmEl = d.getElementById('tc_time'),
        curRow = d.getElementById('curRow');

    var SYM = { EUR: '€', USD: '$', GBP: '£', CAD: 'C$', AUD: 'A$', AED: 'AED' };
    var cur = 'EUR';

    function paint() {
      var f = parseInt(fly.value, 10),
          dN = parseInt(days.value, 10),
          s = parseInt(stay.value, 10),
          wg = parseInt(wage.value, 10);
      var total = f + dN * s + dN * wg;

      flyV.textContent = faNum(f);
      daysV.textContent = fa(dN);
      stayV.textContent = faNum(s);
      wageV.textContent = faNum(wg);

      totEl.textContent = SYM[cur] + ' ' + faNum(total);
      pdEl.textContent = SYM[cur] + ' ' + faNum(total / dN);
      tmEl.textContent = fa(dN) + ' روز';
    }

    [fly, days, stay, wage].forEach(function (el) { el.addEventListener('input', paint); });

    if (curRow) {
      curRow.querySelectorAll('.cur-chip').forEach(function (b) {
        b.addEventListener('click', function () {
          curRow.querySelectorAll('.cur-chip').forEach(function (x) { x.classList.remove('on'); });
          b.classList.add('on');
          cur = b.dataset.cur;
          paint();
        });
      });
    }
    paint();
  })();

  /* ═════════════════════════════════════════════════════════════════════
     کپسول ۳۰ ثانیه‌ای — پرش نرم که فصل بسته را هم باز می‌کند
     ═════════════════════════════════════════════════════════════════════ */
  d.querySelectorAll('.uid-sana-abroad-page [data-jump-soft]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = d.querySelector(b.dataset.jumpSoft);
      if (!t) return;
      if (w.__openFoldFor) w.__openFoldFor(t);
      requestAnimationFrame(function () {
        w.scrollTo({
          top: t.getBoundingClientRect().top + w.scrollY - 84,
          behavior: reduce ? 'auto' : 'smooth'
        });
      });
    });
  });
})();
