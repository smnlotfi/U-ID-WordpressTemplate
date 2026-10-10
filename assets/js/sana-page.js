/**
 * رفتار تعاملی اختصاصی صفحه «احراز هویت ثنا» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال درخواست دمو، دکمه‌های
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
  d.querySelectorAll('.uid-sana-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-sana-page .fld input, .uid-sana-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  var revealEls = d.querySelectorAll('.uid-sana-page .rv, .uid-sana-page [data-count]');
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
  d.querySelectorAll('.uid-sana-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-sana-page .faq-q').forEach(function (q) {
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

  /* ---- دات‌های کاروسل موبایل (مزایا، سرویس‌ها، تیم) ---- */
  d.querySelectorAll('.uid-sana-page [data-rail]').forEach(function (rail) {
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
  d.querySelectorAll('.uid-sana-page a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault();
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ═════════════════════════════════════════════════════════════════════
     وضعیت زنده ساعات پشتیبانی (شنبه تا چهارشنبه، ۹ تا ۱۸، به وقت تهران)
     ═════════════════════════════════════════════════════════════════════ */
  var statusEl = d.getElementById('supStatus'), titleEl = d.getElementById('supTitle'), subEl = d.getElementById('supSub');
  if (statusEl) {
    try {
      var fmt = new Intl.DateTimeFormat('en-US', { timeZone: 'Asia/Tehran', hour12: false, weekday: 'short', hour: '2-digit', minute: '2-digit' });
      var parts = fmt.formatToParts(new Date());
      var map = {}; parts.forEach(function (p) { map[p.type] = p.value; });
      var wd = map.weekday, hh = parseInt(map.hour, 10), mm = parseInt(map.minute, 10);
      var minutes = hh * 60 + mm;
      var openDays = { Sat: 1, Sun: 1, Mon: 1, Tue: 1, Wed: 1, Thu: 0, Fri: 0 };
      var isOpen = openDays[wd] && minutes >= 9 * 60 && minutes < 18 * 60;
      if (isOpen) {
        titleEl.textContent = 'پشتیبانی هم‌اکنون پاسخگوست';
        subEl.textContent = 'کارشناسان ما آماده‌ی راهنمایی شما هستند';
      } else {
        statusEl.classList.add('off');
        titleEl.textContent = 'خارج از ساعات پاسخگویی تلفنی';
        subEl.textContent = 'فرم را ثبت کنید؛ شنبه تا چهارشنبه ۹ صبح تا ۶ عصر تماس می‌گیریم';
      }
    } catch (e) { subEl.textContent = 'شنبه تا چهارشنبه، ۹ صبح تا ۶ عصر'; }
  }

  /* ---- پیشرفت چک‌لیست آمادگی ---- */
  var boxes = d.querySelectorAll('#readyList [data-ready]'), bar = d.getElementById('readyBar'), msg = d.getElementById('readyMsg');
  function updateReady() {
    var total = boxes.length, done = 0;
    boxes.forEach(function (b) { if (b.checked) done++; });
    if (bar) bar.style.width = (done / total * 100) + '%';
    if (msg) msg.textContent = fa(done) + ' از ' + fa(total) + ' آماده';
  }
  boxes.forEach(function (b) { b.addEventListener('change', updateReady); });
  if (boxes.length) updateReady();
})();
