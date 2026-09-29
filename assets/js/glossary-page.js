/**
 * رفتار تعاملی اختصاصی صفحه «واژه‌نامه اصطلاحات احراز هویت» — فقط روی این قالب
 * صفحه بارگذاری می‌شود. نکته: منوی هدر/مگامنو، شیت موبایل، مودال درخواست تماس،
 * دکمه‌های شناور تماس، بازگشت به بالا و رفتار عمومی فرم‌ها/[data-submit] از
 * assets/js/main.js می‌آیند — اینجا عمداً تکرار نشده‌اند.
 *
 * سیستم «فصل تاخوردنی + خواننده فصل» موبایل دقیقاً مثل assets/js/card-to-iban-page.js
 * است، با یک تفاوت: فصل اول (پایه هویت و امنیت) به‌طور پیش‌فرض باز است تا کاربر
 * با یک فهرست واقعی از نام اصطلاح‌ها روبه‌رو شود، نه چهار ردیف بسته.
 */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mqFold = w.matchMedia('(max-width:900px)');

  function fa(s) { return String(s).replace(/[0-9]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'[x]; }); }

  /* ---- اعتبارسنجی و ارسال فرم‌های این صفحه (leadForm در پایین صفحه + modalForm) ---- */
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
  d.querySelectorAll('.uid-glossary-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-glossary-page .fld input, .uid-glossary-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: کاربران شخصی را به جای صف فروش، به صفحه درست هدایت کن ---- */
  var sel = d.querySelector('.uid-glossary-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  d.querySelectorAll('.uid-glossary-page .rv, .uid-glossary-page .fnl').forEach(function (el) {
    if (io) io.observe(el); else el.classList.add('is-in');
  });

  /* ---- تیکر پیش‌نمایش هیرو ---- */
  var prevRows = Array.prototype.slice.call(d.querySelectorAll('.uid-glossary-page .gxprev-row'));
  if (prevRows.length && !reduce) {
    var pi = 0;
    setInterval(function () {
      prevRows.forEach(function (r, i) { r.classList.toggle('on', i === pi); });
      pi = (pi + 1) % prevRows.length;
    }, 2100);
  }

  /* ═════════════════════════════════════════════════════════════════════
     سیستم فصل تاخوردنی + خواننده فصل (چهار دسته موضوعی)
     ═════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('.uid-glossary-page [data-fold]'));
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
    var bodyId = 'gx-fold-body-' + (idx + 1);
    body.id = bodyId;
    btn.setAttribute('aria-controls', bodyId);
    btn.innerHTML =
      '<span class="fnum">' + fa(idx + 1) + '</span>' +
      '<span class="ftx"><b></b><span></span></span>' +
      (sec.dataset.foldMin ? '<span class="fmin">' + sec.dataset.foldMin + '</span>' : '') +
      '<svg class="fchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
      'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' +
      '<path d="M6 9l6 6 6-6"/></svg>';
    btn.querySelector('.ftx b').textContent = title;
    btn.querySelector('.ftx span').textContent = sec.dataset.foldTeaser || '';
    sec._teaser = btn.querySelector('.ftx span');
    sec._teaserBase = sec.dataset.foldTeaser || '';
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
        '<button class="cn-btn cn-cta" type="button" data-open-modal>مشاوره رایگان و دریافت کلید آزمایشی</button>';
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

  /* فهرست نباید به شکل چهار ردیف بسته باز شود — فصل اول همان نمونه است */
  function primeFirst() {
    if (!mqFold.matches) return;
    if (folds.length && folds[0]._foldOpen && !folds[0].classList.contains('open')) {
      folds[0]._foldOpen(true, true);
      paintMeter();
    }
  }
  primeFirst();
  if (mqFold.addEventListener) mqFold.addEventListener('change', primeFirst);
  else if (mqFold.addListener) mqFold.addListener(primeFirst);

  /* دکمه‌های CTA داخل فوتر فصل بعد از باند شدن [data-open-modal] مشترک
     (assets/js/main.js) اضافه می‌شوند، پس باید همین‌جا هم بایند شوند. */
  d.querySelectorAll('.uid-glossary-page .chap-nav [data-open-modal]').forEach(function (b) {
    b.addEventListener('click', function (e) {
      e.preventDefault();
      var m = d.getElementById('modal');
      if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
    });
  });

  /* ═════════════════════════════════════════════════════════════════════
     کاوشگر اصطلاحات: جستجوی زنده + دسته + مخاطب + نمای موضوعی/الفبایی
     ═════════════════════════════════════════════════════════════════════ */
  var TERMS = Array.prototype.slice.call(d.querySelectorAll('.uid-glossary-page .term'));
  var SECS = Array.prototype.slice.call(d.querySelectorAll('.uid-glossary-page .catsec'));
  var input = d.getElementById('gxSearch');
  var field = d.getElementById('gxField');
  var clear = d.getElementById('gxClear');
  var countEl = d.getElementById('gxCount');
  var empty = d.getElementById('gxEmpty');
  var azview = d.getElementById('azview');

  function norm(s) {
    return String(s)
      .replace(/[يى]/g, 'ی')
      .replace(/ك/g, 'ک')
      .replace(/[‌‏‎]/g, '')
      .replace(/[٠-٩]/g, function (c) { return String.fromCharCode(c.charCodeAt(0) - 1584); })
      .replace(/[۰-۹]/g, function (c) { return String.fromCharCode(c.charCodeAt(0) - 1728); })
      .replace(/[ً-ْـ]/g, '')
      .toLowerCase().trim();
  }

  TERMS.forEach(function (t) {
    var hd = t.querySelector('.term-hd');
    var bd = t.querySelector('.term-bd');
    if (!hd || !bd) return;
    t._open = function (open, instant) {
      var isOpen = t.classList.contains('open');
      if (open === isOpen) return;
      if (open) {
        t.classList.add('open');
        hd.setAttribute('aria-expanded', 'true');
        bd.style.visibility = 'visible';
        if (instant || reduce) { bd.style.height = 'auto'; return; }
        bd.style.height = bd.scrollHeight + 'px';
        var done = function () {
          bd.removeEventListener('transitionend', done);
          if (t.classList.contains('open')) bd.style.height = 'auto';
        };
        bd.addEventListener('transitionend', done);
      } else {
        t.classList.remove('open');
        hd.setAttribute('aria-expanded', 'false');
        if (instant || reduce) { bd.style.height = '0px'; return; }
        bd.style.height = bd.scrollHeight + 'px';
        void bd.offsetHeight;
        bd.style.height = '0px';
      }
    };
    hd.addEventListener('click', function () { t._open(!t.classList.contains('open')); });
  });

  function reveal(key) {
    var t = TERMS.filter(function (x) { return x.dataset.key === key; })[0];
    if (!t) return;
    if (t.classList.contains('hide')) resetFilters();
    if (w.__openFoldFor) w.__openFoldFor(t);
    t._open(true, true);
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 110, behavior: reduce ? 'auto' : 'smooth' });
        t.classList.remove('flash'); void t.offsetWidth; t.classList.add('flash');
      });
    });
  }
  d.querySelectorAll('.uid-glossary-page [data-rel]').forEach(function (b) {
    b.addEventListener('click', function () { reveal(b.dataset.rel); });
  });

  TERMS.forEach(function (t) {
    t._nmFa = t.querySelector('.term-nm b');
    t._nmEn = t.querySelector('.term-nm .en');
    if (t._nmFa) t._faTx = t._nmFa.textContent;
    if (t._nmEn) t._enTx = t._nmEn.textContent;
  });
  function esc(s) { return s.replace(/[&<>]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;' }[c]; }); }
  function mark(el, raw, q) {
    if (!el) return;
    if (!q) { el.textContent = raw; return; }
    var hay = norm(raw), i = hay.indexOf(q);
    if (i < 0) { el.textContent = raw; return; }
    var out = '', n = '', start = -1, end = -1;
    for (var k = 0; k < raw.length; k++) {
      var before = n.length;
      n += norm(raw[k]);
      if (start < 0 && before <= i && n.length > i) start = k;
      if (start >= 0 && end < 0 && n.length >= i + q.length) { end = k + 1; break; }
    }
    if (start < 0) { el.textContent = raw; return; }
    if (end < 0) end = raw.length;
    out = esc(raw.slice(0, start)) + '<mark>' + esc(raw.slice(start, end)) + '</mark>' + esc(raw.slice(end));
    el.innerHTML = out;
  }

  var activeCat = 'all';
  var query = '';

  function apply() {
    var q = norm(query);
    var total = 0;
    var perSec = {};

    TERMS.forEach(function (t) {
      var okCat = (activeCat === 'all') || (t.dataset.cat === activeCat);
      var okQ = !q || norm(t.dataset.search).indexOf(q) > -1;
      var show = okCat && okQ;
      t.classList.toggle('hide', !show);
      mark(t._nmFa, t._faTx, q);
      mark(t._nmEn, t._enTx, q);
      if (show) {
        total++;
        perSec[t.dataset.cat] = (perSec[t.dataset.cat] || 0) + 1;
      }
    });

    SECS.forEach(function (s) {
      var n = perSec[s.dataset.cat] || 0;
      s.classList.toggle('no-hit', n === 0);
      if (s._teaser) {
        s._teaser.textContent = (q || activeCat !== 'all')
          ? (n ? fa(n) + ' نتیجه در این دسته' : 'بدون نتیجه')
          : s._teaserBase;
      }
      if (mqFold.matches && s._foldOpen) {
        if (q && n > 0) s._foldOpen(true, true);
        else if (q && n === 0) s._foldOpen(false, true);
      }
    });

    if (!query && activeCat === 'all' && mqFold.matches) {
      folds.forEach(function (s, i) { if (s._foldOpen) s._foldOpen(i === 0, true); });
    }

    paintSplit();

    if (countEl) {
      countEl.innerHTML = (query || activeCat !== 'all')
        ? '<b>' + fa(total) + '</b> اصطلاح پیدا شد'
        : '<b>' + fa(TERMS.length) + '</b> اصطلاح در ' + fa(SECS.length) + ' دسته';
    }
    if (empty) {
      empty.classList.toggle('show', total === 0);
      var qs = empty.querySelector('.q');
      if (qs) qs.textContent = query;
    }
    if (d.body.classList.contains('az-on')) paintAZ();
  }

  function resetFilters() {
    query = ''; activeCat = 'all';
    if (input) input.value = '';
    if (field) field.classList.remove('has');
    d.querySelectorAll('.uid-glossary-page .gx-cat').forEach(function (c) {
      c.classList.toggle('on', c.dataset.cat === 'all');
      c.setAttribute('aria-pressed', c.dataset.cat === 'all' ? 'true' : 'false');
    });
    apply();
  }

  if (input) {
    input.addEventListener('input', function () {
      query = input.value;
      if (field) field.classList.toggle('has', !!query);
      apply();
    });
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { input.value = ''; query = ''; if (field) field.classList.remove('has'); apply(); }
    });
  }
  if (clear) clear.addEventListener('click', function () {
    input.value = ''; query = ''; field.classList.remove('has'); apply(); input.focus();
  });

  d.querySelectorAll('.uid-glossary-page .gx-cat').forEach(function (c) {
    c.addEventListener('click', function () {
      activeCat = c.dataset.cat;
      d.querySelectorAll('.uid-glossary-page .gx-cat').forEach(function (x) {
        var on = x === c;
        x.classList.toggle('on', on);
        x.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
      apply();
    });
  });

  function audMode() {
    return d.body.classList.contains('aud-dev') ? 'dev'
      : d.body.classList.contains('aud-biz') ? 'biz' : null;
  }
  function paintSplit() {
    var m = audMode();
    d.querySelectorAll('.uid-glossary-page .term-list').forEach(function (list) {
      if (!m) { list.classList.remove('aud-split'); return; }
      var above = 0, below = 0;
      list.querySelectorAll('.term').forEach(function (t) {
        if (t.classList.contains('hide')) return;
        if (t.dataset.aud === m) above++; else below++;
      });
      list.classList.toggle('aud-split', above > 0 && below > 0);
    });
  }
  d.querySelectorAll('.uid-glossary-page [data-aud-set]').forEach(function (b) {
    b.addEventListener('click', function () {
      var v = b.dataset.audSet;
      d.body.classList.remove('aud-dev', 'aud-biz');
      if (v !== 'all') d.body.classList.add('aud-' + v);
      d.querySelectorAll('.uid-glossary-page [data-aud-set]').forEach(function (x) {
        var on = x === b;
        x.classList.toggle('on', on);
        x.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
      paintSplit();
    });
  });

  var azBuilt = false;
  function paintAZ() {
    if (!azview) return;
    azview.querySelectorAll('.az-group').forEach(function (g) {
      var vis = Array.prototype.slice.call(g.querySelectorAll('.term'))
        .filter(function (t) { return !t.classList.contains('hide'); }).length;
      g.style.display = vis ? '' : 'none';
      var c = g.querySelector('.az-letter span');
      if (c) c.textContent = fa(vis);
    });
  }
  function buildAZ() {
    if (azBuilt || !azview) return;
    var isFa = function (ch) { return /[؀-ۿ]/.test(ch); };
    var list = TERMS.slice().sort(function (a, b) {
      var ka = a.dataset.sortk, kb = b.dataset.sortk;
      var fa_ = isFa(ka[0]), fb = isFa(kb[0]);
      if (fa_ !== fb) return fa_ ? -1 : 1;
      return ka.localeCompare(kb, fa_ ? 'fa' : 'en');
    });
    var cur = null, group = null;
    list.forEach(function (t) {
      var L = t.dataset.sortk[0];
      if (L !== cur) {
        cur = L;
        group = d.createElement('div');
        group.className = 'az-group';
        group.innerHTML = '<div class="az-letter"><b' + (isFa(L) ? '' : ' class="lat"') + '>' + L +
          '</b><i></i><span></span></div><div class="term-list"></div>';
        azview.appendChild(group);
      }
      group.querySelector('.term-list').appendChild(t);
    });
    azBuilt = true;
  }
  function restoreCats() {
    TERMS.forEach(function (t) {
      var host = d.getElementById('list-' + t.dataset.cat);
      if (host) host.appendChild(t);
    });
    if (azview) azview.innerHTML = '';
    azBuilt = false;
  }
  d.querySelectorAll('.uid-glossary-page [data-view-set]').forEach(function (b) {
    b.addEventListener('click', function () {
      var v = b.dataset.viewSet;
      d.querySelectorAll('.uid-glossary-page [data-view-set]').forEach(function (x) {
        var on = x === b;
        x.classList.toggle('on', on);
        x.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
      if (v === 'az') { buildAZ(); d.body.classList.add('az-on'); paintAZ(); }
      else { d.body.classList.remove('az-on'); restoreCats(); }
    });
  });

  d.querySelectorAll('.uid-glossary-page [data-jump-soft]').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = d.querySelector(b.dataset.jumpSoft);
      if (!t) return;
      if (w.__openFoldFor) w.__openFoldFor(t);
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 76, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ---- نوار پرش سریع موبایل ---- */
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

  apply();
})();
