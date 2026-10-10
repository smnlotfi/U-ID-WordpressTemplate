/**
 * رفتار تعاملی اختصاصی صفحه «یوآیدی‌پلاس (PWA)» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال درخواست دمو، دکمه‌های
 * شناور تماس و بازگشت به بالا از هدر/فوتر مشترک سایت (assets/js/main.js) می‌آیند —
 * اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
 */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function faDigits(s) { return String(s).replace(/[0-9]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'[x]; }); }
  function grp(n) { return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '٬'); }

  /* ---- محاسبه‌گر هزینه ریزش (هیرو) ---- */
  (function () {
    var vol = d.getElementById('vol'), cac = d.getElementById('cac');
    if (!vol) return;
    var volOut = d.getElementById('volOut'), cacOut = d.getElementById('cacOut');
    var oApi = d.getElementById('oApi'), oPwa = d.getElementById('oPwa'),
        oGap = d.getElementById('oGap'), oYear = d.getElementById('oYear'),
        oDay = d.getElementById('oDay');
    var engine = d.getElementById('engine');
    var API = parseFloat((engine && engine.dataset.rateApi) || '0.2359');
    var PWA = parseFloat((engine && engine.dataset.ratePwa) || '0.73185');
    function f(n) { return faDigits(grp(n)); }
    function paint() {
      var v = +vol.value, c = +cac.value;
      var a = v * API, p = v * PWA, g = p - a, year = g * c * 12, day = year / 365;
      volOut.innerHTML = f(v) + '<small>نفر</small>';
      cacOut.innerHTML = f(c) + '<small>تومان</small>';
      oApi.textContent = f(a); oPwa.textContent = f(p);
      oGap.textContent = f(g); oYear.textContent = f(year);
      if (oDay) oDay.textContent = f(Math.round(day / 100000) * 100000);
      [vol, cac].forEach(function (el) {
        var pc = ((el.value - el.min) / (el.max - el.min)) * 100;
        el.style.background = 'linear-gradient(to right, var(--orange) ' + pc + '%, rgba(255,255,255,.16) ' + pc + '%)';
      });
    }
    vol.addEventListener('input', paint); cac.addEventListener('input', paint);
    paint();
  })();

  /* ---- استپر تعاملی هفت‌مرحله‌ای + نمای گوشی ---- */
  (function () {
    var track = d.getElementById('stTrack');
    if (!track) return;
    var nodes = track.querySelectorAll('.st-node');
    var panes = d.querySelectorAll('.st-pane');
    var prev = d.getElementById('stPrev'), next = d.getElementById('stNext'), count = d.getElementById('stCount');
    var screens = d.querySelectorAll('#phone .scr');
    var stage = d.querySelector('.st-stage');
    var i = 0, total = nodes.length, timer = null, userTook = false;
    function go(n) {
      i = Math.max(0, Math.min(total - 1, n));
      nodes.forEach(function (el, k) {
        el.classList.toggle('on', k === i);
        el.classList.toggle('done', k <= i);
        el.setAttribute('aria-selected', k === i ? 'true' : 'false');
      });
      panes.forEach(function (p, k) { p.hidden = k !== i; });
      if (count) count.textContent = faDigits((i + 1) + ' / ' + total);
      if (prev) prev.disabled = i === 0;
      if (next) next.disabled = i === total - 1;
      screens.forEach(function (s, k) { s.classList.toggle('on', k === i); });
      var on = nodes[i];
      if (track.scrollWidth > track.clientWidth) {
        track.scrollTo({ left: on.offsetLeft - track.clientWidth / 2 + on.offsetWidth / 2, behavior: 'smooth' });
      }
    }
    function stop() { clearInterval(timer); timer = null; userTook = true; if (stage) stage.classList.add('is-paused'); }
    function play() {
      if (reduce || userTook) return;
      clearInterval(timer);
      timer = setInterval(function () { go((i + 1) % total); }, 4200);
    }
    nodes.forEach(function (el, k) { el.addEventListener('click', function () { stop(); go(k); }); });
    if (prev) prev.addEventListener('click', function () { stop(); go(i - 1); });
    if (next) next.addEventListener('click', function () { stop(); go(i + 1); });
    if ('IntersectionObserver' in w) {
      new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) { play(); } else { clearInterval(timer); timer = null; } });
      }, { threshold: .35 }).observe(d.querySelector('.stepper'));
    } else { play(); }
    track.addEventListener('keydown', function (e) {
      if (e.key === 'ArrowLeft') { e.preventDefault(); go(i + 1); }
      if (e.key === 'ArrowRight') { e.preventDefault(); go(i - 1); }
    });
  })();

  /* ---- قوانین مسیریابی فرم بند نهایی: کاربران حقیقی را به صفحه ثنا هدایت کن ---- */
  var sel = d.querySelector('[data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () { routeAlert.classList.toggle('show', sel.value === 'ind'); });
  }

  /* ---- اعتبارسنجی و ارسال فرم‌های این صفحه (محاسبه‌گر + بند تماس) ---- */
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
  d.querySelectorAll('#microForm [data-submit], #bandForm [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('#microForm .fld input, #microForm .fld select, #bandForm .fld input, #bandForm .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  var revealEls = d.querySelectorAll('.rv, .fnl, [data-count]');
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
  d.querySelectorAll('[data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = faDigits(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.faq-q').forEach(function (q) {
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

  /* ---- دات‌های کاروسل عمومی (نشت‌ها، تیم، مزایا، قیمت‌گذاری) ---- */
  d.querySelectorAll('[data-rail]').forEach(function (rail) {
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
  d.querySelectorAll('.uid-pwa-page a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault();
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ---- تب‌های جزئیات سرویس + تب‌های نمونه‌کد یکپارچه‌سازی ---- */
  d.querySelectorAll('[data-dtab]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var wrap = btn.closest('.dtabs');
      wrap.querySelectorAll('[data-dtab]').forEach(function (x) { x.classList.remove('on'); x.setAttribute('aria-selected', 'false'); });
      wrap.querySelectorAll('[data-dpane]').forEach(function (x) { x.classList.remove('on'); });
      btn.classList.add('on'); btn.setAttribute('aria-selected', 'true');
      wrap.querySelector('[data-dpane="' + btn.dataset.dtab + '"]').classList.add('on');
    });
  });
  d.querySelectorAll('[data-code]').forEach(function (b) {
    b.addEventListener('click', function () {
      var wrap = b.closest('.codewrap');
      wrap.querySelectorAll('[data-code]').forEach(function (x) { x.classList.remove('on'); });
      wrap.querySelectorAll('[data-pane]').forEach(function (x) { x.classList.remove('on'); });
      b.classList.add('on');
      wrap.querySelector('[data-pane="' + b.dataset.code + '"]').classList.add('on');
    });
  });
})();
