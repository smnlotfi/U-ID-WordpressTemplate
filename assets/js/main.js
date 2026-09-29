  // Hero: live verification-flow widget cycling
  (function(){
    var states = document.querySelectorAll('.vf-state');
    var label = document.getElementById('vfStatusLabel');
    if(!states.length) return;
    var labels = ['در حال اسکن مدرک', 'در حال تحلیل چهره', 'هویت تایید شد'];
    var i = 0;
    setInterval(function(){
      i = (i + 1) % states.length;
      states.forEach(function(s){ s.classList.remove('active'); });
      states[i].classList.add('active');
      if(label) label.textContent = labels[i];
    }, 2400);
  })();

  // Hero: live activity ticker rotation
  (function(){
    var el = document.getElementById('tickerText');
    if(!el) return;
    var events = [
      'کاربری در تهران هویت خود را تایید کرد',
      'کاربری در مشهد احراز هویت خود را تکمیل کرد',
      'کاربری در اصفهان استعلام شاهکار ثبت کرد',
      'کاربری در شیراز هویت خود را با موفقیت احراز کرد',
      'کاربری در تبریز احراز هویت ثنا را تکمیل کرد'
    ];
    var i = 0;
    setInterval(function(){
      i = (i + 1) % events.length;
      el.style.opacity = '0';
      setTimeout(function(){ el.textContent = events[i]; el.style.opacity = '1'; }, 300);
    }, 3200);
  })();

  // Reusable mobile carousel: builds dots and syncs them with scroll-snap position
  function initCarouselDots(trackSelector, dotsSelector){
    var track = document.querySelector(trackSelector);
    var dotsWrap = document.querySelector(dotsSelector);
    if(!track || !dotsWrap) return;
    var items = Array.prototype.slice.call(track.children);
    dotsWrap.innerHTML = '';
    items.forEach(function(_, i){
      var b = document.createElement('button');
      b.setAttribute('aria-label', 'رفتن به مورد ' + (i + 1));
      if(i === 0) b.classList.add('active');
      b.addEventListener('click', function(){
        items[i].scrollIntoView({behavior:'smooth', inline:'start', block:'nearest'});
      });
      dotsWrap.appendChild(b);
    });
    var dots = Array.prototype.slice.call(dotsWrap.children);
    var ticking = false;
    track.addEventListener('scroll', function(){
      if(ticking) return;
      ticking = true;
      requestAnimationFrame(function(){
        var trackRect = track.getBoundingClientRect();
        var closest = 0, minDist = Infinity;
        items.forEach(function(item, i){
          var r = item.getBoundingClientRect();
          var dist = Math.abs(r.left - trackRect.left);
          if(dist < minDist){ minDist = dist; closest = i; }
        });
        dots.forEach(function(d, i){ d.classList.toggle('active', i === closest); });
        ticking = false;
      });
    });
  }
  function setupCarousels(){
    initCarouselDots('#servicesGrid', '#servicesDots');
    initCarouselDots('#testimonialGrid', '#testimonialDots');
    initCarouselDots('#resourcesGrid', '#resourcesDots');
    initCarouselDots('#bentoGrid', '#bentoDots');
  }
  setupCarousels();
  window.addEventListener('resize', setupCarousels);

  // header scroll state + scroll progress bar
  var scrollProgress = document.getElementById('scrollProgress');
  (function(){
    var ticking = false;
    function onScroll(){
      document.body.classList.toggle('scrolled', window.scrollY > 10);
      if(scrollProgress){
        var docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        var pct = docHeight > 0 ? (window.scrollY / docHeight) * 100 : 0;
        scrollProgress.style.width = pct + '%';
      }
      ticking = false;
    }
    window.addEventListener('scroll', function(){
      if(!ticking){ ticking = true; requestAnimationFrame(onScroll); }
    }, {passive:true});
    onScroll();
  })();

  // magnetic nav blob (desktop only, tracks whichever top-level item is hovered/focused)
  (function(){
    var links = document.getElementById('navlinks'), blob = document.getElementById('navblob');
    if(!links || !blob) return;
    var parked = null;
    function moveBlob(el){
      if(!el) return;
      var pr = links.getBoundingClientRect(), er = el.getBoundingClientRect();
      blob.style.width = er.width + 'px';
      blob.style.transform = 'translateX(' + (er.left - pr.left) + 'px)';
      blob.style.opacity = '1';
    }
    function park(){ if(parked) moveBlob(parked); else blob.style.opacity = '0'; }
    links.querySelectorAll('.navlink').forEach(function(el){
      el.addEventListener('mouseenter', function(){ moveBlob(el); });
      el.addEventListener('focus', function(){ moveBlob(el); });
    });
    links.addEventListener('mouseleave', park);
    links.addEventListener('focusout', function(e){ if(!links.contains(e.relatedTarget)) park(); });
    parked = links.querySelector('.navlink.active');
    if(parked) requestAnimationFrame(function(){ moveBlob(parked); });
    window.addEventListener('resize', function(){
      blob.style.transition = 'none'; park();
      requestAnimationFrame(function(){ blob.style.transition = ''; });
    }, {passive:true});
  })();

  // desktop mega menu (hover opens, click toggles, closes on outside click/Escape)
  (function(){
    var mb = document.getElementById('megaBtn'), mg = document.getElementById('mega'), mgT;
    if(!mb || !mg) return;
    function megaSet(v){
      mg.dataset.open = v ? '1' : '0';
      mb.setAttribute('aria-expanded', v ? 'true' : 'false');
    }
    var byHover = false;
    mb.addEventListener('click', function(e){
      e.preventDefault(); e.stopPropagation();
      if(mg.dataset.open === '1' && byHover){ byHover = false; return; }
      megaSet(mg.dataset.open !== '1'); byHover = false;
    });
    [mb, mg].forEach(function(el){
      el.addEventListener('mouseenter', function(){
        clearTimeout(mgT);
        if(mg.dataset.open !== '1') byHover = true;
        megaSet(true);
      });
      el.addEventListener('mouseleave', function(){
        mgT = setTimeout(function(){ megaSet(false); byHover = false; }, 180);
      });
    });
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape'){ megaSet(false); document.body.classList.remove('mopen'); }
    });
    document.addEventListener('click', function(e){
      if(!mg.contains(e.target) && !mb.contains(e.target)) megaSet(false);
    });
  })();

  // mobile full-screen sheet (hamburger)
  (function(){
    var bg = document.getElementById('burger');
    if(!bg) return;
    function mToggle(){
      var open = !document.body.classList.contains('mopen');
      document.body.classList.toggle('mopen', open);
      bg.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    bg.addEventListener('click', mToggle);
    document.querySelectorAll('#msheet a').forEach(function(a){
      a.addEventListener('click', function(){
        document.body.classList.remove('mopen');
        bg.setAttribute('aria-expanded', 'false');
      });
    });
    var moreBtn = document.getElementById('mnavMore');
    if(moreBtn) moreBtn.addEventListener('click', mToggle);
  })();

  // footer link columns: <details> accordion on mobile, always-open on desktop
  // (matchMedia, not resize, so a column a reader opened on a phone stays open while they scroll)
  (function(){
    var mq = window.matchMedia('(min-width:721px)');
    function syncCols(e){
      document.querySelectorAll('footer .f-col').forEach(function(c){ c.open = e.matches; });
    }
    syncCols(mq);
    if(mq.addEventListener) mq.addEventListener('change', syncCols);
    else if(mq.addListener) mq.addListener(syncCols);
    document.querySelectorAll('footer .f-col > summary').forEach(function(s){
      s.addEventListener('click', function(e){ if(mq.matches) e.preventDefault(); });
    });
  })();

  // desktop sticky contact FAB (footer)
  (function(){
    var btn = document.getElementById('cfabBtn'), panel = document.getElementById('cfabPanel'), scrim = document.getElementById('cfabScrim');
    if(!btn || !panel) return;
    function set(open){
      btn.classList.toggle('is-open', open);
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      btn.setAttribute('aria-label', open ? 'بستن منوی تماس' : 'باز کردن منوی تماس');
      panel.classList.toggle('on', open);
      if(scrim) scrim.classList.toggle('on', open);
    }
    btn.addEventListener('click', function(e){ e.stopPropagation(); set(!panel.classList.contains('on')); });
    if(scrim) scrim.addEventListener('click', function(){ set(false); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') set(false); });
    document.addEventListener('click', function(e){
      if(!panel.contains(e.target) && !btn.contains(e.target)) set(false);
    });
    panel.querySelectorAll('a,button').forEach(function(el){
      el.addEventListener('click', function(){ set(false); });
    });
  })();

  // mobile bottom nav: raised button opens the contact sheet (phone / whatsapp / telegram / consult)
  (function(){
    var fab = document.getElementById('mnavFab'), sheet = document.getElementById('csheet'), scrim = document.getElementById('csheetScrim');
    if(!fab || !sheet) return;
    function set(open){
      fab.classList.toggle('is-open', open);
      fab.setAttribute('aria-expanded', open ? 'true' : 'false');
      fab.setAttribute('aria-label', open ? 'بستن راه‌های ارتباطی' : 'باز کردن راه‌های ارتباطی');
      sheet.classList.toggle('on', open);
      if(scrim) scrim.classList.toggle('on', open);
      document.body.classList.toggle('csheet-open', open);
    }
    fab.addEventListener('click', function(e){ e.stopPropagation(); set(!sheet.classList.contains('on')); });
    if(scrim) scrim.addEventListener('click', function(){ set(false); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') set(false); });
    sheet.querySelectorAll('a,button').forEach(function(el){
      el.addEventListener('click', function(){ set(false); });
    });
    // swipe the sheet down to dismiss
    var y0 = null;
    sheet.addEventListener('touchstart', function(e){
      if(sheet.scrollTop > 0) return;
      y0 = e.touches[0].clientY;
    }, {passive:true});
    sheet.addEventListener('touchmove', function(e){
      if(y0 === null) return;
      var dy = e.touches[0].clientY - y0;
      if(dy > 0) sheet.style.transform = 'translateY(' + dy + 'px)';
    }, {passive:true});
    sheet.addEventListener('touchend', function(e){
      if(y0 === null) return;
      var dy = e.changedTouches[0].clientY - y0;
      sheet.style.transform = '';
      if(dy > 70) set(false);
      y0 = null;
    }, {passive:true});
  })();


  // Persona tabs
  document.querySelectorAll('.tab-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
      var target = btn.getAttribute('data-tab');
      document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.remove('active'); });
      document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.remove('active'); });
      btn.classList.add('active');
      document.querySelector('.tab-panel[data-panel="'+target+'"]').classList.add('active');
    });
  });

  // Scroll reveal
  var revealEls = document.querySelectorAll('.reveal');
  if('IntersectionObserver' in window){
    var io = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, {threshold:.15, rootMargin:'0px 0px -40px 0px'});
    revealEls.forEach(function(el){ io.observe(el); });
  } else {
    revealEls.forEach(function(el){ el.classList.add('in'); });
  }

  // Back to top
  var backToTop = document.getElementById('backToTop');
  window.addEventListener('scroll', function(){
    if(window.scrollY > 700){ backToTop.classList.add('show'); } else { backToTop.classList.remove('show'); }
  });
  backToTop.addEventListener('click', function(){ window.scrollTo({top:0, behavior:'smooth'}); });

  // Lead / consult / demo modal open/close (shared, title+desc swap by trigger)
  (function(){
    var overlay = document.getElementById('leadModalOverlay');
    var titleEl = document.getElementById('leadModalTitle');
    var descEl = document.getElementById('leadModalDesc');
    var defaultTitle = titleEl.textContent, defaultDesc = descEl.textContent;
    function openModal(title, desc){
      titleEl.textContent = title || defaultTitle;
      descEl.textContent = desc || defaultDesc;
      overlay.classList.add('open');
      var lmfReset = document.getElementById('leadModalForm');
      if(lmfReset){
        lmfReset.reset();
        lmfReset.dataset.uidSubmitting = '';
        lmfReset.querySelectorAll('[data-req]').forEach(function(f){ f.style.outline = ''; });
        var f1 = lmfReset.querySelector('.lmf-fields'), o1 = lmfReset.querySelector('.lmf-ok');
        if(f1) f1.style.display = '';
        if(o1) o1.style.display = 'none';
      }
    }
    function closeModal(){ overlay.classList.remove('open'); }
    document.getElementById('leadModalClose').addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e){ if(e.target === overlay) closeModal(); });
    window.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeModal(); });

    // every "درخواست دمو" style CTA opens this modal instead of scrolling to #contact
    document.querySelectorAll('.js-demo-btn').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        openModal(btn.getAttribute('data-modal-title'), btn.getAttribute('data-modal-desc'));
      });
    });

    // every global "مشاوره رایگان" trigger (FAB, contact sheet, footer strip, mobile
    // sheet) opens this same modal. Deliberately a different attribute than
    // [data-open-modal], which is reserved for the per-page quick-request modal below.
    document.querySelectorAll('[data-open-lead-modal]').forEach(function(b){
      b.addEventListener('click', function(){ openModal(); });
    });

    // the modal's own form: validate + actually send the request to the server
    var lmf = document.getElementById('leadModalForm');
    if(lmf){
      var lmfFields = lmf.querySelector('.lmf-fields');
      var lmfOk = lmf.querySelector('.lmf-ok');
      var lmfBtn = lmf.querySelector('[data-submit]');
      function lmfValidate(){
        var ok = true;
        lmf.querySelectorAll('[data-req]').forEach(function(f){
          var bad = !f.value.trim();
          if(!bad && f.hasAttribute('data-tel')) bad = !/^09\d{9}$/.test(f.value.replace(/[^0-9]/g,''));
          f.style.outline = bad ? '2px solid #e5484d' : '';
          if(bad) ok = false;
        });
        return ok;
      }
      lmf.querySelectorAll('[data-req]').forEach(function(f){
        f.addEventListener('input', function(){ f.style.outline = ''; });
        f.addEventListener('change', function(){ f.style.outline = ''; });
      });
      if(lmfBtn) lmfBtn.addEventListener('click', function(){
        if(!lmfValidate()) return;
        window.uidSubmitForm(lmf, lmfBtn, function(){
          if(lmfFields) lmfFields.style.display = 'none';
          if(lmfOk) lmfOk.style.display = 'block';
          setTimeout(closeModal, 1800);
        });
      });
    }
  })();

  // Animated count-up stats
  function toPersianDigits(str){
    var en='0123456789', fa='۰۱۲۳۴۵۶۷۸۹';
    return String(str).replace(/[0-9]/g, function(d){ return fa[en.indexOf(d)]; });
  }
  function animateCount(el){
    var target = parseFloat(el.getAttribute('data-target'));
    var suffix = el.getAttribute('data-suffix') || '';
    var prefix = el.getAttribute('data-prefix') || '';
    var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
    var dur = 1500, start = null;
    function step(ts){
      if(!start) start = ts;
      var p = Math.min((ts - start) / dur, 1);
      var eased = 1 - Math.pow(1 - p, 3);
      var val = target * eased;
      var display = decimals > 0 ? val.toFixed(decimals) : Math.round(val).toLocaleString('en-US');
      el.textContent = prefix + toPersianDigits(display) + suffix;
      if(p < 1){ requestAnimationFrame(step); }
    }
    requestAnimationFrame(step);
  }
  var countEls = document.querySelectorAll('.count-up');
  if('IntersectionObserver' in window){
    var countIo = new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){ animateCount(entry.target); countIo.unobserve(entry.target); }
      });
    }, {threshold:.4});
    countEls.forEach(function(el){ countIo.observe(el); });
  } else {
    countEls.forEach(animateCount);
  }

  // Process carousel
  (function(){
    var slides = document.querySelectorAll('.process-slide');
    var dots = document.querySelectorAll('.process-dot');
    if(!slides.length) return;
    var current = 0, timer;
    function go(i){
      current = (i + slides.length) % slides.length;
      slides.forEach(function(s,idx){ s.classList.toggle('active', idx===current); });
      dots.forEach(function(d,idx){ d.classList.toggle('active', idx===current); });
    }
    function restart(){
      clearInterval(timer);
      timer = setInterval(function(){ go(current+1); }, 5000);
    }
    document.getElementById('processNext').addEventListener('click', function(){ go(current+1); restart(); });
    document.getElementById('processPrev').addEventListener('click', function(){ go(current-1); restart(); });
    dots.forEach(function(d){ d.addEventListener('click', function(){ go(parseInt(d.getAttribute('data-goto'))); restart(); }); });
    restart();
  })();

  // FAQ accordion
  document.querySelectorAll('.faq-item').forEach(function(item){
    var btn = item.querySelector('.faq-question');
    var answer = item.querySelector('.faq-answer');
    btn.addEventListener('click', function(){
      var isOpen = item.classList.contains('open');
      document.querySelectorAll('.faq-item.open').forEach(function(openItem){
        if(openItem !== item){
          openItem.classList.remove('open');
          openItem.querySelector('.faq-answer').style.maxHeight = null;
        }
      });
      if(isOpen){
        item.classList.remove('open');
        answer.style.maxHeight = null;
      } else {
        item.classList.add('open');
        answer.style.maxHeight = answer.scrollHeight + 'px';
      }
    });
  });

  // Per-page quick-request modal (#modal), same as the original static
  // reference pages: [data-open-modal] opens it, [data-close-modal]/Esc
  // closes it. Only runs on pages that actually render a #modal element.
  (function(){
    var modal = document.getElementById('modal');
    if(!modal) return;
    var lastFocus = null;
    function modalSet(open){
      modal.dataset.open = open ? '1' : '0';
      document.body.style.overflow = open ? 'hidden' : '';
      if(open){
        lastFocus = document.activeElement;
        setTimeout(function(){ var i = modal.querySelector('input'); if(i) i.focus(); }, 260);
      } else if(lastFocus && lastFocus.focus){
        lastFocus.focus();
      }
    }
    document.querySelectorAll('[data-open-modal]').forEach(function(b){
      b.addEventListener('click', function(e){ e.preventDefault(); modalSet(true); });
    });
    modal.querySelectorAll('[data-close-modal]').forEach(function(b){
      b.addEventListener('click', function(){ modalSet(false); });
    });
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape' && modal.dataset.open === '1') modalSet(false);
    });
  })();

  // Shared submit helper: sends any [data-submit] lead form to the server
  // (WordPress AJAX) and stores it so it shows up under
  // پیشخوان > تنظیمات قالب یوآیدی > درخواست‌های ارسالی. Falls back to the
  // old "fake success" behavior if the AJAX config wasn't localized.
  window.uidSubmitForm = function(form, btn, onSuccess, onError){
    if(!form || form.dataset.uidSubmitting === '1') return;
    var cfg = window.UID_FORM_CFG || {};
    function fail(){
      if(onError){ onError(); return; }
      window.alert('در ارسال درخواست خطایی رخ داد. لطفاً دوباره تلاش کنید یا با شماره تماس سایت تماس بگیرید.');
    }
    function done(success){
      form.dataset.uidSubmitting = '';
      if(btn) btn.disabled = false;
      if(success){ if(onSuccess) onSuccess(); else form.classList.add('is-sent'); }
      else { fail(); }
    }
    if(!cfg.ajaxUrl){ done(true); return; }

    form.dataset.uidSubmitting = '1';
    if(btn) btn.disabled = true;

    var fields = {};
    form.querySelectorAll('input[name], select[name], textarea[name]').forEach(function(el){
      if(el.type === 'submit' || el.type === 'button') return;
      fields[el.name] = el.value;
    });

    var body = new URLSearchParams();
    body.append('action', 'uid_submit_form');
    body.append('nonce', cfg.nonce || '');
    body.append('form_id', form.id || '');
    body.append('page_title', document.title || '');
    body.append('page_url', window.location.href);
    Object.keys(fields).forEach(function(k){ body.append('fields[' + k + ']', fields[k]); });

    fetch(cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
      .then(function(r){ return r.json(); })
      .then(function(res){ done(!!(res && res.success)); })
      .catch(function(){ done(false); });
  };
