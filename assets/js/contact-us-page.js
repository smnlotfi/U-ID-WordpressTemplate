/**
 * رفتار تعاملی اختصاصی صفحه «تماس با ما» — فقط روی این قالب صفحه بارگذاری می‌شود.
 * نکته: منوی هدر/مگامنو، شیت موبایل، مودال درخواست تماس (باز/بسته‌شدن)، دکمه‌های
 * شناور تماس، بازگشت به بالا و نوار پیشرفت اسکرول از assets/js/main.js می‌آیند —
 * اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند. ارسال واقعی فرم هم از طریق
 * window.uidSubmitForm (تعریف‌شده در main.js) به همان اندپوینت AJAX مشترک سایت
 * می‌رود؛ اینجا فقط اعتبارسنجی سمت کاربر و رفتار «مسیریابی بخش‌ها» پیاده‌سازی شده.
 */
(function () {
  'use strict';
  var d = document, w = window;
  var $ = function (s, r) { return (r || d).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || d).querySelectorAll(s)); };
  var root = $('.uid-contact-us-page');
  if (!root) return;

  /* ── ارقام فارسی ───────────────────────────────────────────────────── */
  var FA = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
  function fa(n) { return String(n).replace(/\d/g, function (x) { return FA[+x]; }); }
  function pad2(n) { return (n < 10 ? '0' : '') + n; }
  function toLatinDigits(s) {
    return String(s)
      .replace(/[۰-۹]/g, function (c) { return String(c.charCodeAt(0) - 0x06F0); })
      .replace(/[٠-٩]/g, function (c) { return String(c.charCodeAt(0) - 0x0660); });
  }

  /* ══════════════════════════════════════════════════════════════════════
   * ساعت زنده تهران → وضعیت پاسخگویی
   * ساعت بازدیدکننده را می‌خواند، به وقت تهران تبدیل می‌کند و با بازه منتشرشده
   * شنبه–چهارشنبه ۹:۰۰–۱۸:۰۰ می‌سنجد. اگر ساعات کاری واقعی تغییر کرد، ثابت‌های
   * زیر را هماهنگ با متن «avail_hours_line» در پیشخوان به‌روزرسانی کنید.
   * ══════════════════════════════════════════════════════════════════════ */
  var OPEN_H = 9, CLOSE_H = 18;
  var ORDER = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
  var OPEN_DAYS = { Sat: 1, Sun: 1, Mon: 1, Tue: 1, Wed: 1 };
  var FA_DAY = { Sat: 'شنبه', Sun: 'یکشنبه', Mon: 'دوشنبه', Tue: 'سه‌شنبه', Wed: 'چهارشنبه', Thu: 'پنجشنبه', Fri: 'جمعه' };

  function tehranNow() {
    var now = new Date();
    try {
      var parts = new Intl.DateTimeFormat('en-US', {
        timeZone: 'Asia/Tehran', hour12: false,
        weekday: 'short', hour: '2-digit', minute: '2-digit', second: '2-digit'
      }).formatToParts(now);
      var p = {};
      parts.forEach(function (x) { p[x.type] = x.value; });
      var h = parseInt(p.hour, 10); if (h === 24) h = 0;
      return { wd: p.weekday, h: h, m: parseInt(p.minute, 10), s: parseInt(p.second, 10) };
    } catch (e) {
      var t = new Date(now.getTime() + (now.getTimezoneOffset() + 210) * 60000);
      var names = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      return { wd: names[t.getDay()], h: t.getHours(), m: t.getMinutes(), s: t.getSeconds() };
    }
  }

  function humanGap(mins) {
    if (mins < 1) return 'کمتر از یک دقیقه';
    var h = Math.floor(mins / 60), m = mins % 60;
    if (h === 0) return fa(m) + ' دقیقه';
    if (m === 0) return fa(h) + ' ساعت';
    return fa(h) + ' ساعت و ' + fa(m) + ' دقیقه';
  }

  var elClock = $('#clock', root), elAvail = $('#avail', root),
      elTitle = $('#stateTitle', root), elNote = $('#stateNote', root), elIcon = $('#stateIcon', root),
      elBadge = $('#phoneBadge', root), elPhoneNote = $('#phoneNote', root),
      elPhone = $('#chanPhone', root), elChans = $('.chans', root);

  var ICON_OPEN = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>';
  var ICON_SHUT = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9.5"/><path d="M12 6.5V12l3.5 2"/></svg>';

  function tick() {
    var t = tehranNow();
    if (elClock) elClock.textContent = pad2(t.h) + ':' + pad2(t.m) + ':' + pad2(t.s);

    var mins = t.h * 60 + t.m;
    var isWorkday = !!OPEN_DAYS[t.wd];
    var open = isWorkday && mins >= OPEN_H * 60 && mins < CLOSE_H * 60;

    if (elAvail) elAvail.classList.toggle('shut', !open);
    if (elChans) elChans.classList.toggle('async-first', !open);
    if (elPhone) {
      elPhone.classList.toggle('is-now', open);
      elPhone.classList.toggle('is-off', !open);
    }

    if (open) {
      var left = CLOSE_H * 60 - mins;
      if (elIcon) elIcon.innerHTML = ICON_OPEN;
      if (elTitle) elTitle.textContent = 'الان باز هستیم';
      if (elNote) elNote.textContent = 'تا ساعت ۱۸:۰۰ پاسخگوی تماس شما هستیم — حدود ' + humanGap(left) + ' دیگر.';
      if (elBadge) elBadge.textContent = 'باز';
      if (elPhoneNote) elPhoneNote.textContent = 'سریع‌ترین راه — همین حالا پاسخ داده می‌شود';
    } else {
      var idx = ORDER.indexOf(t.wd);
      var gap = 0, found = null;
      for (var i = 0; i < 8; i++) {
        var dd = ORDER[(idx + i) % 7];
        if (OPEN_DAYS[dd] && !(i === 0 && mins >= OPEN_H * 60)) { found = dd; gap = i; break; }
      }
      var until = (gap * 24 * 60) + (OPEN_H * 60) - mins;
      var dayName = found ? FA_DAY[found] : 'شنبه';
      var when = (gap === 0) ? 'امروز ساعت ۹:۰۰' : (gap === 1 ? 'فردا (' + dayName + ') ساعت ۹:۰۰' : dayName + ' ساعت ۹:۰۰');

      if (elIcon) elIcon.innerHTML = ICON_SHUT;
      if (elTitle) elTitle.textContent = 'الان بسته است';
      var gapPhrase = (until > 24 * 60) ? '' : ' — حدود ' + humanGap(until) + ' دیگر';
      if (elNote) elNote.textContent = 'بازگشایی ' + when + gapPhrase + '. تلگرام، ایمیل و فرم باز هستند و پیام‌ها در نخستین روز کاری بررسی می‌شوند.';
      if (elBadge) elBadge.textContent = 'بسته';
      if (elPhoneNote) elPhoneNote.textContent = 'خارج از ساعات کاری کسی پاسخ نمی‌دهد';
    }
  }
  if (elClock) { tick(); setInterval(tick, 1000); }

  /* ══════════════════════════════════════════════════════════════════════
   * مسیریابی بخش‌ها — یک سوال، پیش از هر چیز دیگری.
   * محتوای این پیکربندی (عنوان/راهنما/پیام موفقیت فرم و حالت فیلد «نام کسب‌وکار»
   * به ازای هر تب) عمداً ثابت است — دقیقاً هماهنگ با uid_cu_router_routes() در
   * inc/contact-us-page.php؛ برای تغییر هر دو را با هم ویرایش کنید.
   * ══════════════════════════════════════════════════════════════════════ */
  var ROUTES = {
    sana: { form: false },
    biz: {
      form: true, org: 'required',
      title: 'ارسال پیام به بخش کسب‌وکارها',
      hint: 'کارشناس در ساعات کاری با شما تماس می‌گیرد.',
      ok: 'پیام شما به بخش کسب‌وکارها رسید. در ساعات کاری تماس می‌گیریم؛ اگر عجله دارید، ۰۲۱۶۶۱۲۳۲۹۰ سریع‌تر است.'
    },
    dev: {
      form: true, org: 'optional', orgLabel: 'نام کسب‌وکار یا پروژه',
      title: 'ارسال پیام به پشتیبانی فنی',
      hint: 'متن دقیق خطا و زمان رخ دادن آن را بنویسید تا سریع‌تر بررسی شود.',
      ok: 'پیام شما به پشتیبانی فنی رسید. پاسخ را از همان راه ارتباطی که وارد کردید می‌فرستیم.'
    },
    support: {
      form: true, org: 'optional', orgLabel: 'نام کسب‌وکار یا شناسه مشتری',
      title: 'پیگیری سرویس فعال',
      hint: 'برای بررسی سریع‌تر، جزئیات مورد و زمان آن را بنویسید.',
      ok: 'پیگیری شما ثبت شد. برای موارد فوری، تلفن و تلگرام سریع‌تر پاسخ می‌دهند.'
    },
    press: {
      form: true, org: 'hidden',
      title: 'رسانه و همکاری',
      hint: 'موضوع همکاری یا درخواست خود را کوتاه بنویسید.',
      ok: 'پیام شما ثبت شد. برای ارسال فایل و مکاتبات رسمی، info@uid.ir در دسترس است.'
    },
    none: {
      form: true, org: 'hidden',
      title: 'ارسال پیام',
      hint: 'نام، یک راه ارتباطی و توضیح کوتاه — همین برای تماس گرفتن با شما کافی است.',
      ok: 'پیام شما ثبت شد. در ساعات کاری با شما تماس می‌گیریم؛ اگر عجله دارید، ۰۲۱۶۶۱۲۳۲۹۰ سریع‌تر است.'
    }
  };

  var routerBox = $('#routerBox', root), fOrg = $('#f-org', root), iOrg = $('#i-org', root),
      orgOpt = $('#orgOpt', root), fTitle = $('#formTitle', root), fHint = $('#formHint', root),
      iIntent = $('#i-intent', root), okNote = $('#okNote', root);

  function applyRoute(key) {
    var cfg = ROUTES[key] || ROUTES.none;

    $$('.rchip', root).forEach(function (c) {
      var on = c.getAttribute('data-r') === key;
      c.classList.toggle('on', on);
      c.setAttribute('aria-selected', on ? 'true' : 'false');
    });
    $$('.rpanel', root).forEach(function (p) {
      p.classList.toggle('on', p.getAttribute('data-p') === key);
    });

    if (routerBox) routerBox.classList.toggle('no-form', !cfg.form);
    if (iIntent) iIntent.value = (key === 'none' ? '' : key);

    if (cfg.form) {
      if (fTitle) fTitle.textContent = cfg.title;
      if (fHint) fHint.textContent = cfg.hint;
      if (okNote) okNote.textContent = cfg.ok;

      var mode = cfg.org || 'hidden';
      if (fOrg) {
        fOrg.classList.toggle('on', mode !== 'hidden');
        fOrg.classList.remove('bad');
        if (mode === 'hidden' && iOrg) iOrg.value = '';
      }
      if (iOrg) {
        iOrg.required = (mode === 'required');
        var lbl = fOrg ? fOrg.querySelector('label') : null;
        if (lbl) {
          var txt = (mode === 'required') ? 'نام کسب‌وکار' : (cfg.orgLabel || 'نام کسب‌وکار');
          lbl.childNodes[0].nodeValue = txt + ' ';
        }
        if (orgOpt) orgOpt.style.display = (mode === 'required') ? 'none' : '';
      }
    }
  }

  $$('.rchip', root).forEach(function (c) {
    c.addEventListener('click', function () { applyRoute(c.getAttribute('data-r')); });
  });
  if (routerBox) applyRoute('none');

  /* ══════════════════════════════════════════════════════════════════════
   * فرم — سه فیلد، اعتبارسنجی درجا، بدون کپچا. ارسال واقعی از طریق
   * window.uidSubmitForm (به همان اندپوینت مشترک سایت assets/js/main.js).
   * ══════════════════════════════════════════════════════════════════════ */
  var form = $('#cform', root);
  var RE_MAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
  var RE_MOBILE = /^09\d{9}$/;
  var RE_TEL = /^0\d{9,10}$/;

  function setBad(fld, bad, msg) {
    if (!fld) return;
    fld.classList.toggle('bad', !!bad);
    if (msg) {
      var t = fld.querySelector('.errtx');
      if (t) t.textContent = msg;
    }
  }

  function checkName(silent) {
    var f = $('#f-name', root), v = $('#i-name', root).value.trim();
    var ok = v.length >= 2;
    if (!silent) setBad(f, !ok);
    if (ok) f.classList.add('good');
    return ok;
  }
  function checkContact(silent) {
    var f = $('#f-contact', root), raw = $('#i-contact', root).value.trim();
    var v = toLatinDigits(raw).replace(/[\s\-()]/g, '');
    var ok = RE_MAIL.test(raw.trim()) || RE_MOBILE.test(v) || RE_TEL.test(v);
    if (!silent) {
      setBad(f, !ok, raw ? 'یک شماره موبایل یا ایمیل معتبر وارد کنید.' : 'یک راه ارتباطی وارد کنید — موبایل یا ایمیل.');
    }
    f.classList.toggle('good', ok);
    return ok;
  }
  function checkOrg(silent) {
    var f = $('#f-org', root);
    if (!iOrg || !iOrg.required) { if (f) f.classList.remove('bad'); return true; }
    var ok = iOrg.value.trim().length >= 2;
    if (!silent) setBad(f, !ok);
    return ok;
  }
  function checkMsg(silent) {
    var f = $('#f-msg', root), ok = $('#i-msg', root).value.trim().length >= 5;
    if (!silent) setBad(f, !ok);
    f.classList.toggle('good', ok);
    return ok;
  }

  function wire(inputSel, fldSel, fn) {
    var el = $(inputSel, root), fld = $(fldSel, root);
    if (!el) return;
    el.addEventListener('blur', function () { if (el.value.trim() !== '') fn(false); });
    el.addEventListener('input', function () {
      if (fld && fld.classList.contains('bad')) fn(false);
    });
  }
  wire('#i-name', '#f-name', checkName);
  wire('#i-contact', '#f-contact', checkContact);
  wire('#i-org', '#f-org', checkOrg);
  wire('#i-msg', '#f-msg', checkMsg);

  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      /* هانی‌پات: ربات این فیلد را پر می‌کند، انسان اصلاً آن را نمی‌بیند */
      var hp = $('#i-hp', root);
      if (hp && hp.value !== '') { form.classList.add('is-sent'); return; }

      var a = checkName(), b = checkContact(), c = checkOrg(), msgOk = checkMsg();
      if (a && b && c && msgOk) {
        var btn = form.querySelector('button[type="submit"]');
        if (w.uidSubmitForm) {
          w.uidSubmitForm(form, btn);
        } else {
          form.classList.add('is-sent');
        }
        var ok = form.querySelector('.form-ok');
        if (ok && ok.focus) ok.focus();
        return;
      }
      var first = form.querySelector('.fld.bad input, .fld.bad textarea');
      if (first) first.focus();
    });
  }

  /* ── کپی در کلیپ‌بورد + پیام تایید ───────────────────────────────────── */
  var toast = $('#toast', root), toastTx = $('#toastTx', root), toastT = null;
  function say(msg) {
    if (!toast) return;
    if (toastTx) toastTx.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastT);
    toastT = setTimeout(function () { toast.classList.remove('show'); }, 2200);
  }
  $$('.copybtn', root).forEach(function (b) {
    b.addEventListener('click', function () {
      var txt = b.getAttribute('data-copy') || '';
      var label = b.getAttribute('data-label') || 'متن';
      var done = function () {
        b.classList.add('done');
        var s = b.querySelector('span'); if (s) s.textContent = 'کپی شد';
        say(label + ' کپی شد');
        setTimeout(function () {
          b.classList.remove('done');
          if (s) s.textContent = 'کپی';
        }, 2000);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(txt).then(done, function () { say('کپی نشد — متن را دستی انتخاب کنید'); });
      } else {
        try {
          var ta = d.createElement('textarea');
          ta.value = txt; ta.setAttribute('readonly', '');
          ta.style.position = 'fixed'; ta.style.opacity = '0';
          d.body.appendChild(ta); ta.select();
          d.execCommand('copy'); d.body.removeChild(ta);
          done();
        } catch (err) { say('کپی نشد — متن را دستی انتخاب کنید'); }
      }
    });
  });

  /* ── آکاردئون سوالات متداول ───────────────────────────────────────────
   * برخلاف الگوی تک‌بازشوی برخی صفحات دیگر (مثل shahkar-page.js)، اینجا دقیقاً
   * مطابق طرح اصلی چند مورد می‌توانند هم‌زمان باز باشند. */
  $$('.faq-q', root).forEach(function (q) {
    q.addEventListener('click', function () {
      var item = q.parentNode, ans = item.querySelector('.faq-a');
      var open = item.classList.toggle('open');
      q.setAttribute('aria-expanded', open ? 'true' : 'false');
      ans.style.maxHeight = open ? (ans.scrollHeight + 'px') : '0px';
    });
  });
  w.addEventListener('resize', function () {
    $$('.faq-i.open .faq-a', root).forEach(function (a) { a.style.maxHeight = a.scrollHeight + 'px'; });
  });

  /* ── ظاهرشدن هنگام اسکرول ─────────────────────────────────────────────── */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); } });
  }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 }) : null;
  $$('.rv', root).forEach(function (el) {
    if (io) io.observe(el); else el.classList.add('is-in');
  });
})();
