/**
 * رفتار تعاملی اختصاصی صفحه «وب‌سرویس احراز هویت تصویری» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال درخواست دمو، دکمه‌های
 * شناور تماس و بازگشت به بالا از هدر/فوتر مشترک سایت (assets/js/main.js) می‌آیند —
 * اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
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

  /* ---- قوانین مسیریابی فرم لید ---- */
  var sel = d.querySelector('[data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () { routeAlert.classList.toggle('show', sel.value === 'ind'); });
  }

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
  d.querySelectorAll('.uid-ekyc-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-ekyc-page .fld input, .uid-ekyc-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  d.querySelectorAll('.uid-ekyc-page .rv, .uid-ekyc-page .fnl, .uid-ekyc-page [data-count]').forEach(function (el) { if (io) io.observe(el); else el.classList.add('is-in'); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-ekyc-page .faq-q').forEach(function (q) {
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

  /* ---- تب‌های نمونه‌کد + کپی ---- */
  var codeTabs = d.querySelectorAll('.uid-ekyc-page [data-code-tab]');
  codeTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      codeTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.codeTab;
      d.querySelectorAll('.uid-ekyc-page [data-code-pane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.codePane === key);
      });
    });
  });
  var copyBtn = d.querySelector('.uid-ekyc-page [data-copy]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var active = d.querySelector('.uid-ekyc-page .code-pane.on');
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

  /* ---- افست نرم لنگر داخلی ---- */
  d.querySelectorAll('.uid-ekyc-page a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault();
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ═══════════════════ CARD DECK (مشترک با صفحات دیگر، پیاده‌سازی مستقل) ═══════════════════ */
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
  d.querySelectorAll('.uid-ekyc-page [data-deck]').forEach(function (dk) { DECKS.push(initDeck(dk)); });

  /* یک دک، بلندترین کارتش را وقتی در جریان عادی است اندازه می‌گیرد. داخل یک فصل
     تاخورده، آن اندازه‌گیری روی جعبه با ارتفاع صفر انجام می‌شود؛ به همین دلیل سیستم
     تاخوردگی، همان لحظه که فصل باز می‌شود این تابع را دوباره صدا می‌زند. */
  w.__uidEkycRemeasureDecks = function (scope) {
    DECKS.forEach(function (fn) {
      if (!fn) return;
      if (scope && !scope.contains(fn.el)) return;
      fn.refresh();
    });
  };

  /* ═══════════════════ انتخابگر صنعت (Industry Picker) ═══════════════════ */
  d.querySelectorAll('.uid-ekyc-page [data-pick]').forEach(function (pick) {
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
          strip.scrollTo({ left: el.offsetLeft - strip.clientWidth / 2 + el.offsetWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
        }
      }
    }
    chips.forEach(function (c, idx) { c.addEventListener('click', function () { i = idx; paint(); }); });

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

  /* ═══════════════════ نوار پرش سریع موبایل ═══════════════════ */
  var jb = d.getElementById('ekJumpbar');
  if (jb) {
    var jchips = Array.prototype.slice.call(jb.querySelectorAll('[data-jump]'));
    var targets = jchips.map(function (c) { return d.querySelector(c.dataset.jump); });
    var skip = jchips.map(function (c) { return c.classList.contains('top'); });

    jchips.forEach(function (c) {
      c.addEventListener('click', function () {
        var t = d.querySelector(c.dataset.jump);
        if (!t) return;
        var opened = w.__uidEkycOpenFoldFor ? w.__uidEkycOpenFoldFor(t) : false;
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
        var el = jchips[active], rail = d.getElementById('ekJumpRail');
        if (rail && el) {
          var er = el.getBoundingClientRect(), rr = rail.getBoundingClientRect();
          if (er.left < rr.left || er.right > rr.right) {
            rail.scrollTo({ left: el.offsetLeft - rail.clientWidth / 2 + el.offsetWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
          }
        }
      }
      jTick = false;
    }
    w.addEventListener('scroll', function () { if (!jTick) { jTick = true; requestAnimationFrame(onJump); } }, { passive: true });
    onJump();
  }

  /* ═════════════════════════════════════════════════════════════════════
     سیستم فصل‌های تاخوردنی (Chapter Fold System) — فقط زیر ۹۰۰px فعال است.
     هر [data-fold] به یک ردیف ۶۴px «شماره + عنوان + معرفی کوتاه + فلش» تبدیل
     می‌شود؛ با کلیک باز می‌شود (به‌صورت آکاردئون، فقط یکی هم‌زمان باز است)،
     یک نوار چسبان بالای فصل باز نمایش داده می‌شود و ناوبری قبلی/بعدی + دکمه
     CTA به انتهای هر فصل اضافه می‌شود. در دسکتاپ کاملاً غیرفعال و نامرئی است.
     ═════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('.uid-ekyc-page [data-fold]'));
  var meter = d.getElementById('ekFoldMeter');

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
    var bodyId = 'uid-ekyc-fold-body-' + (idx + 1);
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
        '<button class="cn-btn cn-cta" type="button" data-open-modal>' +
        'درخواست فعال‌سازی وب‌سرویس</button>' +
        '<span class="cn-hint">بخش <b>' + fa(idx + 1) + '</b> از <b>' + fa(folds.length) + '</b>' +
        ' — یا با <b>۰۲۱۶۶۱۲۳۲۹۰</b> تماس بگیرید</span>';
      inner.appendChild(nav);

      nav.querySelector('[data-cn="prev"]').addEventListener('click', function () { if (idx > 0) openChapter(idx - 1); });
      nav.querySelector('[data-cn="next"]').addEventListener('click', function () { if (idx < folds.length - 1) openChapter(idx + 1); });
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
      if (w.__uidEkycRemeasureDecks) w.__uidEkycRemeasureDecks(sec);
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

  w.__uidEkycOpenFoldFor = function (target) {
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

  var allBtn = d.getElementById('ekFoldAll');
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

  d.querySelectorAll('.uid-ekyc-page .chap-nav [data-open-modal]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      var m = d.getElementById('modal');
      if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
    });
  });

  /* ═════════════════════════════════════════════════════════════════════
     هیرو — شبیه‌سازی زنده یک جلسه احراز هویت (داده نمونه، ساختار واقعی)
     ═════════════════════════════════════════════════════════════════════ */
  var kyw = d.getElementById('kyw');
  if (kyw) {
    var runBtn = d.getElementById('kyw_run'),
        capEl = d.getElementById('kyw_cap'),
        secEl = d.getElementById('kyw_sec'),
        arcEl = d.getElementById('kyw_arc'),
        pctEl = d.getElementById('kyw_pct'),
        discEl = d.getElementById('kyw_disc'),
        badge = d.getElementById('kyw_badge'),
        steps = Array.prototype.slice.call(d.querySelectorAll('.kyw-step')),
        lead = d.getElementById('kywLead'),
        jsonEl = d.getElementById('k_json'),
        out = {
          state: d.getElementById('k_state'), score: d.getElementById('k_score'),
          reason: d.getElementById('k_reason'), live: d.getElementById('k_live'),
          nid: d.getElementById('k_nid'), sid: d.getElementById('k_sid'),
          ms: d.getElementById('k_ms')
        };

    var SCEN = {
      ok: {
        nid: '0079542318', birth: '1372/04/19', sex: 'SEX_MALE',
        live: true, score: 98.17, failAt: -1,
        state: 'AUTHENTICATION_STATE_ACCEPTED',
        reason: 'AUTHENTICATION_RESPONSE_REASON_ACCEPT',
        code: '0', message: 'SUCCESS.',
        capLive: 'بافت پوست، بازتاب نور و عمق تصویر طبیعی است.',
        capEnd: 'هویت تایید شد — کاربر همان صاحب مدرک است.'
      },
      print: {
        nid: '0079542318', birth: '1372/04/19', sex: 'SEX_MALE',
        live: false, score: 0, failAt: 2,
        state: 'AUTHENTICATION_STATE_REJECTED',
        reason: 'AUTHENTICATION_RESPONSE_REASON_REJECT_VIDEO_NOT_LIVE',
        code: '0', message: 'SUCCESS.',
        capLive: 'بافت کاغذ و نبود عمق تصویر تشخیص داده شد.',
        capEnd: 'حمله با عکس چاپ‌شده مسدود شد.'
      },
      screen: {
        nid: '0079542318', birth: '1372/04/19', sex: 'SEX_MALE',
        live: false, score: 0, failAt: 2,
        state: 'AUTHENTICATION_STATE_REJECTED',
        reason: 'AUTHENTICATION_RESPONSE_REASON_REJECT_VIDEO_NOT_LIVE',
        code: '0', message: 'SUCCESS.',
        capLive: 'الگوی نوری و نوسان صفحه نمایش تشخیص داده شد.',
        capEnd: 'پخش تصویر روی مانیتور مسدود شد.'
      },
      nomatch: {
        nid: '0064912877', birth: '1368/11/02', sex: 'SEX_FEMALE',
        live: true, score: 41.6, failAt: 3,
        state: 'AUTHENTICATION_STATE_REJECTED',
        reason: 'AUTHENTICATION_RESPONSE_REASON_REJECT_FACE_NOT_MATCH_ID',
        code: '0', message: 'SUCCESS.',
        capLive: 'فرد زنده است، اما هنوز هویتش تایید نشده.',
        capEnd: 'چهره با تصویر متناظر این کد ملی مطابقت ندارد.'
      }
    };
    (function applyScenarioOverrides(){
      var ov = w.UID_EK_SCENARIOS_OVERRIDE;
      if(!ov) return;
      Object.keys(ov).forEach(function(k){
        var svc = SCEN[k], o = ov[k];
        if(!svc || !o) return;
        if(o.nid) svc.nid = String(o.nid);
        if(o.birth) svc.birth = String(o.birth);
        if(o.capLive) svc.capLive = String(o.capLive);
        if(o.capEnd) svc.capEnd = String(o.capEnd);
      });
    })();

    var current = 'ok', running = false, timers = [];
    function clearTimers() { timers.forEach(clearTimeout); timers = []; }
    function later(fn, ms) { timers.push(setTimeout(fn, ms)); }

    d.querySelectorAll('.kyw-chip').forEach(function (chip) {
      chip.addEventListener('click', function () {
        if (running) return;
        d.querySelectorAll('.kyw-chip').forEach(function (c) { c.classList.remove('on'); c.setAttribute('aria-selected', 'false'); });
        chip.classList.add('on'); chip.setAttribute('aria-selected', 'true');
        current = chip.dataset.sc;
        reset();
      });
    });

    function setBadge(cls, txt) { badge.className = 'kyw-badge ' + cls; badge.textContent = txt; }

    function reset() {
      clearTimers(); running = false;
      kyw.dataset.phase = 'idle'; kyw.dataset.result = '';
      steps.forEach(function (s) { s.classList.remove('on', 'done', 'fail'); });
      ['state', 'score', 'reason', 'live', 'nid', 'sid'].forEach(function (k) { out[k].textContent = '—'; out[k].className = 'v mut'; });
      out.ms.textContent = '—';
      capEl.textContent = 'برای شروع، سناریو را انتخاب و دکمه زیر را بزنید.';
      setBadge('wait', 'در انتظار اجرا');
      arcEl.style.strokeDashoffset = '126';
      secEl.textContent = '۵';
      jsonEl.innerHTML = '{\n  <span class="key">"sessionId"</span>: <span class="s">"…"</span>,\n' +
        '  <span class="key">"status"</span>: { <span class="key">"code"</span>: <span class="s">"…"</span>, <span class="key">"message"</span>: <span class="s">"…"</span> },\n' +
        '  <span class="key">"userInformation"</span>: { <span class="key">"nationalId"</span>: <span class="s">"…"</span>, <span class="key">"date"</span>: <span class="s">"…"</span>, <span class="key">"sex"</span>: <span class="s">"…"</span> },\n' +
        '  <span class="key">"authenticationStatus"</span>: { <span class="key">"state"</span>: <span class="s">"…"</span>, <span class="key">"reason"</span>: <span class="s">"…"</span> }\n}';
      runBtn.disabled = false;
    }

    function mark(i, cls) {
      steps.forEach(function (s, j) { if (j < i) { s.classList.add('done'); s.classList.remove('on'); } });
      if (i > -1 && steps[i]) steps[i].classList.add(cls || 'on');
    }

    function sid() {
      var c = '0123456789abcdef', s = '';
      for (var i = 0; i < 8; i++) s += c[Math.floor(Math.random() * 16)];
      return s + '-' + Date.now().toString(16).slice(-6);
    }

    function paintJson(sc, session) {
      jsonEl.innerHTML =
        '{\n' +
        '  <span class="key">"sessionId"</span>: <span class="s">"' + session + '"</span>,\n' +
        '  <span class="key">"status"</span>: {\n' +
        '    <span class="key">"code"</span>: <span class="n">' + sc.code + '</span>,\n' +
        '    <span class="key">"message"</span>: <span class="s">"' + sc.message + '"</span>\n' +
        '  },\n' +
        '  <span class="key">"userInformation"</span>: {\n' +
        '    <span class="key">"nationalId"</span>: <span class="s">"' + sc.nid + '"</span>,\n' +
        '    <span class="key">"date"</span>: <span class="s">"' + sc.birth + '"</span>,\n' +
        '    <span class="key">"sex"</span>: <span class="s">"' + sc.sex + '"</span>\n' +
        '  },\n' +
        '  <span class="key">"authenticationStatus"</span>: {\n' +
        '    <span class="key">"state"</span>: <span class="s">"' + sc.state + '"</span>,\n' +
        '    <span class="key">"reason"</span>: <span class="s">"' + sc.reason + '"</span>\n' +
        '  }\n' +
        '}';
    }

    function finish(sc) {
      var session = sid();
      var ms = 2.9 + Math.random() * 1.7;
      var okRun = sc.failAt === -1;

      kyw.dataset.phase = 'done';
      kyw.dataset.result = okRun ? 'ok' : 'bad';
      discEl.innerHTML = okRun
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';
      pctEl.textContent = sc.score > 0 ? faDec(sc.score, 2) + '٪' : 'رد شد';
      capEl.textContent = sc.capEnd;

      steps.forEach(function (s, j) {
        s.classList.remove('on');
        if (sc.failAt === -1 || j < sc.failAt) s.classList.add('done');
        else if (j === sc.failAt) s.classList.add('fail');
      });

      setBadge(okRun ? 'ok' : 'bad', okRun ? 'AUTHENTICATION_STATE_ACCEPTED' : 'AUTHENTICATION_STATE_REJECTED');

      out.state.textContent = okRun ? 'تایید شده' : 'رد شده';
      out.state.className = 'v fa ' + (okRun ? 'good' : 'bad');
      out.score.textContent = sc.score > 0 ? faDec(sc.score, 2) + '٪' : '—';
      out.score.className = 'v ' + (okRun ? 'good' : 'mut');
      out.reason.textContent = sc.reason;
      out.reason.className = 'v';
      out.live.textContent = sc.live ? 'زنده — تایید شد' : 'زنده نیست — رد شد';
      out.live.className = 'v fa ' + (sc.live ? 'good' : 'bad');
      out.nid.textContent = sc.nid;
      out.nid.className = 'v';
      out.sid.textContent = session;
      out.sid.className = 'v';
      out.ms.textContent = faDec(ms, 1) + ' ثانیه';

      paintJson(sc, session);
      running = false; runBtn.disabled = false;
      if (lead) lead.classList.add('show');
    }

    function run() {
      if (running) return;
      running = true; runBtn.disabled = true;
      clearTimers();
      kyw.dataset.result = '';
      steps.forEach(function (s) { s.classList.remove('on', 'done', 'fail'); });
      if (lead) lead.classList.remove('show');
      var sc = SCEN[current];

      kyw.dataset.phase = 'send';
      setBadge('load', 'ارسال اطلاعات پایه به سرویس…');
      capEl.textContent = 'کد ملی، سریال کارت، جنسیت و تاریخ تولد ارسال شد.';
      mark(0);

      if (reduce) { finish(sc); return; }

      later(function () {
        kyw.dataset.phase = 'record';
        mark(1);
        setBadge('load', 'در حال ضبط ویدیوی سلفی…');
        capEl.textContent = 'فقط ۵ ثانیه به دوربین نگاه کنید — هیچ دستور دیگری در کار نیست.';
        var left = 5;
        secEl.textContent = fa(left);
        arcEl.style.strokeDashoffset = '126';
        var tick = function () {
          left--;
          secEl.textContent = fa(Math.max(left, 0));
          arcEl.style.strokeDashoffset = String(126 - (126 * ((5 - left) / 5)));
          if (left > 0) later(tick, 1000);
        };
        later(tick, 1000);

        later(function () {
          kyw.dataset.phase = 'analyze';
          mark(2);
          setBadge('load', 'تحلیل زنده‌بودن و ضدجعل…');
          capEl.textContent = sc.capLive;

          later(function () {
            if (sc.failAt === 2) { finish(sc); return; }
            kyw.dataset.phase = 'match';
            mark(3);
            setBadge('load', 'تطبیق بیومتریک با پایگاه ثبت‌احوال…');
            capEl.textContent = 'استخراج فریم باکیفیت و مقایسه با تصویر رسمی کد ملی…';
            var target = sc.score, t0 = null;
            (function count(t) {
              if (!t0) t0 = t;
              var p = Math.min((t - t0) / 1300, 1);
              pctEl.textContent = faDec(target * (1 - Math.pow(1 - p, 3)), 2) + '٪';
              if (p < 1) requestAnimationFrame(count);
            })(performance.now());

            later(function () {
              mark(4);
              setBadge('load', 'فراخوانی کال‌بک پذیرنده…');
              later(function () { finish(sc); }, 460);
            }, 1400);
          }, 1150);
        }, 5100);
      }, 620);
    }

    runBtn.addEventListener('click', run);
    reset();
  }

  var rBtns = d.querySelectorAll('.uid-ekyc-page [data-rview]');
  rBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      rBtns.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      d.querySelectorAll('.uid-ekyc-page [data-rpane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.rpane === btn.dataset.rview);
      });
    });
  });

  /* ═════════════════════════════════════════════════════════════════════
     محاسبه‌گر ریزش (Drop-off Engine) — تمام ورودی‌ها عدد خود کاربر است
     ═════════════════════════════════════════════════════════════════════ */
  var lsTx = d.getElementById('ls_tx');
  if (lsTx) {
    var lsRate = d.getElementById('ls_rate'), lsCost = d.getElementById('ls_cost'),
        txV = d.getElementById('ls_tx_v'), rateV = d.getElementById('ls_rate_v'),
        costV = d.getElementById('ls_cost_v'), failEl = d.getElementById('ls_fail'),
        saveEl = d.getElementById('ls_save'), totEl = d.getElementById('ls_total'),
        dayEl = d.getElementById('ls_daily'), lossVol = d.getElementById('lossVolume');

    var RECOVERY = 0.75;

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
     محاسبه‌گر تعرفه — ⚠ فقط دو ثابت زیر تعرفه نمایشی است
     ═════════════════════════════════════════════════════════════════════ */
  var BASE_UNIT = 12000;
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

    var MIN = 500, MAX = 1000000;
    function posToQty(p) {
      var q = MIN * Math.pow(MAX / MIN, p / 1000);
      if (q < 5000) return Math.round(q / 250) * 250;
      if (q < 50000) return Math.round(q / 500) * 500;
      if (q < 200000) return Math.round(q / 2500) * 2500;
      return Math.round(q / 10000) * 10000;
    }
    function qtyToPos(q) { return Math.round(1000 * Math.log(Math.max(MIN, Math.min(MAX, q)) / MIN) / Math.log(MAX / MIN)); }
    function tierFor(q) {
      for (var t = 0; t < TIERS.length; t++) { if (q <= TIERS[t].max) return TIERS[t]; }
      return TIERS[TIERS.length - 1];
    }

    function paintCalc() {
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

    range.addEventListener('input', paintCalc);
    if (presets) {
      presets.querySelectorAll('.calc-preset').forEach(function (b) {
        b.addEventListener('click', function () { range.value = qtyToPos(parseInt(b.dataset.qty, 10)); paintCalc(); });
      });
    }
    paintCalc();
  }
})();
