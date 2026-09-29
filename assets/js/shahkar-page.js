/**
 * رفتار تعاملی اختصاصی صفحه «وب‌سرویس شاهکار» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال درخواست دمو، دکمه‌های
 * شناور تماس و بازگشت به بالا از هدر/فوتر مشترک سایت (assets/js/main.js) می‌آیند —
 * اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
 */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
  d.querySelectorAll('.uid-shahkar-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-shahkar-page .fld input, .uid-shahkar-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  d.querySelectorAll('.uid-shahkar-page .rv, .uid-shahkar-page [data-count]').forEach(function (el) { if (io) io.observe(el); else el.classList.add('is-in'); });

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
  d.querySelectorAll('.uid-shahkar-page [data-count]').forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-shahkar-page .faq-q').forEach(function (q) {
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
  d.querySelectorAll('.uid-shahkar-page [data-rail]').forEach(function (rail) {
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
  d.querySelectorAll('.uid-shahkar-page a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var id = a.getAttribute('href'); if (id.length < 2) return;
      var t = d.querySelector(id); if (!t) return;
      e.preventDefault();
      w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
    });
  });

  /* ---- تب‌های دمو (امتحان کنید / این استعلام چیست) ---- */
  var demoTabs = d.querySelectorAll('.uid-shahkar-page [data-demo-tab]');
  demoTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      demoTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.demoTab;
      d.querySelectorAll('.uid-shahkar-page [data-demo-pane]').forEach(function (p) {
        p.style.display = (p.dataset.demoPane === key) ? '' : 'none';
      });
    });
  });

  /* ---- تب‌های نمونه‌کد + کپی ---- */
  var codeTabs = d.querySelectorAll('.uid-shahkar-page [data-code-tab]');
  codeTabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      codeTabs.forEach(function (b) { b.classList.remove('on'); });
      btn.classList.add('on');
      var key = btn.dataset.codeTab;
      d.querySelectorAll('.uid-shahkar-page [data-code-pane]').forEach(function (p) {
        p.classList.toggle('on', p.dataset.codePane === key);
      });
    });
  });
  var copyBtn = d.querySelector('.uid-shahkar-page [data-copy]');
  if (copyBtn) {
    copyBtn.addEventListener('click', function () {
      var active = d.querySelector('.uid-shahkar-page .code-pane.on');
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

  /* ═════════════════════════════════════════════════════════════════════
     شبیه‌سازی زنده دموی هیرو — تماماً نمایشی، ساختار پاسخ واقعی است
     ═════════════════════════════════════════════════════════════════════ */
  var nidEl = d.getElementById('d_nid'), mobEl = d.getElementById('d_mob'),
      runBtn = d.getElementById('d_run'), badge = d.getElementById('d_badge'), json = d.getElementById('d_json');
  function render(state) {
    if (!badge || !json) return;
    if (state === 'empty') {
      badge.className = 'demo-badge wait'; badge.textContent = 'در انتظار ورودی';
      json.innerHTML = '{\n  "isMatched": <span class="k">null</span>,\n  "responseContext": { "status": { "code": 0, "message": "کد ملی و شماره موبایل را وارد کنید" } }\n}';
      return;
    }
    if (state === 'invalid') {
      badge.className = 'demo-badge bad'; badge.textContent = 'ورودی نامعتبر';
      json.innerHTML = '{\n  "isMatched": <span class="b-false">false</span>,\n  "responseContext": {\n    "status": { "code": 3, "message": "کد ملی باید ۱۰ رقم و شماره موبایل باید با ۰۹ شروع و ۱۱ رقم باشد" }\n  }\n}';
      return;
    }
    var matched = state === 'matched';
    badge.className = 'demo-badge ' + (matched ? 'ok' : 'bad');
    badge.textContent = matched ? 'true — تطابق دارد' : 'false — تطابق ندارد';
    json.innerHTML =
      '{\n  "isMatched": <span class="' + (matched ? 'b-true' : 'b-false') + '">' + matched + '</span>,\n' +
      '  "responseContext": {\n' +
      '    "status": {\n' +
      '      "code": ' + (matched ? 1 : 2) + ',\n' +
      '      "message": "' + (matched ? 'شماره موبایل متعلق به صاحب کد ملی است' : 'شماره موبایل متعلق به صاحب کد ملی نیست') + '",\n' +
      '      "details": []\n    },\n' +
      '    "requestId": "' + fa(Math.floor(Math.random() * 900000 + 100000)) + '"\n  }\n}';
  }
  if (badge) render('empty');
  if (runBtn) {
    runBtn.addEventListener('click', function () {
      var nid = (nidEl.value || '').replace(/[^0-9]/g, '');
      var mob = (mobEl.value || '').replace(/[^0-9]/g, '');
      if (nid.length !== 10 || !/^09\d{9}$/.test(mob)) { render('invalid'); return; }
      badge.className = 'demo-badge wait'; badge.textContent = 'در حال بررسی…';
      json.textContent = 'در حال ارسال درخواست به سرور…';
      setTimeout(function () {
        var sum = 0; for (var i = 0; i < nid.length; i++) sum += parseInt(nid[i], 10);
        var matched = (sum + parseInt(mob.slice(-1), 10)) % 2 === 0;
        render(matched ? 'matched' : 'unmatched');
      }, 550);
    });
  }
})();
