/**
 * رفتار تعاملی اختصاصی صفحه «وب سرویس ثبت احوال» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال درخواست دمو، دکمه‌های
 * شناور تماس و بازگشت به بالا از هدر/فوتر مشترک سایت (assets/js/main.js) می‌آیند —
 * اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
 */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mqMobile = w.matchMedia('(max-width:720px)');

  function faDigits(s) { return String(s).replace(/[0-9]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'[x]; }); }
  function grp(n) { return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '٬'); }

  /* ---- قوانین مسیریابی فرم لید: کاربران حقیقی را به صفحات دیگر هدایت کن ---- */
  var sel = d.querySelector('[data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () { routeAlert.classList.toggle('show', sel.value === 'ind'); });
  }

  /* ---- اعتبارسنجی و ارسال فرم‌های این صفحه (فرم لید پایین صفحه + میکروفرم هیرو) ---- */
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
  d.querySelectorAll('.uid-civil-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-civil-page .fld input, .uid-civil-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  d.querySelectorAll('.uid-civil-page .rv, .uid-civil-page [data-count]').forEach(function (el) { if (io) io.observe(el); else el.classList.add('is-in'); });

  /* ---- شمارش صعودی اعداد (باند اعتماد) ---- */
  var cio = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) {
      if (!e.isIntersecting) return; cio.unobserve(e.target);
      var el = e.target, to = parseFloat(el.dataset.count), dec = parseInt(el.dataset.dec || '0', 10);
      if (reduce) { el.textContent = faDigits(dec ? to.toFixed(dec) : grp(to)); return; }
      var t0 = null, dur = 1400;
      function step(t) {
        if (!t0) t0 = t;
        var p = Math.min((t - t0) / dur, 1);
        var v = to * (1 - Math.pow(1 - p, 3));
        el.textContent = faDigits(dec ? v.toFixed(dec) : grp(v));
        if (p < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    });
  }, { threshold: .4 }) : null;
  d.querySelectorAll('.uid-civil-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = faDigits(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-civil-page .faq-q').forEach(function (q) {
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

  /* ---- دات‌های کاروسل موبایل (سرویس‌های مورد نیاز) ---- */
  d.querySelectorAll('.uid-civil-page [data-rail]').forEach(function (rail) {
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

  /* ---- افست نرم لنگر داخلی ---- */
  d.querySelectorAll('.uid-civil-page a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault();
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ---- تب‌های نمونه‌کد (درخواست/پاسخ) + کپی ---- */
  var codeTabs = d.querySelectorAll('.uid-civil-page [data-code-tab]');
  codeTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      codeTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.codeTab;
      d.querySelectorAll('.uid-civil-page [data-code-pane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.codePane === key);
      });
    });
  });
  var copyBtn = d.querySelector('.uid-civil-page [data-copy]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var active = d.querySelector('.uid-civil-page .code-pane.on');
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
     visible, two peeking behind it. Cards stop being a long block of scroll
     and become a single block the user flicks through. */
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
      if (count) count.textContent = faDigits(i + 1) + ' / ' + faDigits(n);
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
    if (mqMobile.addEventListener) mqMobile.addEventListener('change', sync);
    else if (mqMobile.addListener) mqMobile.addListener(sync);
    var rt; w.addEventListener('resize', function () {
      clearTimeout(rt); rt = setTimeout(function () { if (mqMobile.matches) measure(); }, 180);
    }, { passive: true });
    if (d.fonts && d.fonts.ready) d.fonts.ready.then(function () { if (mqMobile.matches) measure(); });
    w.addEventListener('load', function () { if (mqMobile.matches) measure(); });
  }
  d.querySelectorAll('.uid-civil-page [data-deck]').forEach(initDeck);

  /* ───────────── STICKY MOBILE JUMP BAR ───────────── */
  var jb = d.getElementById('crJumpbar');
  if (jb) {
    var chips = Array.prototype.slice.call(jb.querySelectorAll('[data-jump]'));
    var targets = chips.map(function (c) { return d.querySelector(c.dataset.jump); });
    var skip = chips.map(function (c) { return c.classList.contains('top'); });

    chips.forEach(function (c) {
      c.addEventListener('click', function () {
        var t = d.querySelector(c.dataset.jump);
        if (!t) return;
        w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 96, behavior: reduce ? 'auto' : 'smooth' });
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
      chips.forEach(function (c, idx) { c.classList.toggle('on', idx === active); });
      if (active > -1) {
        var el = chips[active], rail = d.getElementById('crJumpRail');
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

  /* ───────────── HERO: identity-record reveal (fabricated demo, not real API) ───────────── */
  var nidEl = d.getElementById('i_nid'), bdEl = d.getElementById('i_bd'),
      runBtn = d.getElementById('i_run'), badge = d.getElementById('i_badge'),
      grid = d.getElementById('i_grid'), codeEl = d.getElementById('i_code'),
      msEl = d.getElementById('i_ms'), leadBox = d.getElementById('idwLead');

  if (runBtn && grid) {
    var SAMPLES = [
      { nid: '0079145823', bd: '1372/11/02', first: 'سارا', last: 'محمدی', father: 'رضا',
        gender: 'GENDER_FEMALE', genderFa: 'زن', office: 'اداره ثبت احوال تهران — منطقه ۳' },
      { nid: '1288460397', bd: '1365/04/19', first: 'امیرحسین', last: 'کریمی', father: 'مرتضی',
        gender: 'GENDER_MALE', genderFa: 'مرد', office: 'اداره ثبت احوال اصفهان — مرکزی' },
      { nid: '2593017468', bd: '1380/08/25', first: 'نگار', last: 'حسینی', father: 'بهرام',
        gender: 'GENDER_FEMALE', genderFa: 'زن', office: 'اداره ثبت احوال مشهد — ناحیه ۱' }
    ];
    if (w.UID_CR_SAMPLES_OVERRIDE && w.UID_CR_SAMPLES_OVERRIDE.length) {
      SAMPLES = w.UID_CR_SAMPLES_OVERRIDE.map(function (row, i) {
        var base = SAMPLES[i] || SAMPLES[0];
        var out = {};
        Object.keys(base).forEach(function (k) { out[k] = (row[k] !== undefined && row[k] !== '') ? String(row[k]) : base[k]; });
        return out;
      });
    }

    var ROWS = [
      { k: 'firstName', get: function (s) { return s.first; } },
      { k: 'lastName', get: function (s) { return s.last; } },
      { k: 'fatherName', get: function (s) { return s.father; } },
      { k: 'gender', get: function (s) { return s.genderFa; }, sub: function (s) { return s.gender; } },
      { k: 'nationalId', get: function (s) { return s.nid; }, num: true },
      { k: 'birthDate', get: function (s) { return s.bd; }, num: true },
      { k: 'deathStatus', get: function () { return 'در قید حیات'; }, sub: function () { return 'DEATH_STATUS_ALIVE'; }, alive: true },
      { k: 'officeName', get: function (s) { return s.office; } }
    ];

    var rowEls = Array.prototype.slice.call(grid.children);
    var timers = [];
    function clearTimers() { timers.forEach(clearTimeout); timers = []; }

    function resetCard(msg) {
      clearTimers();
      rowEls.forEach(function (el, idx) {
        el.classList.remove('in', 'alive');
        el.innerHTML = '<span class="k">' + ROWS[idx].k + '</span><span class="v mut">—</span>';
      });
      badge.className = 'idbadge wait'; badge.textContent = msg || 'در انتظار ورودی';
      if (codeEl) codeEl.textContent = '—';
      if (msEl) msEl.textContent = '—';
      if (leadBox) leadBox.classList.remove('show');
    }

    function fill(s) {
      clearTimers();
      badge.className = 'idbadge busy'; badge.textContent = 'در حال استعلام…';
      if (codeEl) codeEl.textContent = '…';
      if (msEl) msEl.textContent = '…';
      rowEls.forEach(function (el, idx) {
        el.classList.remove('in', 'alive');
        el.innerHTML = '<span class="k">' + ROWS[idx].k + '</span><span class="v mut">—</span>';
      });

      var step = reduce ? 0 : 130;
      var start = reduce ? 0 : 420;

      ROWS.forEach(function (r, idx) {
        timers.push(setTimeout(function () {
          var el = rowEls[idx];
          var val = r.get(s);
          var sub = r.sub ? r.sub(s) : null;
          el.innerHTML = '<span class="k">' + r.k + '</span>' +
            '<span class="v' + (r.num ? ' num' : '') + '">' + val +
            (sub ? ' <span class="mono" style="font-size:10.5px;opacity:.6">' + sub + '</span>' : '') + '</span>';
          if (r.alive) el.classList.add('alive');
          el.classList.add('in');
        }, start + idx * step));
      });

      timers.push(setTimeout(function () {
        badge.className = 'idbadge ok'; badge.textContent = 'SUCCESS — پاسخ دریافت شد';
        if (codeEl) codeEl.textContent = 'code: 1';
        if (msEl) msEl.textContent = faDigits(Math.floor(Math.random() * 420 + 380)) + ' میلی‌ثانیه';
        if (leadBox) leadBox.classList.add('show');
      }, start + ROWS.length * step + 120));
    }

    function invalid(msg) {
      clearTimers();
      rowEls.forEach(function (el, idx) {
        el.classList.remove('in', 'alive');
        el.innerHTML = '<span class="k">' + ROWS[idx].k + '</span><span class="v mut">—</span>';
      });
      badge.className = 'idbadge bad'; badge.textContent = 'ورودی نامعتبر';
      if (codeEl) codeEl.textContent = 'code: 3';
      if (msEl) msEl.textContent = '—';
      rowEls[0].innerHTML = '<span class="k">status.message</span><span class="v mut">' + msg + '</span>';
      rowEls[0].classList.add('in');
      if (leadBox) leadBox.classList.remove('show');
    }

    resetCard();

    d.querySelectorAll('[data-sample]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var s = SAMPLES[parseInt(btn.dataset.sample, 10)];
        nidEl.value = s.nid; bdEl.value = s.bd;
        fill(s);
      });
    });

    runBtn.addEventListener('click', function () {
      var nid = (nidEl.value || '').replace(/[^0-9]/g, '');
      var bd = (bdEl.value || '').trim();
      if (nid.length !== 10) { invalid('کد ملی باید ۱۰ رقم باشد'); return; }
      if (!/^1[34]\d{2}\/\d{2}\/\d{2}$/.test(bd)) { invalid('تاریخ تولد با فرمت YYYY/MM/DD وارد شود'); return; }
      var sum = 0; for (var k = 0; k < nid.length; k++) sum += parseInt(nid[k], 10);
      var base = SAMPLES[sum % SAMPLES.length];
      fill({ nid: nid, bd: bd, first: base.first, last: base.last, father: base.father,
             gender: base.gender, genderFa: base.genderFa, office: base.office });
    });

    [nidEl, bdEl].forEach(function (el) {
      if (!el) return;
      el.addEventListener('input', function () { if (badge.classList.contains('bad')) resetCard(); });
    });
  }
})();
