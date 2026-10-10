/**
 * رفتار تعاملی اختصاصی صفحه «فهرست وب‌سرویس‌های احراز هویت» — فقط روی این قالب صفحه
 * بارگذاری می‌شود. نکته: نوار پیشرفت اسکرول، منوی هدر/مگامنو، شیت موبایل، مودال
 * درخواست تماس، دکمه‌های شناور تماس و بازگشت به بالا از هدر/فوتر مشترک سایت
 * (assets/js/main.js) می‌آیند — اینجا عمداً تکرار نشده‌اند تا با آن‌ها تداخل نکنند.
 *
 * سه بخش مستقل:
 *   ۱) فرم‌ها + مسیریابی واجدشرایط + ظاهرشدن هنگام اسکرول + شمارش صعودی + آکاردئون
 *      سوالات متداول + دات‌های کاروسل موبایل + اسکرول نرم لنگرها (دقیقاً مثل بقیه
 *      صفحات وب‌سرویس).
 *   ۲) نوار پرش سریع موبایل — عنصر ساختاری ثابت، مثل inc/postal-address-page.php.
 *   ۳) رفتار اختصاصی همین صفحه: مسیریاب انتخاب سرویس، کنسول مشترک هشت وب‌سرویس
 *      (داده‌های نمونه کاملاً در همین فایل تعریف شده‌اند)، فهرست/فیلتر/جست‌وجوی
 *      کاتالوگ سرویس‌ها، و سیستم «فصل تاخوردنی + خواننده فصل» موبایل — دقیقاً مثل
 *      assets/js/ekyc-liveness-page.js.
 */

