/**
 * رفتار تعاملی اختصاصی صفحه «استعلام کد پستی و آدرس» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال درخواست تماس، دکمه‌های
 * شناور تماس و بازگشت به بالا از هدر/فوتر مشترک سایت (assets/js/main.js) می‌آیند —
 * اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
 */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mqMobile = w.matchMedia('(max-width:720px)');

  function fa(s) { return String(s).replace(/[0-9]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'[x]; }); }
  function grp(n) { return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '٬'); }
  function faNum(n) { return fa(grp(n)); }

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
  d.querySelectorAll('.uid-postal-address-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-postal-address-page .fld input, .uid-postal-address-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: کاربران شخصی را به جای صف فروش، به صفحه درست هدایت کن ---- */
  var sel = d.querySelector('.uid-postal-address-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  var revealEls = d.querySelectorAll('.uid-postal-address-page .rv, .uid-postal-address-page [data-count]');
  revealEls.forEach(function (el) {
    if (!io) { el.classList.add('is-in'); return; }
    var rect = el.getBoundingClientRect();
    if (rect.top < w.innerHeight && rect.bottom > 0) { el.classList.add('is-in'); } else { io.observe(el); }
  });
  if (io) { setTimeout(function () { io.disconnect(); revealEls.forEach(function (el) { el.classList.add('is-in'); }); }, 1200); }

  /* ---- شمارش صعودی اعداد (باند اعتماد) ---- */
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
  d.querySelectorAll('.uid-postal-address-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-postal-address-page .faq-q').forEach(function (q) {
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
  d.querySelectorAll('.uid-postal-address-page [data-rail]').forEach(function (rail) {
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
  d.querySelectorAll('.uid-postal-address-page a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault();
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ---- تب‌های نمونه‌کد + کپی ---- */
  var codeTabs = d.querySelectorAll('.uid-postal-address-page [data-code-tab]');
  codeTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      codeTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.codeTab;
      d.querySelectorAll('.uid-postal-address-page [data-code-pane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.codePane === key);
      });
    });
  });
  var copyBtn = d.querySelector('.uid-postal-address-page [data-copy]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var active = d.querySelector('.uid-postal-address-page .code-pane.on');
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
    if (mqMobile.addEventListener) mqMobile.addEventListener('change', sync);
    else if (mqMobile.addListener) mqMobile.addListener(sync);
    var rt; w.addEventListener('resize', function () {
      clearTimeout(rt); rt = setTimeout(function () { if (mqMobile.matches) measure(); }, 180);
    }, { passive: true });
    if (d.fonts && d.fonts.ready) d.fonts.ready.then(function () { if (mqMobile.matches) measure(); });
    w.addEventListener('load', function () { if (mqMobile.matches) measure(); });
  }
  d.querySelectorAll('.uid-postal-address-page [data-deck]').forEach(initDeck);

  /* ───────────── INDUSTRY PICKER ─────────────
     Rather than making a phone user flip through six industry cards, it asks
     one question — which industry are you? — and renders only that panel. */
  d.querySelectorAll('.uid-postal-address-page [data-pick]').forEach(function (pick) {
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

  /* ───────────── STICKY MOBILE JUMP BAR ─────────────
     Lets a phone user reach any section without scrolling through the page. */
  var jb = d.getElementById('jumpbar');
  if (jb) {
    var jchips = Array.prototype.slice.call(jb.querySelectorAll('[data-jump]'));
    var targets = jchips.map(function (c) { return d.querySelector(c.dataset.jump); });
    var skip = jchips.map(function (c) { return c.classList.contains('top'); });

    jchips.forEach(function (c) {
      c.addEventListener('click', function () {
        var t = d.querySelector(c.dataset.jump);
        if (!t) return;
        w.scrollTo({
          top: t.getBoundingClientRect().top + w.scrollY - 96,
          behavior: reduce ? 'auto' : 'smooth'
        });
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
            rail.scrollTo({
              left: el.offsetLeft - rail.clientWidth / 2 + el.offsetWidth / 2,
              behavior: reduce ? 'auto' : 'smooth'
            });
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

  /* ───────────── HERO: postal code → address reveal ─────────────
     Client-side simulation with fabricated sample records. The JSON pane
     mirrors the real shape of inquiry/address/v2 so a developer sees exactly
     what their own platform receives. */
  var zCode = d.getElementById('z_code'), zRun = d.getElementById('z_run'),
      zBadge = d.getElementById('z_badge'), zGrid = d.getElementById('z_grid'),
      zFull = d.getElementById('z_full'), zJson = d.getElementById('z_json'),
      zMs = d.getElementById('z_ms'), zStatus = d.getElementById('z_status'),
      zField = d.getElementById('z_field'), zCnt = d.getElementById('z_cnt'),
      zBar = d.getElementById('z_bar'), zLead = d.getElementById('idwLead');

  if (zRun && zGrid) {
    var SAMPLES = [
      { zip: '1993774511', province: 'تهران', county: 'تهران', city: 'تهران',
        district: 'منطقه ۳', hood: 'ونک', main: 'خیابان ولیعصر', side: 'کوچه شهید نصیری',
        plaque: '۲۴', unit: 'طبقه ۲ — واحد ۳' },
      { zip: '8163745192', province: 'اصفهان', county: 'اصفهان', city: 'اصفهان',
        district: 'منطقه ۵', hood: 'مرداویج', main: 'بلوار کشاورز', side: 'خیابان شهید قندی',
        plaque: '۷', unit: 'طبقه همکف' },
      { zip: '4761836254', province: 'مازندران', county: 'بابل', city: 'روستای درزیکلا',
        district: 'بخش مرکزی', hood: 'محله بالا', main: 'جاده اصلی درزیکلا', side: 'کوچه گلستان ۲',
        plaque: '۱۱', unit: '—' }
    ];
    if (w.UID_PA_SAMPLES_OVERRIDE && w.UID_PA_SAMPLES_OVERRIDE.length) {
      SAMPLES = w.UID_PA_SAMPLES_OVERRIDE.map(function (row, i) {
        var base = SAMPLES[i] || SAMPLES[0];
        var out = {};
        Object.keys(base).forEach(function (k) { out[k] = (row[k] !== undefined && row[k] !== '') ? String(row[k]) : base[k]; });
        return out;
      });
    }

    var ROWS = [
      { get: function (s) { return s.province; } },
      { get: function (s) { return s.county; } },
      { get: function (s) { return s.city; } },
      { get: function (s) { return s.district; } },
      { get: function (s) { return s.hood; } },
      { get: function (s) { return s.main; } },
      { get: function (s) { return s.side; } },
      { get: function (s) { return s.plaque; }, num: true },
      { get: function (s) { return s.unit; } }
    ];
    var LABELS = ['استان', 'شهرستان', 'شهر / روستا', 'منطقه پستی', 'محله',
      'معبر اصلی', 'معبر فرعی', 'پلاک', 'طبقه و واحد'];

    var rowEls = Array.prototype.slice.call(zGrid.children);
    var timers = [];
    function clearT() { timers.forEach(clearTimeout); timers = []; }

    function blankRows() {
      rowEls.forEach(function (el, idx) {
        el.classList.remove('in');
        el.innerHTML = '<span class="k">' + LABELS[idx] + '</span><span class="v mut">—</span>';
      });
    }

    function fullAddress(s) {
      var parts = [s.province, s.county, s.city, s.hood, s.main, s.side, 'پلاک ' + s.plaque];
      if (s.unit && s.unit !== '—') parts.push(s.unit);
      return parts.join('، ');
    }

    function paintJson(s, ok) {
      if (!zJson) return;
      if (!ok) {
        zJson.innerHTML =
          '{\n  <span class="k">"responseContext"</span>: {\n    <span class="k">"status"</span>: { <span class="k">"code"</span>: <span class="n">0</span>, <span class="k">"message"</span>: <span class="s">"—"</span> }\n  },\n  <span class="k">"postalCode"</span>: <span class="s">"—"</span>,\n  <span class="k">"address"</span>: <span class="s">"—"</span>\n}';
        return;
      }
      zJson.innerHTML =
        '{\n  <span class="k">"responseContext"</span>: {\n    <span class="k">"status"</span>: {\n      <span class="k">"code"</span>: <span class="n">0</span>,\n      <span class="k">"message"</span>: <span class="s">"SUCCESS."</span>,\n      <span class="k">"details"</span>: []\n    },\n    <span class="k">"requestId"</span>: <span class="s">""</span>,\n    <span class="k">"correlationId"</span>: <span class="s">""</span>\n  },\n  <span class="k">"postalCode"</span>: <span class="s">"' + s.zip + '"</span>,\n  <span class="k">"address"</span>: <span class="s">"' + fullAddress(s) + '"</span>\n}';
    }

    function reset(msg) {
      clearT(); blankRows();
      zBadge.className = 'idbadge wait'; zBadge.textContent = msg || 'در انتظار ورودی';
      if (zFull) { zFull.className = 'v mut'; zFull.textContent = 'نشانی کامل پس از استعلام اینجا ساخته می‌شود'; }
      if (zStatus) zStatus.textContent = '—';
      if (zMs) zMs.textContent = '—';
      if (zLead) zLead.classList.remove('show');
      paintJson(null, false);
    }

    function fill(s) {
      clearT(); blankRows();
      zBadge.className = 'idbadge busy'; zBadge.textContent = 'در حال استعلام…';
      if (zStatus) zStatus.textContent = '…';
      if (zMs) zMs.textContent = '…';
      if (zFull) { zFull.className = 'v mut'; zFull.textContent = 'در حال ساخت نشانی…'; }

      var step = reduce ? 0 : 120;
      var start = reduce ? 0 : 380;

      ROWS.forEach(function (r, idx) {
        timers.push(setTimeout(function () {
          var el = rowEls[idx], val = r.get(s);
          el.innerHTML = '<span class="k">' + LABELS[idx] + '</span>' +
            '<span class="v' + (r.num ? ' num' : '') + (val === '—' ? ' mut' : '') + '">' + val + '</span>';
          el.classList.add('in');
        }, start + idx * step));
      });

      timers.push(setTimeout(function () {
        zBadge.className = 'idbadge ok'; zBadge.textContent = 'SUCCESS — پاسخ دریافت شد';
        if (zStatus) zStatus.textContent = 'code: 0';
        if (zMs) zMs.textContent = fa(Math.floor(Math.random() * 260 + 240)) + ' میلی‌ثانیه';
        if (zFull) { zFull.className = 'v'; zFull.textContent = fullAddress(s); }
        paintJson(s, true);
        if (zLead) zLead.classList.add('show');
      }, start + ROWS.length * step + 120));
    }

    function invalid(msg) {
      clearT(); blankRows();
      zBadge.className = 'idbadge bad'; zBadge.textContent = 'ورودی نامعتبر';
      if (zStatus) zStatus.textContent = 'INVALID_INPUT';
      if (zMs) zMs.textContent = '—';
      if (zFull) { zFull.className = 'v mut'; zFull.textContent = msg; }
      if (zLead) zLead.classList.remove('show');
      paintJson(null, false);
    }

    /* digit counter + fill bar under the hero input */
    function meter() {
      var v = (zCode.value || '').replace(/[^0-9]/g, '');
      if (zCode.value !== v) zCode.value = v;
      if (zCnt) zCnt.textContent = fa(v.length) + ' / ۱۰';
      if (zBar) zBar.style.width = Math.min(v.length / 10, 1) * 100 + '%';
      if (zField) zField.classList.toggle('full', v.length === 10);
      if (zBadge.classList.contains('bad')) reset();
    }
    if (zCode) zCode.addEventListener('input', meter);

    d.querySelectorAll('.uid-postal-address-page [data-zip]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var s = SAMPLES[parseInt(btn.dataset.zip, 10)];
        zCode.value = s.zip; meter(); fill(s);
      });
    });

    zRun.addEventListener('click', function () {
      var v = (zCode.value || '').replace(/[^0-9]/g, '');
      if (v.length !== 10) { invalid('کد پستی باید دقیقاً ۱۰ رقم باشد.'); return; }
      /* deterministic demo record: the same code always returns the same
         sample, because this is illustrative data and not a real inquiry */
      var sum = 0; for (var k = 0; k < v.length; k++) sum += parseInt(v[k], 10);
      var base = SAMPLES[sum % SAMPLES.length];
      var rec = {}; for (var p in base) rec[p] = base[p];
      rec.zip = v;
      fill(rec);
    });
    if (zCode) zCode.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); zRun.click(); }
    });

    reset();
  }

  /* ───────────── SIGNATURE: form auto-fill on scroll ─────────────
     The eight fields on the right fill themselves the first time the block
     enters the viewport — the same beat a real checkout would have. */
  var fcut = d.getElementById('fcut');
  if (fcut) {
    var rows = Array.prototype.slice.call(fcut.querySelectorAll('.fr.auto'));
    function runFill() {
      rows.forEach(function (r, idx) {
        setTimeout(function () { r.classList.add('in'); }, reduce ? 0 : 220 + idx * 150);
      });
    }
    if ('IntersectionObserver' in w) {
      var fio = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) { fio.disconnect(); runFill(); } });
      }, { threshold: .28 });
      fio.observe(fcut);
    } else { runFill(); }
  }

  /* ───────────── VOLUME / TARIFF CALCULATOR ─────────────
     ══════════════════════════════════════════════════════════════════════
     ⚠ تعرفه‌ها: مقادیر زیر نمایشی است. برای انتشار نهایی، فقط همین بلوک را
       با تعرفه واقعی یوآیدی جایگزین کنید — بقیه صفحه نیازی به تغییر ندارد.
         BASE_UNIT  → قیمت پایه هر استعلام موفق (تومان)
         TIERS      → پله‌های تخفیف بر اساس حجم ماهانه
     ══════════════════════════════════════════════════════════════════════ */
  var BASE_UNIT = 1500;
  var TIERS = [
    { max: 10000,   name: 'پلن پایه',     off: 0 },
    { max: 50000,   name: 'پلن رشد',      off: 0.10 },
    { max: 200000,  name: 'پلن کسب‌وکار', off: 0.18 },
    { max: 1000000, name: 'پلن سازمانی',  off: 0.28 },
    { max: Infinity, name: 'پلن اختصاصی', off: null }   /* null → تعرفه قراردادی */
  ];

  var range = d.getElementById('cl_range');
  if (range) {
    var qtyEl = d.getElementById('cl_qty'), planEl = d.getElementById('cl_plan'),
        offEl = d.getElementById('cl_off'), nEl = d.getElementById('cl_n'),
        unitEl = d.getElementById('cl_unit'), grossEl = d.getElementById('cl_gross'),
        totalEl = d.getElementById('cl_total'), presets = d.getElementById('cl_presets'),
        volField = d.getElementById('leadVolume');

    var MIN = 1000, MAX = 2000000;
    function posToQty(p) {                 /* logarithmic so the low end stays usable */
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
        var gross = q * BASE_UNIT;
        var net = q * unit;
        offEl.textContent = t.off > 0 ? fa(Math.round(t.off * 100)) + '٪ تخفیف پلکانی' : 'بدون پله تخفیف';
        unitEl.textContent = faNum(unit) + ' تومان';
        grossEl.textContent = faNum(gross) + ' تومان';
        totalEl.textContent = faNum(net) + ' تومان';
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

  /* ───────────── result-view switch (ساختاری / JSON) ───────────── */
  var rBtns = d.querySelectorAll('.uid-postal-address-page [data-rview]');
  rBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      rBtns.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      d.querySelectorAll('.uid-postal-address-page [data-rpane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.rpane === btn.dataset.rview);
      });
    });
  });
})();
