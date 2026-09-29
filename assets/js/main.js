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

  // Mobile mega-menu accordion (hover doesn't fire on touch, so intercept the tap)
  (function(){
    var trigger = document.querySelector('.mega-trigger');
    var wrap = trigger ? trigger.closest('.nav-item-wrap') : null;
    if(!trigger || !wrap) return;
    trigger.addEventListener('click', function(e){
      if(window.innerWidth <= 900){
        e.preventDefault();
        wrap.classList.toggle('mega-open');
      }
    });
  })();

  document.getElementById('navToggle').addEventListener('click', function(){
    var nav = document.getElementById('mainNav');
    var isOpen = nav.classList.toggle('open');
    this.classList.toggle('is-open', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  });
  // close mobile menu when a link is clicked (except the mega-trigger, which only toggles its submenu)
  document.querySelectorAll('#mainNav a').forEach(function(a){
    if(a.classList.contains('mega-trigger')) return;
    a.addEventListener('click', function(){
      document.getElementById('mainNav').classList.remove('open');
      document.getElementById('navToggle').classList.remove('is-open');
      document.body.style.overflow = '';
    });
  });

  // header scroll state + scroll progress bar
  var siteHeader = document.getElementById('siteHeader');
  var scrollProgress = document.getElementById('scrollProgress');
  window.addEventListener('scroll', function(){
    siteHeader.classList.toggle('scrolled', window.scrollY > 10);
    var docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    var pct = docHeight > 0 ? (window.scrollY / docHeight) * 100 : 0;
    scrollProgress.style.width = pct + '%';
  });

  // magnetic nav highlight (desktop only, matches whichever top-level item is hovered)
  (function(){
    var nav = document.getElementById('mainNav');
    var highlight = document.getElementById('navHighlight');
    if(!nav || !highlight) return;
    var items = nav.querySelectorAll(':scope > a, :scope > .nav-item-wrap > a');
    items.forEach(function(item){
      item.addEventListener('mouseenter', function(){
        if(window.innerWidth <= 900) return;
        var navRect = nav.getBoundingClientRect();
        var itemRect = item.getBoundingClientRect();
        highlight.style.opacity = '1';
        highlight.style.left = (itemRect.left - navRect.left) + 'px';
        highlight.style.width = itemRect.width + 'px';
      });
    });
    nav.addEventListener('mouseleave', function(){ highlight.style.opacity = '0'; });
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

    // FAB cluster "درخواست مشاوره" item also opens this modal
    var fabConsult = document.getElementById('fabConsultBtn');
    if(fabConsult) fabConsult.addEventListener('click', function(){
      closeFabCluster();
      openModal('درخواست مشاوره از تیم یوآیدی', 'مشخصات خود را ثبت کنید تا در اولین فرصت با شما تماس بگیریم.');
    });

    // mobile contact-sheet "درخواست مشاوره" row
    var csConsult = document.getElementById('csConsult');
    if(csConsult) csConsult.addEventListener('click', function(){
      closeContactSheet();
      openModal('درخواست مشاوره از تیم یوآیدی', 'مشخصات خود را ثبت کنید تا در اولین فرصت با شما تماس بگیریم.');
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

  // Live chat modal (placeholder UI for the existing Raychat widget)
  var chatOverlay = document.getElementById('chatModalOverlay');
  function openChatModal(){ chatOverlay.classList.add('open'); }
  function closeChatModal(){ chatOverlay.classList.remove('open'); }
  document.getElementById('chatModalClose').addEventListener('click', closeChatModal);
  chatOverlay.addEventListener('click', function(e){ if(e.target === chatOverlay) closeChatModal(); });
  var fabChat = document.getElementById('fabChatBtn');
  if(fabChat) fabChat.addEventListener('click', function(){ closeFabCluster(); openChatModal(); });
  var csChat = document.getElementById('csChat');
  if(csChat) csChat.addEventListener('click', function(){ closeContactSheet(); openChatModal(); });

  // Desktop floating action cluster (expand/collapse)
  var fabToggleBtn = document.getElementById('leadFabBtn');
  var fabCluster = document.getElementById('fabCluster');
  function closeFabCluster(){
    if(fabCluster) fabCluster.classList.remove('open');
    if(fabToggleBtn){ fabToggleBtn.classList.remove('is-open'); fabToggleBtn.setAttribute('aria-expanded','false'); }
  }
  if(fabToggleBtn && fabCluster){
    fabToggleBtn.addEventListener('click', function(){
      var isOpen = fabCluster.classList.toggle('open');
      fabToggleBtn.classList.toggle('is-open', isOpen);
      fabToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    document.addEventListener('click', function(e){
      if(!fabCluster.contains(e.target) && e.target !== fabToggleBtn && !fabToggleBtn.contains(e.target)){
        closeFabCluster();
      }
    });
    window.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeFabCluster(); });
  }

  // Mobile bottom-nav "تماس" button opens the contact sheet (chat / consult / phone / telegram / whatsapp)
  var contactSheetOverlay = document.getElementById('contactSheetOverlay');
  function openContactSheet(){ if(contactSheetOverlay) contactSheetOverlay.classList.add('open'); }
  function closeContactSheet(){ if(contactSheetOverlay) contactSheetOverlay.classList.remove('open'); }
  var mnavLead = document.getElementById('mnavLead');
  if(mnavLead) mnavLead.addEventListener('click', openContactSheet);
  var csCancel = document.getElementById('csCancel');
  if(csCancel) csCancel.addEventListener('click', closeContactSheet);
  if(contactSheetOverlay) contactSheetOverlay.addEventListener('click', function(e){ if(e.target === contactSheetOverlay) closeContactSheet(); });

  // Bottom mobile navbar: scroll-to-section + "more" opens full menu + scrollspy
  (function(){
    var items = document.querySelectorAll('.mnav-item[data-target]');
    items.forEach(function(btn){
      btn.addEventListener('click', function(){
        var el = document.querySelector(btn.getAttribute('data-target'));
        if(el) el.scrollIntoView({behavior:'smooth'});
      });
    });
    var moreBtn = document.getElementById('mnavMore');
    if(moreBtn){
      moreBtn.addEventListener('click', function(){
        document.getElementById('mainNav').classList.add('open');
        document.getElementById('navToggle').classList.add('is-open');
        document.body.style.overflow = 'hidden';
      });
    }
    var sections = ['#hero','#services','#resources'].map(function(id){ return document.querySelector(id); }).filter(Boolean);
    if('IntersectionObserver' in window && sections.length){
      var io = new IntersectionObserver(function(entries){
        entries.forEach(function(entry){
          if(entry.isIntersecting){
            var id = '#' + entry.target.id;
            items.forEach(function(b){ b.classList.toggle('active', b.getAttribute('data-target') === id); });
          }
        });
      }, {rootMargin:'-45% 0px -45% 0px'});
      sections.forEach(function(s){ io.observe(s); });
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