(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
  d.querySelectorAll('.uid-web-services-hub-page [data-submit]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var form = btn.closest('form');
      if (form && validate(form)) window.uidSubmitForm(form, btn);
    });
  });
  d.querySelectorAll('.uid-web-services-hub-page .fld input, .uid-web-services-hub-page .fld select').forEach(function (f) {
    f.addEventListener('input', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
    f.addEventListener('change', function () { var w = f.closest('.fld'); if (w) w.classList.remove('bad'); });
  });

  /* ---- مسیریابی واجدشرایط: کاربران شخصی را به جای صف فروش، به صفحه درست هدایت کن ---- */
  var sel = d.querySelector('.uid-web-services-hub-page [data-route]'), routeAlert = d.getElementById('routeAlert');
  if (sel && routeAlert) {
    sel.addEventListener('change', function () {
      routeAlert.classList.toggle('show', sel.value === 'ind');
    });
  }

  /* ---- ظاهرشدن هنگام اسکرول ---- */
  var io = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
    es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .12, rootMargin: '0px 0px -8% 0px' }) : null;
  var revealEls = d.querySelectorAll('.uid-web-services-hub-page .rv, .uid-web-services-hub-page .fnl, .uid-web-services-hub-page [data-count]');
  revealEls.forEach(function (el) {
    if (!io) { el.classList.add('is-in'); return; }
    var rect = el.getBoundingClientRect();
    if (rect.top < w.innerHeight && rect.bottom > 0) { el.classList.add('is-in'); } else { io.observe(el); }
  });
  // Safety net: never leave content hidden forever if the browser stalls
  // the first IntersectionObserver callback on an idle page.
  if (io) { setTimeout(function () { io.disconnect(); revealEls.forEach(function (el) { el.classList.add('is-in'); }); }, 1200); }

  /* ---- شمارش صعودی اعداد ---- */
  function fa(s) { return String(s).replace(/[0-9]/g, function (x) { return '۰۱۲۳۴۵۶۷۸۹'[x]; }); }
  function grp(n) { return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '٬'); }
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
  var countEls = d.querySelectorAll('.uid-web-services-hub-page [data-count]');
  countEls.forEach(function (el) { if (cio) cio.observe(el); else el.textContent = fa(grp(el.dataset.count)); });
  if (cio) {
    setTimeout(function () {
      cio.disconnect();
      countEls.forEach(function (el) {
        var to = parseFloat(el.dataset.count), dec = parseInt(el.dataset.dec || '0', 10);
        el.textContent = fa(dec ? to.toFixed(dec) : grp(to));
      });
    }, 1200);
  }

  /* ---- آکاردئون سوالات متداول ---- */
  d.querySelectorAll('.uid-web-services-hub-page .faq-q').forEach(function (q) {
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
  d.querySelectorAll('.uid-web-services-hub-page [data-rail]').forEach(function (rail) {
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

  /* ---- اسکرول نرم لنگرها ---- */
  d.querySelectorAll('.uid-web-services-hub-page a[href^="#"]').forEach(function (a) {
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
})();

/* ---- نوار پرش سریع موبایل — عنصر ساختاری ثابت (نه یک سکشن محتوایی) ---- */
(function () {
  'use strict';
  var d = document, w = window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
})();

/* ══════════════════════════════════════════════════════════════════════════
   HUB PAGE — page-specific behaviour
   ──────────────────────────────────────────────────────────────────────────
   Four mechanisms, in the order the visitor meets them:
     1. ROUTER    — one question narrows fourteen services to two or three.
     2. CONSOLE   — one panel, eight simulators. Each scenario below is the
                    one the client's service brief specifies for that service;
                    nothing here invents a behaviour the brief does not state.
     3. DIRECTORY — filter chips + search over the catalogue, and on phones
                    the two-column tiles that expand in place.
     4. FOLD      — the chapter fold / reader layer shared with the eleven
                    service pages.
   All sample data is fabricated demo data and is labelled as such on screen.
   ══════════════════════════════════════════════════════════════════════════ */
(function(){
  'use strict';
  var d=document, w=window;
  var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var mqFold = w.matchMedia('(max-width:900px)');
  var mqDir  = w.matchMedia('(max-width:720px)');

  function fa(s){ return String(s).replace(/[0-9]/g,function(x){return '۰۱۲۳۴۵۶۷۸۹'[x];}); }
  function grp(n){ return String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g,'٬'); }
  function faNum(n){ return fa(grp(n)); }
  function esc(s){ return String(s).replace(/[&<>]/g,function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;'}[c]; }); }

  /* ═════════════════════════════════════════════════════════════════════
     BIN → issuing bank. Public six-digit prefixes of Shetab member banks,
     used only to name and tint the issuing bank in the two card services.
     ⚠ اگر ترجیح می‌دهید نام بانک نمایش داده نشود، کافی است BANKS را خالی
       بگذارید؛ در آن صورت برچسب عمومی «بانک عضو شتاب» نمایش داده می‌شود.
     ═════════════════════════════════════════════════════════════════════ */
  var BANKS = {
    '603799':{n:'بانک ملی ایران',  c:'#1B4D8F', s:'ملی', code:'017'},
    '610433':{n:'بانک ملت',        c:'#B8123A', s:'ملت', code:'012'},
    '627353':{n:'بانک تجارت',      c:'#1E6BB8', s:'تجارت', code:'018'},
    '589210':{n:'بانک سپه',        c:'#0E4C8A', s:'سپه', code:'015'},
    '627961':{n:'بانک صنعت و معدن',c:'#1F7A5A', s:'ص.م', code:'011'},
    '627412':{n:'بانک اقتصاد نوین',c:'#5B2E86', s:'نوین', code:'055'},
    '622106':{n:'بانک پارسیان',    c:'#7A1F3D', s:'پارسیان', code:'054'},
    '502229':{n:'بانک پاسارگاد',   c:'#C08A16', s:'پاسارگاد', code:'057'},
    '621986':{n:'بانک سامان',      c:'#1B7FA8', s:'سامان', code:'056'},
    '636214':{n:'بانک آینده',      c:'#2C7A4B', s:'آینده', code:'062'},
    '502938':{n:'بانک دی',         c:'#0F5F7A', s:'دی', code:'066'},
    '505801':{n:'بانک کوثر',       c:'#8A5A12', s:'کوثر', code:'073'}
  };
  var GENERIC = {n:'بانک عضو شتاب', c:'#3B4F7A', s:'شتاب', code:'000'};
  function bankOf(card){
    var six = String(card||'').replace(/\D/g,'').slice(0,6);
    return BANKS[six] || GENERIC;
  }
  /* a structurally valid IR IBAN for the demo output: IR + mod-97 check +
     3-digit bank code + 0 + 18-digit account, derived from the card number so
     the same card always yields the same sample شبا */
  function sampleIban(card){
    var b = bankOf(card), digits = String(card||'').replace(/\D/g,'');
    var acc = (digits.slice(6) + '0000000000000000').slice(0,18);
    var bban = b.code + '0' + acc;
    var rearranged = bban + '1827' + '00';      /* IR → 18 27, then 00 */
    var rem = 0;
    for(var i=0;i<rearranged.length;i++) rem = (rem*10 + (+rearranged[i])) % 97;
    var chk = 98 - rem; if(chk<10) chk = '0'+chk;
    return 'IR' + chk + bban;
  }

  /* ═════════════════════════════════════════════════════════════════════
     1. THE SERVICE MODEL FOR THE CONSOLE
     Each entry declares what the panel shows for that service: its fields,
     its quick-test buttons and the response it returns. `run` is given the
     current field values and the key of the button that was pressed.
     ═════════════════════════════════════════════════════════════════════ */
  var CONSOLE = {
    /* ── ۱. احراز هویت تصویری ─────────────────────────────────────────── */
    ekyc:{
      name:'شبیه‌ساز تشخیص چهره و زنده بودن <span class="lat">(Live Test)</span>',
      sub:'دو سناریوی آماده: یک ویدیوی سلفی واقعی و یک تلاش جعل با عکس.',
      ep:'eKYC · Liveness + Face Verification',
      go:'/api/', goName:'صفحه وب‌سرویس احراز هویت تصویری',
      stage:'face',
      fields:[{id:'nid',label:'کد ملی',ph:'۰۰۷۹۱۸۴۶۵۲',max:10,val:'0079184652'},
              {id:'bd', label:'تاریخ تولد',ph:'۱۳۷۰/۰۵/۱۷',max:10,val:'1370/05/17'}],
      tests:[{k:'pass', tone:'ok', label:'تست تصویر واقعی (Liveness Pass)'},
             {k:'spoof',tone:'bad',label:'تست تصویر جعلی (Spoof Attack)'}],
      run:'pass',
      runLabel:'اجرای تست زنده بودن تصویر',
      exec:function(v,k){
        if(k==='spoof') return {
          tone:'bad', face:'fail', faceTx:'SPOOF DETECTED',
          badge:{cls:'bad',text:'رد شد'},
          title:'عدم تایید زنده بودن تصویر',
          sub:'تصویر ارسالی زنده تشخیص داده نشد — <span class="lat">Photo Attack Detected</span>. '+
              'ویدیوی دریافتی با یک عکس چاپی یا نمایش تصویر روی نمایشگر ساخته شده است.',
          lat:'۰٫۹ ثانیه',
          rows:[{k:'is_live',v:'false',cls:'num'},
                {k:'نوع تقلب شناسایی‌شده',v:'Photo Attack',cls:'num'},
                {k:'درصد تطابق چهره',v:'محاسبه نشد',cls:'mut'},
                {k:'زمان پردازش',v:'۰٫۹ ثانیه'}],
          json:{is_live:false, spoof_type:"PHOTO_ATTACK",
                message:"Photo Attack Detected", face_match_score:null,
                processing_time:"0.9s", verified:false}
        };
        return {
          tone:'ok', face:'pass', faceTx:'LIVE ✓',
          badge:{cls:'ok',text:'تایید شد'},
          title:'زنده بودن تصویر و تطابق چهره تایید شد',
          sub:'ویدیوی سلفی زنده تشخیص داده شد و چهره کاربر با تصویر مرجع پایگاه ثبت احوال '+
              'تطبیق داده شد.',
          lat:'۱٫۲ ثانیه',
          rows:[{k:'is_live',v:'true',cls:'num'},
                {k:'درصد تطابق چهره',v:'۹۸٫۵٪',cls:'num'},
                {k:'زمان پردازش',v:'۱٫۲ ثانیه'},
                {k:'تطبیق با تصویر مرجع',v:'تایید'}],
          json:{is_live:true, face_match_score:98.5, processing_time:"1.2s",
                reference:"CIVIL_REGISTRY", verified:true}
        };
      }
    },

    /* ── ۲. شاهکار ────────────────────────────────────────────────────── */
    shahkar:{
      name:'شبیه‌ساز استعلام مالکیت سیم‌کارت',
      sub:'شماره موبایل و کد ملی را وارد کنید یا یکی از دو سناریوی نمونه را بزنید.',
      ep:'Shahkar · SIM Ownership Match',
      go:'/api-shahkar/', goName:'صفحه وب‌سرویس شاهکار',
      fields:[{id:'mob',label:'شماره موبایل',ph:'۰۹۱۲۱۲۳۴۵۶۷',max:11,val:'09121234567'},
              {id:'nid',label:'کد ملی',ph:'۰۰۷۹۱۸۴۶۵۲',max:10,val:'0079184652'}],
      tests:[{k:'match',   tone:'ok',  label:'نمونه مالکیت تاییدشده'},
             {k:'mismatch',tone:'bad', label:'نمونه عدم تطابق'}],
      runLabel:'استعلام مالکیت',
      fill:{match:{mob:'09121234567',nid:'0079184652'},
            mismatch:{mob:'09354448812',nid:'0079184652'}},
      exec:function(v,k){
        var ok = (k==='match') || (k===null && v.mob==='09121234567' && v.nid==='0079184652');
        if(!ok) return {
          tone:'warn', badge:{cls:'bad',text:'Mismatched'},
          title:'شماره موبایل به این کد ملی تعلق ندارد',
          sub:'سیم‌کارت واردشده به نام صاحب این کد ملی ثبت نشده است. در سناریوی واقعی، ثبت‌نام '+
              'در این نقطه متوقف می‌شود.',
          lat:'۳۱۰ میلی‌ثانیه',
          rows:[{k:'isMatched',v:'false',cls:'num'},
                {k:'شماره موبایل ارسالی',v:fa(v.mob||'—'),cls:'num'},
                {k:'کد ملی ارسالی',v:fa(v.nid||'—'),cls:'num'},
                {k:'وضعیت',v:'Mismatched',cls:'num'}],
          json:{isMatched:false, mobile:v.mob||"", nationalId:v.nid||"",
                status:"MISMATCHED", message:"شماره موبایل به این کد ملی تعلق ندارد"}
        };
        return {
          tone:'ok', badge:{cls:'ok',text:'Matched'},
          title:'مالکیت سیم‌کارت تایید شد',
          sub:'شماره موبایل واردشده به نام صاحب همین کد ملی ثبت شده است.',
          lat:'۲۹۰ میلی‌ثانیه',
          rows:[{k:'isMatched',v:'true',cls:'num'},
                {k:'شماره موبایل ارسالی',v:fa(v.mob||'—'),cls:'num'},
                {k:'کد ملی ارسالی',v:fa(v.nid||'—'),cls:'num'},
                {k:'وضعیت',v:'Matched',cls:'num'}],
          json:{isMatched:true, mobile:v.mob||"", nationalId:v.nid||"",
                status:"MATCHED", message:"مالکیت سیم‌کارت تایید شد"}
        };
      }
    },

    /* ── ۳. ثبت احوال ─────────────────────────────────────────────────── */
    civil:{
      name:'پیش‌نمایش استعلام هویتی',
      sub:'با یک کلیک، فرم با داده نمونه پر می‌شود و پاسخ استعلام نمایش داده می‌شود.',
      ep:'Civil Registry · Identity Inquiry',
      go:'/api-inquiry-person/', goName:'صفحه وب‌سرویس ثبت احوال',
      fields:[{id:'nid',label:'کد ملی',ph:'کد ملی ۱۰ رقمی',max:10,val:''},
              {id:'bd', label:'تاریخ تولد',ph:'۱۳۷۰/۰۵/۱۷',max:10,val:''}],
      tests:[{k:'demo',tone:'neutral',label:'تست استعلام هویتی'}],
      runLabel:'تست استعلام هویتی',
      fill:{demo:{nid:'0079184652',bd:'1370/05/17'}},
      exec:function(v){
        if(!v.nid) { v = {nid:'0079184652', bd:'1370/05/17'}; }
        return {
          tone:'ok', badge:{cls:'ok',text:'استعلام موفق'},
          title:'اطلاعات هویتی تاییدشده',
          sub:'اطلاعات پایه مستقیماً از پایگاه داده ثبت احوال بازگردانده شد.',
          lat:'۳۴۰ میلی‌ثانیه',
          rows:[{k:'نام',v:'سارا'},
                {k:'نام خانوادگی',v:'موسوی‌نژاد'},
                {k:'نام پدر',v:'محمدرضا'},
                {k:'وضعیت حیات',v:'در قید حیات'},
                {k:'وضعیت شناسنامه',v:'معتبر'},
                {k:'شناسه پیگیری استعلام',v:'UID-4F27-9B31',cls:'num'}],
          json:{firstName:"سارا", lastName:"موسوی‌نژاد", fatherName:"محمدرضا",
                isAlive:true, idCardStatus:"VALID", trackId:"UID-4F27-9B31"}
        };
      }
    },

    /* ── ۴. استعلام شبا ───────────────────────────────────────────────── */
    iban:{
      name:'شبیه‌ساز استعلام حساب بانکی',
      sub:'شماره شبا را وارد کنید و یکی از دو وضعیت نمونه را انتخاب کنید.',
      ep:'IBAN Inquiry · Account Status',
      go:'/api-inquiry-iban/', goName:'صفحه وب‌سرویس استعلام شبا',
      fields:[{id:'iban',label:'شماره شبا',ph:'IR۰۶۰۱۲۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰',max:26,
               val:'IR060120000000000000000000',wide:true}],
      tests:[{k:'active',  tone:'ok', label:'حساب فعال و معتبر'},
             {k:'blocked', tone:'bad',label:'حساب مسدود / غیرفعال'}],
      runLabel:'استعلام وضعیت حساب',
      run:'active',
      exec:function(v,k){
        var ib = (v.iban||'IR060120000000000000000000').toUpperCase();
        if(k==='blocked') return {
          tone:'bad', badge:{cls:'bad',text:'INACTIVE'},
          title:'حساب مسدود / غیرفعال است',
          sub:'واریز به این حساب برگشت می‌خورد. در سناریوی واقعی، تسویه پیش از انجام تراکنش '+
              'متوقف می‌شود.',
          lat:'۳۸۰ میلی‌ثانیه',
          rows:[{k:'status',v:'INACTIVE / BLOCKED',cls:'num'},
                {k:'شماره شبا',v:ib,cls:'num'},
                {k:'نام صاحب حساب',v:'بازگردانده نشد',cls:'mut'},
                {k:'بانک صادرکننده',v:'بانک ملت'}],
          json:{status:"INACTIVE", blocked:true, iban:ib, owners:[],
                bank:"بانک ملت", message:"حساب مسدود یا غیرفعال است"}
        };
        return {
          tone:'ok', badge:{cls:'ok',text:'ACTIVE'},
          title:'حساب فعال و معتبر است',
          sub:'وضعیت حساب، نام صاحب حساب و بانک صادرکننده در لحظه بازگردانده شد.',
          lat:'۳۶۰ میلی‌ثانیه',
          rows:[{k:'status',v:'ACTIVE',cls:'num'},
                {k:'شماره شبا',v:ib,cls:'num'},
                {k:'نام صاحب حساب',v:'سارا موسوی‌نژاد'},
                {k:'بانک صادرکننده',v:'بانک ملت'}],
          json:{status:"ACTIVE", blocked:false, iban:ib,
                owners:["سارا موسوی‌نژاد"], bank:"بانک ملت"}
        };
      }
    },

    /* ── ۵. کد پستی ───────────────────────────────────────────────────── */
    postal:{
      name:'شبیه‌ساز دریافت خودکار آدرس',
      sub:'کد پستی ۱۰ رقمی را وارد کنید تا فیلدهای نشانی پر شود.',
      ep:'Postal Code · Address Lookup',
      go:'/address-postcode-docs/', goName:'صفحه وب‌سرویس استعلام کد پستی',
      fields:[{id:'pc',label:'کد پستی',ph:'۱۹۶۸۶۵۳۷۶۱',max:10,val:'1968653761',wide:true}],
      tests:[{k:'demo',tone:'neutral',label:'پر کردن با کد پستی نمونه'}],
      runLabel:'دریافت آدرس',
      fill:{demo:{pc:'1968653761'}},
      exec:function(v){
        var pc = (v.pc||'').replace(/\D/g,'') || '1968653761';
        return {
          tone:'ok', badge:{cls:'ok',text:'نشانی یافت شد'},
          title:'نشانی متناظر با این کد پستی دریافت شد',
          sub:'فیلدهای نشانی به‌صورت تفکیک‌شده بازگردانده می‌شود و می‌تواند مستقیماً فرم شما را پر کند.',
          lat:'۳۰۰ میلی‌ثانیه',
          rows:[{k:'استان',v:'تهران'},
                {k:'شهر',v:'تهران'},
                {k:'خیابان',v:'ولی‌عصر — کوچه شهید نجفی'},
                {k:'پلاک و واحد',v:'۲۷ — واحد ۴',cls:'num'}],
          addr:{k:'نشانی یکپارچه', v:'تهران، تهران، خیابان ولی‌عصر، کوچه شهید نجفی، پلاک ۲۷، واحد ۴ — کد پستی '+fa(pc)},
          json:{postalCode:pc, province:"تهران", city:"تهران",
                street:"ولی‌عصر", alley:"کوچه شهید نجفی", no:"27", unit:"4",
                address:"تهران، تهران، خیابان ولی‌عصر، کوچه شهید نجفی، پلاک ۲۷، واحد ۴"}
        };
      }
    },

    /* ── ۶. تبدیل کارت به شبا ─────────────────────────────────────────── */
    card2iban:{
      name:'شبیه‌ساز تبدیل آنی کارت به شبا',
      sub:'شماره کارت ۱۶ رقمی را وارد کنید؛ به‌محض کامل شدن، شبا و بانک صادرکننده ظاهر می‌شود.',
      ep:'Card → IBAN Conversion',
      go:'/api-inquiry-card/', goName:'صفحه وب‌سرویس تبدیل کارت به شبا',
      fields:[{id:'card',label:'شماره کارت',ph:'۶۱۰۴ ۳۳۷۷ ۱۲۳۴ ۵۶۷۸',max:19,
               val:'6104337712345678',wide:true,live:true}],
      tests:[{k:'melli',tone:'neutral',label:'نمونه کارت بانک ملی'},
             {k:'mellat',tone:'neutral',label:'نمونه کارت بانک ملت'}],
      runLabel:'تبدیل به شبا',
      fill:{melli:{card:'6037997412345678'}, mellat:{card:'6104337712345678'}},
      exec:function(v){
        var raw = String(v.card||'').replace(/\D/g,'');
        if(raw.length<16) return {
          tone:'warn', badge:{cls:'busy',text:'ورودی ناقص'},
          title:'شماره کارت کامل نیست',
          sub:'برای تبدیل، شماره کارت باید هر ۱۶ رقم را داشته باشد.',
          lat:'—',
          rows:[{k:'ارقام واردشده',v:fa(raw.length)+' از ۱۶',cls:'num'}],
          json:{error:"INVALID_CARD_LENGTH", digits:raw.length}
        };
        var b = bankOf(raw), ib = sampleIban(raw);
        return {
          tone:'ok', badge:{cls:'ok',text:'تبدیل شد'},
          title:'شماره شبای متصل به این کارت',
          sub:'کاربر برای پیدا کردن شبا از سامانه شما خارج نمی‌شود.',
          lat:'۲۷۰ میلی‌ثانیه',
          mark:b,
          rows:[{k:'IBAN',v:ib,cls:'num'},
                {k:'بانک صادرکننده',v:b.n},
                {k:'شماره کارت',v:fa(raw.replace(/(\d{4})(?=\d)/g,'$1 ')),cls:'num'},
                {k:'BIN',v:raw.slice(0,6),cls:'num'}],
          json:{cardNumber:raw, iban:ib, bank:b.n, bin:raw.slice(0,6)}
        };
      }
    },

    /* ── ۷. تطبیق کد ملی و شبا ───────────────────────────────────────── */
    ibanval:{
      name:'شبیه‌ساز اعتبارسنجی مالکیت شبا',
      sub:'دو سناریوی آماده: تطابق موفق و عدم تطابق.',
      ep:'IBAN Ownership · Matched / Mismatched',
      go:'/api-validate-iban/', goName:'صفحه وب‌سرویس تطبیق شبا و کد ملی',
      fields:[{id:'iban',label:'شماره شبا',ph:'IR۰۶۰۱۲۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰۰',max:26,
               val:'IR060120000000000000000000',wide:true},
              {id:'nid',label:'کد ملی',ph:'۰۰۷۹۱۸۴۶۵۲',max:10,val:'0079184652'},
              {id:'bd', label:'تاریخ تولد',ph:'۱۳۷۰/۰۵/۱۷',max:10,val:'1370/05/17'}],
      tests:[{k:'match',   tone:'ok', label:'تطابق موفق'},
             {k:'mismatch',tone:'bad',label:'تست عدم تطابق'}],
      runLabel:'اجرای اعتبارسنجی مالکیت',
      run:'match',
      fill:{mismatch:{nid:'1287456390'}, match:{nid:'0079184652'}},
      exec:function(v,k){
        if(k==='mismatch') return {
          tone:'bad', badge:{cls:'bad',text:'Mismatched'},
          title:'شبا به کد ملی واردشده تعلق ندارد',
          sub:'این همان حالتی است که اجاره حساب بانکی و واریز اشتباه را متوقف می‌کند.',
          lat:'۳۲۰ میلی‌ثانیه',
          rows:[{k:'isMatched',v:'false',cls:'num'},
                {k:'وضعیت',v:'Mismatched',cls:'num'},
                {k:'شماره شبا',v:(v.iban||'—'),cls:'num'},
                {k:'کد ملی ارسالی',v:fa(v.nid||'—'),cls:'num'}],
          json:{isMatched:false, status:"MISMATCHED", iban:v.iban||"",
                nationalId:v.nid||"", message:"شبا به کد ملی واردشده تعلق ندارد"}
        };
        return {
          tone:'ok', badge:{cls:'ok',text:'Matched'},
          title:'مالکیت شبا تایید شد',
          sub:'شماره شبا به همین کد ملی و تاریخ تولد تعلق دارد.',
          lat:'۳۱۰ میلی‌ثانیه',
          rows:[{k:'isMatched',v:'true',cls:'num'},
                {k:'وضعیت',v:'Matched',cls:'num'},
                {k:'شماره شبا',v:(v.iban||'—'),cls:'num'},
                {k:'کد ملی ارسالی',v:fa(v.nid||'—'),cls:'num'}],
          json:{isMatched:true, status:"MATCHED", iban:v.iban||"",
                nationalId:v.nid||"", message:"مالکیت شبا تایید شد"}
        };
      }
    },

    /* ── ۸. تطبیق شماره کارت با کد ملی ───────────────────────────────── */
    cardval:{
      name:'شبیه‌ساز صحت‌سنجی کارت بانکی',
      sub:'دو سناریوی آماده، به‌همراه نمایش نام بانک صادرکننده.',
      ep:'Card Ownership · Matched / Mismatched',
      go:'/api-validate-card/', goName:'صفحه وب‌سرویس تطبیق کارت و کد ملی',
      fields:[{id:'card',label:'شماره کارت',ph:'۶۱۰۴ ۳۳۷۷ ۱۲۳۴ ۵۶۷۸',max:19,
               val:'6104337712345678',wide:true},
              {id:'nid',label:'کد ملی',ph:'۰۰۷۹۱۸۴۶۵۲',max:10,val:'0079184652'},
              {id:'bd', label:'تاریخ تولد',ph:'۱۳۷۰/۰۵/۱۷',max:10,val:'1370/05/17'}],
      tests:[{k:'match',   tone:'ok', label:'کارت متعلق به فرد است'},
             {k:'mismatch',tone:'bad',label:'کارت متعلق به شخص دیگری است'}],
      runLabel:'اجرای تطبیق مالکیت کارت',
      run:'match',
      fill:{match:{nid:'0079184652'}, mismatch:{nid:'1287456390'}},
      exec:function(v,k){
        var b = bankOf(v.card);
        if(k==='mismatch') return {
          tone:'bad', badge:{cls:'bad',text:'Mismatched'},
          title:'کارت متعلق به شخص دیگری است',
          sub:'ثبت این کارت برای تراکنش مالی در سناریوی واقعی متوقف می‌شود.',
          lat:'۳۳۰ میلی‌ثانیه', mark:b,
          rows:[{k:'isMatched',v:'false',cls:'num'},
                {k:'بانک صادرکننده کارت',v:b.n},
                {k:'کد ملی ارسالی',v:fa(v.nid||'—'),cls:'num'},
                {k:'وضعیت',v:'Mismatched',cls:'num'}],
          json:{isMatched:false, bank:b.n, nationalId:v.nid||"", status:"MISMATCHED"}
        };
        return {
          tone:'ok', badge:{cls:'ok',text:'Matched'},
          title:'کارت متعلق به همین شخص است',
          sub:'مالکیت کارت بانکی نسبت به کد ملی و تاریخ تولد تایید شد.',
          lat:'۳۲۰ میلی‌ثانیه', mark:b,
          rows:[{k:'isMatched',v:'true',cls:'num'},
                {k:'بانک صادرکننده کارت',v:b.n},
                {k:'کد ملی ارسالی',v:fa(v.nid||'—'),cls:'num'},
                {k:'وضعیت',v:'Matched',cls:'num'}],
          json:{isMatched:true, bank:b.n, nationalId:v.nid||"", status:"MATCHED"}
        };
      }
    }
  };

  /* برچسب‌ها/مقادیر پیش‌فرض کنسول را از تنظیمات پیشخوان (در صورت وجود) روی داده بالا
     می‌نشاند — فقط رشته‌ها عوض می‌شوند، خودِ منطق exec دست‌نخورده می‌ماند. */
  (function applyConsoleOverrides(){
    var overrides = w.UID_WSH_CONSOLE_OVERRIDES;
    if(!overrides) return;
    function escAttr(s){
      return String(s).replace(/[&<>"']/g, function(c){
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
      });
    }
    Object.keys(overrides).forEach(function(key){
      var svc = CONSOLE[key], ov = overrides[key];
      if(!svc || !ov) return;
      if(ov.name) svc.name = escAttr(ov.name);
      if(ov.sub) svc.sub = escAttr(ov.sub);
      if(ov.runLabel) svc.runLabel = escAttr(ov.runLabel);
      if(ov.goName) svc.goName = escAttr(ov.goName);
      if(ov.fields) Object.keys(ov.fields).forEach(function(i){
        var f = svc.fields[i], fo = ov.fields[i];
        if(!f || !fo) return;
        if(fo.label) f.label = escAttr(fo.label);
        if(fo.ph) f.ph = escAttr(fo.ph);
        if(fo.val) f.val = escAttr(fo.val);
      });
      if(ov.tests) Object.keys(ov.tests).forEach(function(i){
        var t = svc.tests[i], to = ov.tests[i];
        if(!t || !to) return;
        if(to.label) t.label = escAttr(to.label);
      });
    });
  })();

  var ORDER = ['ekyc','shahkar','civil','iban','postal','card2iban','ibanval','cardval'];
  var SHORT = {ekyc:'احراز هویت تصویری', shahkar:'شاهکار', civil:'ثبت احوال',
               iban:'استعلام شبا', postal:'کد پستی', card2iban:'کارت به شبا',
               ibanval:'تطبیق شبا و کد ملی', cardval:'تطبیق کارت و کد ملی'};

  /* ═════════════════════════════════════════════════════════════════════
     THE CONSOLE
     ═════════════════════════════════════════════════════════════════════ */
  var rail   = d.getElementById('consRail');
  var elName = d.getElementById('consName'),  elSub   = d.getElementById('consSub');
  var elStage= d.getElementById('consStage'), elFlds  = d.getElementById('consFlds');
  var elTests= d.getElementById('consTests'), elRun   = d.getElementById('consRun');
  var elRunTx= d.getElementById('consRunTx'), elEp    = d.getElementById('consEp');
  var elBadge= d.getElementById('consBadge'), elCard  = d.getElementById('consCard');
  var elJson = d.getElementById('consJson'),  elGo    = d.getElementById('consGo');
  var elFootN= d.getElementById('consFootName');
  var active = 'ekyc', busy = false;

  var TICK='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>';
  var BANG='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 001.7 3h17a2 2 0 001.7-3L14.7 3.9a2 2 0 00-3.4 0z"/></svg>';
  var CROSS='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg>';

  if(rail){
    rail.innerHTML = ORDER.map(function(k,i){
      return '<button class="cons-chip'+(i===0?' on':'')+'" type="button" role="tab" '+
             'aria-selected="'+(i===0?'true':'false')+'" data-cons="'+k+'">'+
             '<span class="cn">'+fa(i+1)+'</span>'+SHORT[k]+'</button>';
    }).join('');
    rail.addEventListener('click', function(e){
      var b = e.target.closest('[data-cons]');
      if(b) select(b.dataset.cons);
    });
  }

  function jsonHtml(o){
    var s = JSON.stringify(o, null, 2);
    return esc(s)
      .replace(/&quot;/g,'"')
      .replace(/"([^"]+)":/g, '<span class="k">"$1"</span>:')
      .replace(/: (&quot;|")([^"]*)("|&quot;)/g, ': <span class="s">"$2"</span>')
      .replace(/: (true)/g, ': <span class="b-true">true</span>')
      .replace(/: (false)/g, ': <span class="b-false">false</span>')
      .replace(/: (-?\d+(\.\d+)?)/g, ': <span class="n">$1</span>');
  }

  function vals(){
    var o = {};
    elFlds.querySelectorAll('input[data-f]').forEach(function(i){ o[i.dataset.f]=i.value.trim(); });
    return o;
  }

  function select(k, scroll){
    var s = CONSOLE[k]; if(!s) return;
    active = k;
    rail.querySelectorAll('[data-cons]').forEach(function(b){
      var on = b.dataset.cons===k;
      b.classList.toggle('on', on);
      b.setAttribute('aria-selected', on?'true':'false');
      if(on && rail){
        var er=b.getBoundingClientRect(), rr=rail.getBoundingClientRect();
        if(er.left<rr.left || er.right>rr.right){
          rail.scrollTo({left:b.offsetLeft - rail.clientWidth/2 + b.offsetWidth/2,
                         behavior:reduce?'auto':'smooth'});
        }
      }
    });

    elName.innerHTML = s.name;
    elSub.textContent = s.sub;
    elEp.textContent = s.ep;
    elGo.setAttribute('href', s.go);
    elFootN.textContent = s.goName;
    elRunTx.textContent = s.runLabel;

    elStage.innerHTML = s.stage==='face'
      ? '<div class="facebox" id="faceBox"><div class="frame"><span class="mouth"></span></div>'+
        '<span class="scan"></span><span class="verdict">در انتظار اجرا</span></div>' : '';

    elFlds.innerHTML = s.fields.map(function(f){
      return '<div class="cons-fld'+(f.wide?' wide':'')+'">'+
        '<label for="cf_'+f.id+'">'+f.label+'</label>'+
        '<input id="cf_'+f.id+'" data-f="'+f.id+'" type="text" autocomplete="off" '+
        'inputmode="'+(f.id==='iban'?'text':'numeric')+'" maxlength="'+f.max+'" '+
        'placeholder="'+f.ph+'" value="'+(f.val||'')+'"></div>';
    }).join('');

    elTests.innerHTML = s.tests.map(function(t){
      return '<button class="cons-test '+t.tone+'" type="button" data-test="'+t.k+'">'+
             (t.tone==='ok'?TICK:t.tone==='bad'?BANG:'')+'<span>'+t.label+'</span></button>';
    }).join('');

    reset();
    if(scroll){
      var top = d.getElementById('console');
      if(w.__openFoldFor) w.__openFoldFor(top);
      w.scrollTo({top: top.getBoundingClientRect().top + w.scrollY - 70,
                  behavior:reduce?'auto':'smooth'});
    }
  }

  function reset(){
    elBadge.className='idbadge wait'; elBadge.textContent='در انتظار اجرا';
    elCard.innerHTML =
      '<div class="rescard"><div class="rc-hd">'+
        '<span class="rc-ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" '+
        'stroke-width="2" stroke-linecap="round" stroke-linejoin="round">'+
        '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>'+
        '<b class="rc-t">هنوز اجرا نشده است</b></div>'+
        '<p class="rc-sub">یکی از دکمه‌های سناریو را بزنید یا ورودی‌ها را تغییر دهید و '+
        'دکمه نارنجی را بزنید.</p></div>';
    elJson.innerHTML = jsonHtml({status:"idle"});
  }

  function paint(res){
    var mark = res.mark
      ? '<div class="bankmark"><i style="--bk:'+res.mark.c+'">'+res.mark.s.slice(0,3)+'</i>'+
        '<span style="font-size:12.5px;color:#B9C6E0">'+res.mark.n+'</span></div>' : '';
    var rows = (res.rows||[]).map(function(r,i){
      return '<div class="rc-row'+((res.rows.length%2 && i===res.rows.length-1)?' wide':'')+'">'+
             '<span class="k">'+r.k+'</span>'+
             '<span class="v '+(r.cls||'')+'">'+r.v+'</span></div>';
    }).join('');
    var addr = res.addr
      ? '<div class="rc-addr"><span class="k">'+res.addr.k+'</span>'+
        '<span class="v">'+res.addr.v+'</span></div>' : '';
    var ic = res.tone==='ok'?TICK:res.tone==='warn'?BANG:CROSS;

    elCard.innerHTML =
      '<div class="rescard '+res.tone+'">'+
        '<div class="rc-hd"><span class="rc-ic">'+ic+'</span>'+
        '<b class="rc-t">'+res.title+'</b>'+
        '<span class="rc-lat">'+(res.lat||'')+'</span></div>'+
        '<p class="rc-sub">'+res.sub+'</p>'+ mark +
        '<div class="rc-rows">'+rows+'</div>'+ addr +
      '</div>';
    elBadge.className = 'idbadge '+(res.badge.cls||'wait');
    elBadge.textContent = res.badge.text;
    elJson.innerHTML = jsonHtml(res.json);
  }

  function execute(k){
    if(busy) return;
    var s = CONSOLE[active];
    if(s.fill && s.fill[k]){
      Object.keys(s.fill[k]).forEach(function(id){
        var i = elFlds.querySelector('[data-f="'+id+'"]'); if(!i) return;
        var val = s.fill[k][id];
        /* the card field is displayed in 4-digit groups, so a filled sample
           has to arrive in the same shape the user would have typed */
        if(id==='card') val = val.replace(/\D/g,'').replace(/(\d{4})(?=\d)/g,'$1 ');
        i.value = val;
      });
    }
    busy = true;
    elTests.querySelectorAll('[data-test]').forEach(function(b){
      b.classList.toggle('on', b.dataset.test===k); });
    elBadge.className='idbadge busy'; elBadge.textContent='در حال پردازش…';
    var face = d.getElementById('faceBox');
    if(face){ face.className='facebox run'; face.querySelector('.verdict').textContent='در حال بررسی…'; }

    setTimeout(function(){
      var res = s.exec(vals(), k);
      paint(res);
      if(face && res.face){
        face.className = 'facebox '+res.face;
        face.querySelector('.verdict').textContent = res.faceTx;
      }
      busy = false;
    }, reduce ? 60 : (s.stage==='face' ? 1100 : 480));
  }

  if(elTests){
    elTests.addEventListener('click', function(e){
      var b = e.target.closest('[data-test]');
      if(b) execute(b.dataset.test);
    });
  }
  if(elRun){
    elRun.addEventListener('click', function(){
      var s = CONSOLE[active];
      execute(s.run || (s.tests.length===1 ? s.tests[0].k : null));
    });
  }
  if(elFlds){
    elFlds.addEventListener('input', function(e){
      var i = e.target;
      if(!i.dataset || !i.dataset.f) return;
      if(active==='card2iban' && i.dataset.f==='card'){
        var raw = i.value.replace(/\D/g,'').slice(0,16);
        i.value = raw.replace(/(\d{4})(?=\d)/g,'$1 ');
        if(raw.length===16) execute(null);
      }
    });
  }
  if(rail) select('ekyc');

  /* ═════════════════════════════════════════════════════════════════════
     2. THE ROUTER — reads the catalogue itself, so adding a service to the
     grid adds it to the router with no second list to maintain.
     ═════════════════════════════════════════════════════════════════════ */
  var cards = Array.prototype.slice.call(d.querySelectorAll('.cat-card'));
  var LABEL = {identity:'هویت شخص', mobile:'شماره موبایل', bank:'حساب بانکی',
               card:'کارت بانکی', address:'نشانی و کد پستی'};
  var WHY = {
    identity:{ekyc:'وقتی باید مطمئن شوید شخص، واقعاً همان صاحب کد ملی است',
              civil:'وقتی فقط به مشخصات هویتی پایه نیاز دارید',
              shahkar:'لایه اول: مالکیت سیم‌کارت شخص',
              ibanval:'وقتی هویت را به یک حساب بانکی گره می‌زنید',
              cardval:'وقتی هویت را به یک کارت بانکی گره می‌زنید'},
    mobile:{shahkar:'تنها سرویسی که مالکیت شماره موبایل را با کد ملی تطبیق می‌دهد'},
    bank:{iban:'وضعیت حساب و نام صاحب حساب پیش از واریز',
          ibanval:'تعلق شبا به یک کد ملی مشخص',
          card2iban:'وقتی کاربر فقط شماره کارت دارد'},
    card:{cardval:'مالکیت کارت پیش از ثبت برای تراکنش',
          card2iban:'استخراج شبای متصل به همان کارت'},
    address:{postal:'نشانی کامل از روی کد پستی ۱۰ رقمی'}
  };

  var rtOpts = d.getElementById('rtOpts'), rtBody = d.getElementById('rtBody'),
      rtCount= d.getElementById('rtCount');

  function routeTo(cat){
    var list = cards.filter(function(c){
      return (' '+c.dataset.cat+' ').indexOf(' '+cat+' ')>-1; });
    /* WHY decides the order and the one-line reason; anything not named there
       still appears, just without a reason line */
    var why = WHY[cat]||{};
    list.sort(function(a,b){
      var ka=Object.keys(why).indexOf(a.dataset.k), kb=Object.keys(why).indexOf(b.dataset.k);
      return (ka<0?99:ka)-(kb<0?99:kb);
    });
    list = list.slice(0,3);

    rtCount.textContent = fa(list.length);
    rtBody.innerHTML = list.map(function(c,i){
      var k = c.dataset.k;
      return '<div class="rt-row" data-k="'+k+'">'+
        '<span class="n">'+fa(i+1)+'</span>'+
        '<span class="tx"><b>'+(SHORT[k]||c.dataset.name)+'</b>'+
        '<span>'+(why[k]||c.dataset.name)+'</span></span>'+
        '<button class="rt-try" type="button" data-try="'+k+'">تست در کنسول</button>'+
        '<a class="rt-go" href="'+c.dataset.slug+'">صفحه سرویس'+
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" '+
        'stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/>'+
        '</svg></a></div>';
    }).join('');
    var rows = rtBody.querySelectorAll('.rt-row');
    rows.forEach(function(r,i){ setTimeout(function(){ r.classList.add('in'); }, reduce?0:i*80); });
    /* the answer also narrows the catalogue below, so the two never disagree */
    setCat(cat);
  }

  if(rtOpts){
    rtOpts.addEventListener('click', function(e){
      var b = e.target.closest('[data-rt]'); if(!b) return;
      rtOpts.querySelectorAll('[data-rt]').forEach(function(x){
        var on = x===b; x.classList.toggle('on', on);
        x.setAttribute('aria-selected', on?'true':'false');
      });
      routeTo(b.dataset.rt);
    });
  }

  /* ═════════════════════════════════════════════════════════════════════
     3. THE CATALOGUE — filter, search, and the phone directory accordion
     ═════════════════════════════════════════════════════════════════════ */
  var chips = d.getElementById('catChips'), qInput = d.getElementById('catQ'),
      count = d.getElementById('catCount'), empty = d.getElementById('catEmpty'),
      grid  = d.getElementById('catGrid');
  var curCat = 'all';

  function applyFilter(){
    var q = (qInput && qInput.value || '').trim().toLowerCase();
    var n = 0;
    cards.forEach(function(c){
      var okCat = curCat==='all' || (' '+c.dataset.cat+' ').indexOf(' '+curCat+' ')>-1;
      var okQ   = !q || c.dataset.q.toLowerCase().indexOf(q)>-1;
      var show  = okCat && okQ;
      c.hidden = !show;
      if(!show && c.classList.contains('open')) closeCard(c);
      if(show) n++;
    });
    if(count) count.textContent = fa(n)+' سرویس';
    if(empty) empty.classList.toggle('show', n===0);
  }
  function setCat(cat){
    curCat = cat;
    if(chips){
      chips.querySelectorAll('[data-cat]').forEach(function(b){
        var on = b.dataset.cat===cat;
        b.classList.toggle('on', on);
        b.setAttribute('aria-selected', on?'true':'false');
      });
    }
    applyFilter();
  }
  if(chips){
    chips.addEventListener('click', function(e){
      var b = e.target.closest('[data-cat]'); if(!b) return;
      setCat(b.dataset.cat);
      if(qInput) qInput.value='';
      applyFilter();
    });
  }
  if(qInput) qInput.addEventListener('input', applyFilter);
  var reset2 = d.getElementById('catReset');
  if(reset2) reset2.addEventListener('click', function(){
    if(qInput) qInput.value=''; setCat('all');
  });

  /* the phone directory: one open tile at a time, expanding in place */
  function openCard(c){
    var body = c.querySelector('.cat-body');
    cards.forEach(function(o){ if(o!==c && o.classList.contains('open')) closeCard(o, true); });
    c.classList.add('open');
    c.querySelector('.cat-tile').setAttribute('aria-expanded','true');
    if(!mqDir.matches) return;
    body.style.visibility='visible';
    body.style.height = body.scrollHeight+'px';
    var done = function(){
      body.removeEventListener('transitionend', done);
      if(c.classList.contains('open')) body.style.height='auto';
    };
    if(reduce){ body.style.height='auto'; } else { body.addEventListener('transitionend', done); }
  }
  function closeCard(c, instant){
    var body = c.querySelector('.cat-body');
    c.classList.remove('open');
    c.querySelector('.cat-tile').setAttribute('aria-expanded','false');
    if(!mqDir.matches){ body.style.height=''; body.style.visibility=''; return; }
    if(instant || reduce){ body.style.height='0px'; return; }
    body.style.height = body.scrollHeight+'px';
    void body.offsetHeight;
    body.style.height = '0px';
  }
  cards.forEach(function(c){
    c.querySelector('.cat-tile').addEventListener('click', function(){
      if(!mqDir.matches) return;
      if(c.classList.contains('open')) closeCard(c);
      else {
        openCard(c);
        requestAnimationFrame(function(){
          var t = c.getBoundingClientRect().top + w.scrollY - 96;
          if(c.getBoundingClientRect().top < 70) w.scrollTo({top:t, behavior:reduce?'auto':'smooth'});
        });
      }
    });
  });
  function syncDir(){
    cards.forEach(function(c){
      var body = c.querySelector('.cat-body');
      if(!mqDir.matches){ body.style.height=''; body.style.visibility=''; }
      else if(!c.classList.contains('open')){ body.style.height='0px'; }
    });
  }
  if(mqDir.addEventListener) mqDir.addEventListener('change', syncDir);
  else if(mqDir.addListener) mqDir.addListener(syncDir);
  syncDir();

  /* «تست در کنسول» from anywhere — catalogue card or router row */
  d.addEventListener('click', function(e){
    var b = e.target.closest('[data-try]');
    if(!b) return;
    e.preventDefault();
    select(b.dataset.try, true);
  });

  /* a request button that names its service pre-fills the form behind it */
  d.addEventListener('click', function(e){
    var b = e.target.closest('[data-ask]');
    if(!b) return;
    var t = d.getElementById('modalTitle');
    if(t) t.textContent = 'درخواست ' + b.dataset.ask;
    var hidden = d.getElementById('leadService');
    if(hidden) hidden.value = b.dataset.ask;
  });

  applyFilter();


  /* ═════════════════════════════════════════════════════════════════════
     CHAPTER FOLD SYSTEM + CHAPTER READER
     ─────────────────────────────────────────────────────────────────────
     The fold itself is the mechanism the earlier pages already ship: on
     ≤900px every [data-fold] section collapses behind a one-line chapter row.

     What is new here is the reader layer on top of it:
       · accordion — only one chapter is open at a time, so the contents list
         is never more than one tap away;
       · a sticky chapter bar carries the number, the title and a close button
         while the chapter is being read, so the visitor never loses position;
       · every chapter ends with prev / next controls and the page CTA, which
         turns ten section endings into ten conversion points instead of one;
       · a progress meter on the contents header ticks up as chapters are read.
     Desktop is untouched — none of this markup is visible above 900px.
     ═════════════════════════════════════════════════════════════════════ */
  var folds = Array.prototype.slice.call(d.querySelectorAll('[data-fold]'));
  var meter = d.getElementById('foldMeter');

  function paintMeter(){
    if(!meter) return;
    var read = folds.filter(function(s){ return s.classList.contains('read'); }).length;
    meter.style.width = (folds.length ? (read/folds.length)*100 : 0)+'%';
  }

  function chevron(dir){
    /* RTL: "next" points to the start of the line */
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" '+
           'stroke-linecap="round" stroke-linejoin="round"><path d="'+
           (dir==='next' ? 'M15 18l-6-6 6-6' : 'M9 18l6-6-6-6')+'"/></svg>';
  }

  folds.forEach(function(sec,idx){
    var body = sec.querySelector('.fold-body');
    if(!body) return;
    var title = sec.dataset.foldTitle || '';

    /* ---- the collapsed chapter row ---- */
    var btn = d.createElement('button');
    btn.className = 'fold-btn';
    btn.type = 'button';
    btn.setAttribute('aria-expanded','false');
    var bodyId = 'fold-body-'+(idx+1);
    body.id = bodyId;
    btn.setAttribute('aria-controls', bodyId);
    btn.innerHTML =
      '<span class="fnum">'+fa(idx+1)+'</span>'+
      '<span class="ftx"><b></b><span></span></span>'+
      (sec.dataset.foldMin ? '<span class="fmin">'+sec.dataset.foldMin+'</span>' : '')+
      (sec.hasAttribute('data-fold-hot') ? '<span class="fdot"></span>' : '')+
      '<svg class="fchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" '+
        'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">'+
        '<path d="M6 9l6 6 6-6"/></svg>';
    btn.querySelector('.ftx b').textContent = title;
    btn.querySelector('.ftx span').textContent = sec.dataset.foldTeaser || '';
    sec.insertBefore(btn, sec.firstChild);

    /* ---- the sticky reader bar ----
       It sits OUTSIDE .fold-body on purpose: .fold-body is overflow:hidden so
       that it can animate its height, and position:sticky does not work inside
       an overflow-hidden ancestor. */
    var sticky = d.createElement('div');
    sticky.className = 'chap-sticky';
    sticky.innerHTML =
      '<span class="cs-n">'+fa(idx+1)+' / '+fa(folds.length)+'</span>'+
      '<span class="cs-t"></span>'+
      '<button class="cs-x" type="button" aria-label="بستن این بخش">'+
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" '+
        'stroke-linecap="round"><path d="M18 6L6 18M6 6l12 12"/></svg></button>';
    sticky.querySelector('.cs-t').textContent = title;
    sec.insertBefore(sticky, body);
    sticky.querySelector('.cs-x').addEventListener('click', function(){
      sec._foldOpen(false);
      w.scrollTo({top: sec.getBoundingClientRect().top + w.scrollY - 108,
                  behavior: reduce?'auto':'smooth'});
    });

    /* ---- the chapter footer: where to go next, and how to convert ---- */
    var inner = sec.querySelector('.fold-inner');
    if(inner){
      var nav = d.createElement('div');
      nav.className = 'chap-nav';
      var prevTitle = idx>0 ? (folds[idx-1].dataset.foldTitle||'بخش قبلی') : '';
      var nextTitle = idx<folds.length-1 ? (folds[idx+1].dataset.foldTitle||'بخش بعدی') : '';
      nav.innerHTML =
        '<button class="cn-btn" type="button" data-cn="prev"'+(idx===0?' disabled':'')+'>'+
          chevron('prev')+'<span>'+(prevTitle||'ابتدای فهرست')+'</span></button>'+
        '<button class="cn-btn" type="button" data-cn="next"'+(idx===folds.length-1?' disabled':'')+'>'+
          '<span>'+(nextTitle||'پایان فهرست')+'</span>'+chevron('next')+'</button>'+
        '<button class="cn-btn cn-cta" type="button" data-open-modal>'+
          'درخواست وب‌سرویس یا دریافت کلید سندباکس</button>';
      inner.appendChild(nav);

      nav.querySelector('[data-cn="prev"]').addEventListener('click', function(){
        if(idx>0) openChapter(idx-1);
      });
      nav.querySelector('[data-cn="next"]').addEventListener('click', function(){
        if(idx<folds.length-1) openChapter(idx+1);
      });
    }

    sec._foldOpen = function(open, instant){
      if(!mqFold.matches) return;
      var isOpen = sec.classList.contains('open');
      if(open === isOpen) return;

      if(open){
        sec.classList.add('open','read');
        btn.setAttribute('aria-expanded','true');
        body.style.visibility = 'visible';
        paintMeter();
        if(instant || reduce){ body.style.height='auto'; afterOpen(); return; }
        body.style.height = body.scrollHeight + 'px';
        var done = function(){
          body.removeEventListener('transitionend', done);
          if(sec.classList.contains('open')) body.style.height='auto';
          afterOpen();
        };
        body.addEventListener('transitionend', done);
      } else {
        sec.classList.remove('open');
        btn.setAttribute('aria-expanded','false');
        if(instant || reduce){ body.style.height='0px'; return; }
        /* an explicit pixel height is needed before the browser will animate
           down from `auto` */
        body.style.height = body.scrollHeight + 'px';
        void body.offsetHeight;
        body.style.height = '0px';
      }
    };

    function afterOpen(){
      /* decks measure their tallest card while the cards are in normal flow.
         Inside a fold they were measured at height 0, so they need a second
         pass the moment the chapter actually opens. */
      if(w.__remeasureDecks) w.__remeasureDecks(sec);
      sec.querySelectorAll('.rv,.fnl').forEach(function(el){ el.classList.add('is-in'); });
    }

    btn.addEventListener('click', function(){
      if(sec.classList.contains('open')) sec._foldOpen(false);
      else openChapter(idx);
    });
  });

  /* accordion + scroll-into-place: this is what makes it read like a book */
  function openChapter(i, noScroll){
    var target = folds[i];
    if(!target || !target._foldOpen) return;
    folds.forEach(function(s,j){
      if(j!==i && s.classList.contains('open') && s._foldOpen) s._foldOpen(false, true);
    });
    target._foldOpen(true, true);
    paintMeter();
    if(noScroll) return;
    requestAnimationFrame(function(){
      requestAnimationFrame(function(){
        w.scrollTo({top: target.getBoundingClientRect().top + w.scrollY - 100,
                    behavior: reduce?'auto':'smooth'});
      });
    });
  }

  /* jumping to a folded chapter must open it first, or the visitor lands on a
     closed row and thinks the link is broken */
  w.__openFoldFor = function(target){
    if(!target || !mqFold.matches) return false;
    var sec = target.closest ? target.closest('[data-fold]') : null;
    if(!sec) sec = (target.hasAttribute && target.hasAttribute('data-fold')) ? target : null;
    if(sec && sec._foldOpen && !sec.classList.contains('open')){
      var i = folds.indexOf(sec);
      if(i>-1) openChapter(i, true); else sec._foldOpen(true, true);
      return true;
    }
    return false;
  };

  /* ── essentials filter ──────────────────────────────────────────────────
     Nine chapter rows is still nine rows. The four rows carrying the buying
     decision are already marked data-fold-hot for the orange dot, so the same
     attribute drives a filter that hides the other five outright. A visitor in
     a hurry gets a four-item contents list instead of a nine-item one. Any
     chapter that is open when the filter turns on is closed first, so the
     visitor is never left reading a section that has just been hidden. */
  var chapters  = d.getElementById('chapters');
  var filterBtn = d.getElementById('foldFilter');
  var fbTx      = d.querySelector('#foldbar .fb-tx');
  if(chapters && filterBtn && fbTx){
    var hotCount = folds.filter(function(s){ return s.hasAttribute('data-fold-hot'); }).length;
    var fullTx   = fbTx.innerHTML;
    var hotTx    = '<b>'+fa(hotCount)+'</b> بخش کلیدی — بقیه بخش‌ها موقتاً پنهان شده‌اند.';
    filterBtn.addEventListener('click', function(){
      var on = filterBtn.getAttribute('aria-pressed') !== 'true';
      if(on){
        folds.forEach(function(s){
          if(!s.hasAttribute('data-fold-hot') && s.classList.contains('open') && s._foldOpen){
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
  if(allBtn){
    allBtn.addEventListener('click', function(){
      var anyClosed = folds.some(function(s){ return !s.classList.contains('open'); });
      folds.forEach(function(s){
        if(s._foldOpen) s._foldOpen(anyClosed, true);
        if(anyClosed) s.classList.add('read');
      });
      paintMeter();
      allBtn.textContent = anyClosed ? 'بستن همه' : 'باز کردن همه';
    });
  }

  /* leaving mobile must hand the sections back to the desktop layout cleanly */
  function syncFold(){
    if(mqFold.matches){
      folds.forEach(function(s){
        var b=s.querySelector('.fold-body');
        if(b && !s.classList.contains('open')) b.style.height='0px';
      });
    } else {
      folds.forEach(function(s){
        var b=s.querySelector('.fold-body');
        if(b){ b.style.height=''; b.style.visibility=''; }
        s.classList.remove('open');
        var fb=s.querySelector('.fold-btn');
        if(fb) fb.setAttribute('aria-expanded','false');
      });
      if(allBtn) allBtn.textContent='باز کردن همه';
    }
  }
  if(mqFold.addEventListener) mqFold.addEventListener('change',syncFold);
  else if(mqFold.addListener) mqFold.addListener(syncFold);
  syncFold();
  paintMeter();

  /* the chapter footer CTA buttons are injected after the shared script bound
     [data-open-modal], so they need binding here */
  d.querySelectorAll('.chap-nav [data-open-modal]').forEach(function(b){
    b.addEventListener('click', function(e){
      e.preventDefault();
      var m=d.getElementById('modal');
      if(m){ m.dataset.open='1'; d.body.style.overflow='hidden'; }
    });
  });


  /* ═════════════════════════════════════════════════════════════════════
     30-SECOND CAPSULE — soft jump (works whether or not the target section
     is inside a collapsed chapter)
     ═════════════════════════════════════════════════════════════════════ */
  d.querySelectorAll('[data-jump-soft]').forEach(function(b){
    b.addEventListener('click', function(){
      var t = d.querySelector(b.dataset.jumpSoft);
      if(!t) return;
      if(w.__openFoldFor) w.__openFoldFor(t);
      w.scrollTo({top: t.getBoundingClientRect().top + w.scrollY - 76,
                  behavior: reduce?'auto':'smooth'});
    });
  });

})();
