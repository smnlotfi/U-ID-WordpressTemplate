/**
 * رفتار تعاملی اختصاصی صفحه «استعلام شبا» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال درخواست تماس، دکمه‌های
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
  function en(s) { /* Persian/Arabic digits → ASCII, so a pasted IBAN still works */
    return String(s).replace(/[۰-۹]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(x); })
      .replace(/[٠-٩]/g, function (x) { return '٠١٢٣٤٥٦٧٨٩'.indexOf(x); });
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
  d.querySelectorAll('.uid-iban-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-iban-page .fld input, .uid-iban-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: کاربران شخصی را به جای صف فروش، به صفحه درست هدایت کن ---- */
  var sel = d.querySelector('.uid-iban-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  d.querySelectorAll('.uid-iban-page .rv, .uid-iban-page .fnl, .uid-iban-page [data-count]').forEach(function (el) {
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
  d.querySelectorAll('.uid-iban-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-iban-page .faq-q').forEach(function (q) {
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
  d.querySelectorAll('.uid-iban-page [data-rail]').forEach(function (rail) {
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
  var codeTabs = d.querySelectorAll('.uid-iban-page [data-code-tab]');
  codeTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      codeTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.codeTab;
      d.querySelectorAll('.uid-iban-page [data-code-pane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.codePane === key);
      });
    });
  });
  var copyBtn = d.querySelector('.uid-iban-page [data-copy]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var active = d.querySelector('.uid-iban-page .code-pane.on');
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
  d.querySelectorAll('.uid-iban-page [data-deck]').forEach(function (dk) { DECKS.push(initDeck(dk)); });

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
  d.querySelectorAll('.uid-iban-page [data-pick]').forEach(function (pick) {
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
     CHAPTER FOLD SYSTEM
     ─────────────────────────────────────────────────────────────────────
     On ≤900px every [data-fold] section collapses behind a 64px chapter row.
     Nine sections stop being thousands of px of scroll and become a
     contents list the visitor reads in one screen. Desktop is untouched.
     ═════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('.uid-iban-page [data-fold]'));

  folds.forEach(function (sec, idx) {
    var body = sec.querySelector('.fold-body');
    if (!body) return;

    var btn = d.createElement('button');
    btn.className = 'fold-btn';
    btn.type = 'button';
    btn.setAttribute('aria-expanded', 'false');
    var bodyId = 'uid-iban-fold-body-' + (idx + 1);
    body.id = bodyId;
    btn.setAttribute('aria-controls', bodyId);
    btn.innerHTML =
      '<span class="fnum">' + fa(idx + 1) + '</span>' +
      '<span class="ftx"><b></b><span></span></span>' +
      (sec.hasAttribute('data-fold-hot') ? '<span class="fdot"></span>' : '') +
      '<svg class="fchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
      'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' +
      '<path d="M6 9l6 6 6-6"/></svg>';
    btn.querySelector('.ftx b').textContent = sec.dataset.foldTitle || '';
    btn.querySelector('.ftx span').textContent = sec.dataset.foldTeaser || '';
    sec.insertBefore(btn, sec.firstChild);

    sec._foldOpen = function (open, instant) {
      if (!mqFold.matches) return;
      var isOpen = sec.classList.contains('open');
      if (open === isOpen) return;

      if (open) {
        sec.classList.add('open');
        btn.setAttribute('aria-expanded', 'true');
        body.style.visibility = 'visible';
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
      sec._foldOpen(!sec.classList.contains('open'));
    });
  });

  /* jumping to a folded chapter must open it first, or the visitor lands on a
     closed row and thinks the link is broken */
  w.__openFoldFor = function (target) {
    if (!target || !mqFold.matches) return false;
    var sec = target.closest ? target.closest('[data-fold]') : null;
    if (!sec) sec = (target.hasAttribute && target.hasAttribute('data-fold')) ? target : null;
    if (sec && sec._foldOpen && !sec.classList.contains('open')) {
      sec._foldOpen(true, true);
      return true;
    }
    return false;
  };

  var allBtn = d.getElementById('foldAll');
  if (allBtn) {
    allBtn.addEventListener('click', function () {
      var anyClosed = folds.some(function (s) { return !s.classList.contains('open'); });
      folds.forEach(function (s) { if (s._foldOpen) s._foldOpen(anyClosed, true); });
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

  /* ---- افست نرم لنگر داخلی (فصل بسته را ابتدا باز می‌کند) ---- */
  d.querySelectorAll('.uid-iban-page a[href^="#"]').forEach(function (a) {
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
     HERO — live IBAN inquiry demo
     Sample records only. The shape of the response mirrors the real
     inquiry/iban/v2 payload exactly, including the owners[] array.
     ═════════════════════════════════════════════════════════════════════ */
  var RECORDS = {
    '820540102680020817909002': {
      bank: 'بانک پارسیان', acc: '102680020817909002',
      fn: 'سارا', ln: 'محمدی', nid: '0079542318',
      status: 'ACCOUNT_STATUS_ACTIVE', type: 'CUSTOMER_TYPE_NATURAL', ok: true
    },
    '120620000000203608707002': {
      bank: 'بانک آینده', acc: '0203608707002',
      fn: 'شرکت داده‌پرداز', ln: 'نوین‌راه', nid: '14006358921',
      status: 'ACCOUNT_STATUS_ACTIVE', type: 'CUSTOMER_TYPE_LEGAL', ok: true
    },
    '190570028180010930000101': {
      bank: 'بانک پاسارگاد', acc: '28180010930000101',
      fn: 'رضا', ln: 'کاظمی', nid: '0451209873',
      status: 'ACCOUNT_STATUS_BLOCKED', type: 'CUSTOMER_TYPE_UNKNOWN', ok: false
    }
  };

  var inp = d.getElementById('ib_code');
  if (inp) {
    var field = d.getElementById('ib_field'), cnt = d.getElementById('ib_cnt'),
        bar = d.getElementById('ib_bar'), run = d.getElementById('ib_run'),
        badge = d.getElementById('ib_badge'), lead = d.getElementById('idwLead'),
        jsonEl = d.getElementById('ib_json'), statusEl = d.getElementById('ib_status'),
        msEl = d.getElementById('ib_ms');
    var out = {
      fn: d.getElementById('ib_fn'), ln: d.getElementById('ib_ln'),
      nid: d.getElementById('ib_nid'), bank: d.getElementById('ib_bank'),
      acc: d.getElementById('ib_acc'), st: d.getElementById('ib_st'),
      ct: d.getElementById('ib_ct')
    };

    /* .idrow ships at opacity:0 in the shared stylesheet and paints on `.in`.
       Without this the record grid renders as an empty box. */
    var rows = Array.prototype.slice.call(d.querySelectorAll('#i_grid .idrow'));
    var revealT = [];
    function revealRows(stagger) {
      revealT.forEach(clearTimeout); revealT = [];
      rows.forEach(function (r, i) {
        if (!stagger || reduce) { r.classList.add('in'); return; }
        r.classList.remove('in');
        revealT.push(setTimeout(function () { r.classList.add('in'); }, 60 + i * 70));
      });
    }
    revealRows(false);

    function digits() { return en(inp.value).replace(/[^0-9]/g, ''); }

    function paintField() {
      var v = digits().slice(0, 24);
      if (inp.value !== v) inp.value = v;
      cnt.textContent = fa(v.length) + ' / ۲۴';
      bar.style.width = (v.length / 24 * 100) + '%';
      field.classList.toggle('full', v.length === 24);
    }
    inp.addEventListener('input', paintField);
    inp.addEventListener('paste', function () { setTimeout(paintField, 0); });
    paintField();

    function setBadge(cls, txt) { badge.className = 'idbadge ' + cls; badge.textContent = txt; }

    function reset() {
      Object.keys(out).forEach(function (k) {
        out[k].textContent = '—';
        out[k].className = (k === 'fn' || k === 'ln' || k === 'bank') ? 'v fa mut' : 'v mut';
      });
      statusEl.textContent = '—'; msEl.textContent = '—';
    }

    function render(rec) {
      out.fn.textContent = rec.fn; out.fn.className = 'v fa';
      out.ln.textContent = rec.ln; out.ln.className = 'v fa';
      out.nid.textContent = rec.nid; out.nid.className = 'v';
      out.bank.textContent = rec.bank; out.bank.className = 'v fa';
      out.acc.textContent = rec.acc; out.acc.className = 'v';
      out.st.textContent = rec.ok ? 'ACCOUNT_STATUS_ACTIVE' : 'ACCOUNT_STATUS_BLOCKED';
      out.st.className = 'v ' + (rec.ok ? 'good' : 'bad');
      out.ct.textContent = rec.type; out.ct.className = 'v';
      statusEl.textContent = rec.ok ? '0 · SUCCESS' : '0 · SUCCESS';
      msEl.textContent = fa(Math.round(180 + Math.random() * 220)) + ' میلی‌ثانیه';

      var esc = function (s) { return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;'); };
      jsonEl.innerHTML =
        '{\n' +
        '  <span class="key">"responseContext"</span>: {\n' +
        '    <span class="key">"status"</span>: { <span class="key">"code"</span>: <span class="n">0</span>, <span class="key">"message"</span>: <span class="s">"SUCCESS."</span>, <span class="key">"details"</span>: [] }\n' +
        '  },\n' +
        '  <span class="key">"accountBasicInformation"</span>: {\n' +
        '    <span class="key">"iban"</span>: <span class="s">"IR' + esc(digits()) + '"</span>,\n' +
        '    <span class="key">"accountNumber"</span>: <span class="s">"' + esc(rec.acc) + '"</span>,\n' +
        '    <span class="key">"bankInformation"</span>: { <span class="key">"bankName"</span>: <span class="s">"' + esc(rec.bank) + '"</span> }\n' +
        '  },\n' +
        '  <span class="key">"accountStatus"</span>: <span class="s">"' + rec.status + '"</span>,\n' +
        '  <span class="key">"owners"</span>: [\n' +
        '    {\n' +
        '      <span class="key">"firstName"</span>: <span class="s">"' + esc(rec.fn) + '"</span>,\n' +
        '      <span class="key">"lastName"</span>: <span class="s">"' + esc(rec.ln) + '"</span>,\n' +
        '      <span class="key">"nationalIdentifier"</span>: <span class="s">"' + esc(rec.nid) + '"</span>,\n' +
        '      <span class="key">"customerType"</span>: <span class="s">"' + rec.type + '"</span>\n' +
        '    }\n' +
        '  ]\n' +
        '}';

      setBadge(rec.ok ? 'ok' : 'blocked', rec.ok ? 'حساب فعال — تأیید شد' : 'حساب مسدود — واریز نکنید');
      revealRows(true);
      if (lead) lead.classList.add('show');
    }

    var busy = false;
    function inquire() {
      if (busy) return;
      var v = digits();
      if (v.length !== 24) {
        setBadge('wait', 'شماره شبا باید ۲۴ رقم باشد');
        field.classList.add('full'); setTimeout(function () { paintField(); }, 10);
        inp.focus();
        return;
      }
      busy = true;
      reset();
      setBadge('load', 'در حال استعلام از شبکه بانکی…');
      setTimeout(function () {
        var rec = RECORDS[v];
        if (!rec) {
          /* an unknown IBAN is the honest answer: the widget does not invent a
             person for a number the demo does not hold */
          setBadge('wait', 'این شماره در داده نمونه وجود ندارد');
          statusEl.textContent = '—';
          msEl.textContent = '—';
          out.st.textContent = '—';
          revealRows(false);
          if (lead) lead.classList.add('show');
          busy = false;
          return;
        }
        render(rec);
        busy = false;
      }, reduce ? 0 : 820);
    }

    if (run) run.addEventListener('click', inquire);
    inp.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); inquire(); } });

    d.querySelectorAll('.uid-iban-page .ibsample').forEach(function (b) {
      b.addEventListener('click', function () {
        inp.value = b.dataset.iban;
        paintField();
        inquire();
      });
    });
  }

  /* result-view switch (ساختاری / JSON) */
  var rBtns = d.querySelectorAll('.uid-iban-page [data-rview]');
  rBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      rBtns.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      d.querySelectorAll('.uid-iban-page [data-rpane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.rpane === btn.dataset.rview);
      });
    });
  });

  /* ═════════════════════════════════════════════════════════════════════
     LOSS ENGINE — every input is the visitor's own number
     ═════════════════════════════════════════════════════════════════════ */
  var lsTx = d.getElementById('ls_tx');
  if (lsTx) {
    var lsRate = d.getElementById('ls_rate'), lsCost = d.getElementById('ls_cost'),
        txV = d.getElementById('ls_tx_v'), rateV = d.getElementById('ls_rate_v'),
        costV = d.getElementById('ls_cost_v'), failEl = d.getElementById('ls_fail'),
        apiEl = d.getElementById('ls_api'), totEl = d.getElementById('ls_total'),
        dayEl = d.getElementById('ls_daily'), lossVol = d.getElementById('lossVolume');

    /* the per-inquiry figure used for the comparison line. Same illustrative
       base rate as the tariff calculator below — change both together. */
    var DEMO_UNIT = 1800;

    function lsPaint() {
      var tx = parseInt(lsTx.value, 10);
      var rate = parseInt(lsRate.value, 10) / 10;      /* slider is in tenths of a % */
      var cost = parseInt(lsCost.value, 10);

      var fail = Math.round(tx * rate / 100);
      var total = fail * cost;
      var apiC = tx * DEMO_UNIT;

      txV.textContent = faNum(tx);
      rateV.innerHTML = fa(rate.toFixed(1).replace('.', '٫')) + '<small>درصد</small>';
      costV.textContent = faNum(cost);
      failEl.textContent = faNum(fail);
      apiEl.textContent = faNum(apiC);
      totEl.textContent = faNum(total);
      dayEl.textContent = faNum(total / 30);
      if (lossVol) lossVol.value = tx;
    }
    [lsTx, lsRate, lsCost].forEach(function (el) { el.addEventListener('input', lsPaint); });
    lsPaint();
  }

  /* ═════════════════════════════════════════════════════════════════════
     VOLUME / TARIFF CALCULATOR
     ─────────────────────────────────────────────────────────────────────
     ⚠ تعرفه‌ها نمایشی است. برای انتشار نهایی فقط همین دو ثابت را با تعرفه
       واقعی یوآیدی جایگزین کنید — بقیه صفحه نیازی به تغییر ندارد.
         BASE_UNIT → قیمت پایه هر استعلام موفق (تومان)
         TIERS     → پله‌های تخفیف بر اساس حجم ماهانه
     ═════════════════════════════════════════════════════════════════════ */
  var BASE_UNIT = 1800;
  var TIERS = [
    { max: 10000, name: 'پلن پایه', off: 0 },
    { max: 50000, name: 'پلن رشد', off: 0.10 },
    { max: 200000, name: 'پلن کسب‌وکار', off: 0.18 },
    { max: 1000000, name: 'پلن سازمانی', off: 0.28 },
    { max: Infinity, name: 'پلن اختصاصی', off: null }
  ];

  var range = d.getElementById('cl_range');
  if (range) {
    var qtyEl = d.getElementById('cl_qty'), planEl = d.getElementById('cl_plan'),
        offEl = d.getElementById('cl_off'), nEl = d.getElementById('cl_n'),
        unitEl = d.getElementById('cl_unit'), grossEl = d.getElementById('cl_gross'),
        totalEl = d.getElementById('cl_total'), presets = d.getElementById('cl_presets'),
        volField = d.getElementById('leadVolume');

    var MIN = 1000, MAX = 2000000;
    function posToQty(p) {
      var q = MIN * Math.pow(MAX / MIN, p / 1000);
      if (q < 10000) return Math.round(q / 500) * 500;
      if (q < 100000) return Math.round(q / 1000) * 1000;
      if (q < 500000) return Math.round(q / 5000) * 5000;
      return Math.round(q / 25000) * 25000;
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
