/**
 * رفتار تعاملی اختصاصی صفحه «مقاله OCR چیست؟» — فقط روی این قالب صفحه بارگذاری
 * می‌شود.
 * نکته: منوی هدر/مگامنو، شیت موبایل، مودال درخواست مشاوره (باز/بسته‌شدن #modal
 * از طریق [data-open-modal]/[data-close-modal])، دکمه‌های شناور تماس، بازگشت به
 * بالا و نوار پیشرفت اسکرول سراسری (#scrollProgress) از assets/js/main.js
 * می‌آیند — اینجا عمداً تکرار نشده‌اند. ارسال واقعی فرم‌ها هم از طریق
 * window.uidSubmitForm (تعریف‌شده در main.js) به همان اندپوینت AJAX مشترک سایت
 * می‌رود؛ اینجا فقط اعتبارسنجی سمت کاربر پیاده‌سازی شده.
 *
 * سیستم «فصل تاخوردنی» (fold) و نوار «پرش سریع» با حلقهٔ پیشرفت مطالعه، هر دو
 * مخصوص همین صفحهٔ مقاله‌ایِ بلندند (برخلاف صفحات خدمت/فروش) — دقیقاً همان سیستمی
 * که در assets/js/web-services-hub-page.js/glossary-page.js هست، بعلاوهٔ حلقهٔ
 * درصد مطالعه که تنها مخصوص این صفحه است.
 */
(function () {
	'use strict';
	var d = document, w = window;
	var $ = function (s, r) { return (r || d).querySelector(s); };
	var $$ = function (s, r) { return Array.prototype.slice.call((r || d).querySelectorAll(s)); };
	var root = $('.uid-ocr-pillar-article-page');
	if (!root) return;

	var reduce = w.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var FA = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
	function fa(s) { return String(s).replace(/[0-9]/g, function (x) { return FA[+x]; }); }

	/* ══════════════════════════════════════════════════════════════════════
	 * فرم‌ها — اعتبارسنجی درجا + ارسال از طریق window.uidSubmitForm
	 * ══════════════════════════════════════════════════════════════════════ */
	function validateForm(form) {
		var ok = true;
		$$('[data-req]', form).forEach(function (f) {
			var fld = f.closest('.fld');
			var bad = !f.value.trim();
			if (!bad && f.hasAttribute('data-tel')) {
				bad = !/^09\d{9}$/.test(f.value.replace(/[^0-9]/g, ''));
			}
			if (fld) fld.classList.toggle('bad', bad);
			if (bad) ok = false;
		});
		return ok;
	}
	$$('[data-submit]', root).forEach(function (btn) {
		btn.addEventListener('click', function () {
			var form = btn.closest('form');
			if (!form) return;
			if (validateForm(form)) {
				if (w.uidSubmitForm) {
					w.uidSubmitForm(form, btn);
				} else {
					form.classList.add('is-sent');
				}
			} else {
				var first = form.querySelector('.fld.bad input, .fld.bad select');
				if (first) first.focus();
			}
		});
	});
	$$('.fld input,.fld select', root).forEach(function (f) {
		f.addEventListener('input', function () { var fld = f.closest('.fld'); if (fld) fld.classList.remove('bad'); });
		f.addEventListener('change', function () { var fld = f.closest('.fld'); if (fld) fld.classList.remove('bad'); });
	});

	/* قابل‌روییت شدن فیلد «نوع کسب‌وکار = شخصی» در فرم لید: راهنمای سامانه ثنا */
	var routeSel = $('[data-route]', root), routeAlert = $('#routeAlert', root);
	if (routeSel && routeAlert) {
		routeSel.addEventListener('change', function () {
			routeAlert.classList.toggle('show', routeSel.value === 'ind');
		});
	}

	/* ══════════════════════════════════════════════════════════════════════
	 * آکاردئون سوالات متداول
	 * ══════════════════════════════════════════════════════════════════════ */
	$$('.faq-q', root).forEach(function (q) {
		q.addEventListener('click', function () {
			var item = q.parentNode, ans = item.querySelector('.faq-a');
			var open = item.classList.toggle('open');
			q.setAttribute('aria-expanded', open ? 'true' : 'false');
			if (ans) ans.style.maxHeight = open ? (ans.scrollHeight + 'px') : '0px';
		});
	});
	w.addEventListener('resize', function () {
		$$('.faq-i.open .faq-a', root).forEach(function (a) { a.style.maxHeight = a.scrollHeight + 'px'; });
	});

	/* ══════════════════════════════════════════════════════════════════════
	 * ظاهرشدن هنگام اسکرول (.rv)
	 * ══════════════════════════════════════════════════════════════════════ */
	var revealIo = 'IntersectionObserver' in w ? new IntersectionObserver(function (es) {
		es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-in'); revealIo.unobserve(en.target); } });
	}, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 }) : null;
	$$('.rv', root).forEach(function (el) {
		if (revealIo) revealIo.observe(el); else el.classList.add('is-in');
	});

	/* ══════════════════════════════════════════════════════════════════════
	 * پرش نرم به لنگرهای داخل صفحه (jumpbar/toc/tldr) — اگر لنگر داخل فصل
	 * تاخورده باشد، ابتدا همان فصل را باز می‌کند (window.__openFoldFor پایین‌تر
	 * تعریف می‌شود).
	 * ══════════════════════════════════════════════════════════════════════ */
	$$('a[href^="#"]', root).forEach(function (a) {
		a.addEventListener('click', function (e) {
			var id = a.getAttribute('href');
			if (!id || id.length < 2) return;
			var t = d.querySelector(id);
			if (!t) return;
			e.preventDefault();
			var opened = w.__openFoldFor ? w.__openFoldFor(t) : false;
			var go = function () {
				w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 90, behavior: reduce ? 'auto' : 'smooth' });
			};
			if (opened) requestAnimationFrame(function () { requestAnimationFrame(go); });
			else go();
		});
	});
	$$('[data-jump-soft]', root).forEach(function (b) {
		b.addEventListener('click', function () {
			var t = d.querySelector(b.getAttribute('data-jump-soft'));
			if (!t) return;
			w.scrollTo({ top: t.getBoundingClientRect().top + w.scrollY - 96, behavior: reduce ? 'auto' : 'smooth' });
		});
	});

	/* ══════════════════════════════════════════════════════════════════════
	 * نوار «پرش سریع» موبایل — چیپ‌ها + حلقهٔ درصد مطالعه
	 * ══════════════════════════════════════════════════════════════════════ */
	var jb = $('#jumpbar', root);
	if (jb) {
		var jchips = $$('[data-jump]', jb);
		var jtargets = jchips.map(function (c) { return d.querySelector(c.getAttribute('data-jump')); });
		var jskip = jchips.map(function (c) { return c.classList.contains('top'); });

		jchips.forEach(function (c) {
			c.addEventListener('click', function () {
				var t = d.querySelector(c.getAttribute('data-jump'));
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
			var active = -1;
			jtargets.forEach(function (t, idx) {
				if (!t || jskip[idx]) return;
				if (t.getBoundingClientRect().top <= w.innerHeight * 0.34) active = idx;
			});
			jchips.forEach(function (c, idx) { c.classList.toggle('on', idx === active); });
			if (active > -1) {
				var el = jchips[active], rail = $('#jumpRail', jb);
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

	/* حلقهٔ دایره‌ای درصد مطالعه، مستقل از نمایش/عدم‌نمایش نوار پرش */
	var ring = $('#jbRing', root), ringPct = $('#jbPct', root);
	if (ring && ringPct) {
		var C = 2 * Math.PI * 15.5, rTick = false;
		function paintRing() {
			var h = d.documentElement.scrollHeight - w.innerHeight;
			var p = h > 0 ? Math.min(Math.max(w.scrollY / h, 0), 1) : 0;
			ring.style.strokeDashoffset = (C * (1 - p)).toFixed(2);
			ringPct.textContent = fa(Math.round(p * 100));
			rTick = false;
		}
		ring.style.strokeDasharray = C.toFixed(2);
		w.addEventListener('scroll', function () { if (!rTick) { rTick = true; requestAnimationFrame(paintRing); } }, { passive: true });
		paintRing();
	}

	/* ══════════════════════════════════════════════════════════════════════
	 * سیستم «فصل تاخوردنی» موبایل — دقیقاً همان سیستم صفحات دیگر
	 * (web-services-hub-page.js/glossary-page.js)، با تفاوت اینکه فوتر هر فصل
	 * دکمه CTA فروش ندارد؛ فقط ناوبری قبلی/بعدی و یک راهنمای موقعیت.
	 * ══════════════════════════════════════════════════════════════════════ */
	var mqFold = w.matchMedia('(max-width:900px)');
	var folds = $$('[data-fold]', root);
	var meter = $('#foldMeter', root);

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
		var title = sec.getAttribute('data-fold-title') || '';

		var btn = d.createElement('button');
		btn.className = 'fold-btn';
		btn.type = 'button';
		btn.setAttribute('aria-expanded', 'false');
		var bodyId = 'op-fold-body-' + (idx + 1);
		body.id = bodyId;
		btn.setAttribute('aria-controls', bodyId);
		btn.innerHTML =
			'<span class="fnum">' + fa(idx + 1) + '</span>' +
			'<span class="ftx"><b></b><span></span></span>' +
			(sec.getAttribute('data-fold-min') ? '<span class="fmin">' + sec.getAttribute('data-fold-min') + '</span>' : '') +
			(sec.hasAttribute('data-fold-hot') ? '<span class="fdot"></span>' : '') +
			'<svg class="fchev" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
			'stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">' +
			'<path d="M6 9l6 6 6-6"/></svg>';
		btn.querySelector('.ftx b').textContent = title;
		btn.querySelector('.ftx span').textContent = sec.getAttribute('data-fold-teaser') || '';
		sec.insertBefore(btn, sec.firstChild);

		var sticky = d.createElement('div');
		sticky.className = 'chap-sticky';
		sticky.innerHTML =
			'<span class="cs-n">' + fa(idx + 1) + ' / ' + fa(folds.length) + '</span>' +
			'<span class="cs-t"></span>' +
			'<button class="cs-x" type="button" aria-label="' + 'بستن این بخش' + '">' +
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
			var prevTitle = idx > 0 ? (folds[idx - 1].getAttribute('data-fold-title') || 'بخش قبلی') : '';
			var nextTitle = idx < folds.length - 1 ? (folds[idx + 1].getAttribute('data-fold-title') || 'بخش بعدی') : '';
			nav.innerHTML =
				'<button class="cn-btn" type="button" data-cn="prev"' + (idx === 0 ? ' disabled' : '') + '>' +
				chevron('prev') + '<span>' + (prevTitle || 'ابتدای فهرست') + '</span></button>' +
				'<button class="cn-btn" type="button" data-cn="next"' + (idx === folds.length - 1 ? ' disabled' : '') + '>' +
				'<span>' + (nextTitle || 'پایان فهرست') + '</span>' + chevron('next') + '</button>' +
				'<span class="cn-hint">بخش <b>' + fa(idx + 1) + '</b> از <b>' + fa(folds.length) + '</b> — هر بخش مستقل است و لازم نیست همه را بخوانید.</span>';
			inner.appendChild(nav);

			nav.querySelector('[data-cn="prev"]').addEventListener('click', function () { if (idx > 0) openChapter(idx - 1); });
			nav.querySelector('[data-cn="next"]').addEventListener('click', function () { if (idx < folds.length - 1) openChapter(idx + 1); });
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
			sec.querySelectorAll('.rv').forEach(function (el) { el.classList.add('is-in'); });
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
		if (!sec && target.hasAttribute && target.hasAttribute('data-fold')) sec = target;
		if (sec && sec._foldOpen && !sec.classList.contains('open')) {
			var i = folds.indexOf(sec);
			if (i > -1) openChapter(i, true); else sec._foldOpen(true, true);
			return true;
		}
		return false;
	};

	/* فیلتر «فقط بخش‌های ضروری» */
	var chaptersWrap = $('#chapters', root);
	var filterBtn = $('#foldFilter', root);
	var fbTx = root.querySelector('#foldbar .fb-tx');
	if (chaptersWrap && filterBtn && fbTx) {
		var hotCount = folds.filter(function (s) { return s.hasAttribute('data-fold-hot'); }).length;
		var fullTx = fbTx.innerHTML;
		var hotTx = '<b>' + fa(hotCount) + '</b> بخش کلیدی — بقیه بخش‌ها موقتاً پنهان شده‌اند.';
		filterBtn.addEventListener('click', function () {
			var on = filterBtn.getAttribute('aria-pressed') !== 'true';
			if (on) {
				folds.forEach(function (s) {
					if (!s.hasAttribute('data-fold-hot') && s.classList.contains('open') && s._foldOpen) {
						s._foldOpen(false, true);
					}
				});
			}
			filterBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
			chaptersWrap.classList.toggle('only-hot', on);
			var lbl = filterBtn.querySelector('span');
			if (lbl) lbl.textContent = on ? 'نمایش همه بخش‌ها' : 'فقط بخش‌های ضروری';
			fbTx.innerHTML = on ? hotTx : fullTx;
		});
	}

	function syncFold() {
		if (mqFold.matches) {
			folds.forEach(function (s) {
				var b = s.querySelector('.fold-body');
				if (b && !s.classList.contains('open')) b.style.height = '0px';
			});
		} else {
			folds.forEach(function (s) {
				var b = s.querySelector('.fold-body');
				if (b) { b.style.height = ''; b.style.visibility = ''; }
				s.classList.remove('open');
				var fb = s.querySelector('.fold-btn');
				if (fb) fb.setAttribute('aria-expanded', 'false');
			});
		}
	}
	if (mqFold.addEventListener) mqFold.addEventListener('change', syncFold);
	else if (mqFold.addListener) mqFold.addListener(syncFold);
	syncFold();
	paintMeter();

	/* دکمه‌های CTA داخل فوتر فصل که بعد از رویداد سراسری [data-open-modal] در
	 * main.js تزریق شده‌اند، نیاز به اتصال دستی این‌جا دارند (اینجا هیچ دکمه‌ای
	 * از این نوع در chap-nav ساخته نمی‌شود، اما اگر در آینده اضافه شد همین‌جا
	 * کار می‌کند). */
	$$('.chap-nav [data-open-modal]', root).forEach(function (b) {
		b.addEventListener('click', function (e) {
			e.preventDefault();
			var m = d.getElementById('modal');
			if (m) { m.dataset.open = '1'; d.body.style.overflow = 'hidden'; }
		});
	});

	/* ══════════════════════════════════════════════════════════════════════
	 * ۱ — شبیه‌ساز شش‌مرحله‌ای OCR (فصل «چگونه کار می‌کند»)
	 * ══════════════════════════════════════════════════════════════════════ */
	var pipe = $('#pipe', root);
	if (pipe) {
		var steps = $$('.pipe-step', pipe);
		var psts = $$('.pst', pipe);
		var oEmpty = pipe.querySelector('.o-empty');
		var oRaw = pipe.querySelector('.o-raw');
		var oFix = pipe.querySelector('.o-fixed');
		var playBtn = $('#pipePlay', root);
		var resetBtn = $('#pipeReset', root);
		var cur = 1, timer = null;

		function setStep(n) {
			cur = Math.max(1, Math.min(6, n));
			pipe.dataset.st = String(cur);
			steps.forEach(function (b) {
				var i = parseInt(b.getAttribute('data-step'), 10);
				b.classList.toggle('on', i === cur);
				b.classList.toggle('done', i < cur);
				b.setAttribute('aria-selected', i === cur ? 'true' : 'false');
			});
			psts.forEach(function (p) { p.hidden = parseInt(p.getAttribute('data-i'), 10) !== cur; });
			if (oEmpty) oEmpty.hidden = cur >= 5;
			if (oRaw) oRaw.hidden = cur !== 5;
			if (oFix) oFix.hidden = cur !== 6;
		}
		function stopPlay() {
			if (timer) { clearInterval(timer); timer = null; }
			if (playBtn) {
				playBtn.classList.remove('on');
				var s = playBtn.querySelector('span'); if (s) s.textContent = 'پخش خودکار';
			}
		}
		steps.forEach(function (b) {
			b.addEventListener('click', function () { stopPlay(); setStep(parseInt(b.getAttribute('data-step'), 10)); });
		});
		if (playBtn) {
			playBtn.addEventListener('click', function () {
				if (timer) { stopPlay(); return; }
				if (cur >= 6) setStep(1);
				playBtn.classList.add('on');
				var s = playBtn.querySelector('span'); if (s) s.textContent = 'توقف';
				timer = setInterval(function () {
					if (cur >= 6) { stopPlay(); return; }
					setStep(cur + 1);
				}, reduce ? 1100 : 2000);
			});
		}
		if (resetBtn) resetBtn.addEventListener('click', function () { stopPlay(); setStep(1); });
		setStep(1);
	}

	/* ══════════════════════════════════════════════════════════════════════
	 * ۲ — شبیه‌ساز دقت (فصل «عوامل دقت») — یک نمایش آموزشی، نه اندازه‌گیری واقعی
	 * ══════════════════════════════════════════════════════════════════════ */
	var sim = $('#sim', root);
	if (sim) {
		var FACTORS = {
			blur: { w: 22, label: 'تصویر تار' },
			dark: { w: 14, label: 'نور کم' },
			skew: { w: 16, label: 'سند کج' },
			res: { w: 18, label: 'رزولوشن پایین' },
			bg: { w: 13, label: 'پس‌زمینه شلوغ' },
			hand: { w: 25, label: 'متن دست‌نویس' }
		};
		var CONF = {
			'ب': 'پتث', 'پ': 'بتث', 'ت': 'بپث', 'ث': 'بپت',
			'ج': 'چحخ', 'چ': 'جحخ', 'ح': 'جچخ', 'خ': 'جچح',
			'د': 'ذ', 'ذ': 'د', 'ر': 'زژ', 'ز': 'رژ', 'ژ': 'رز',
			'س': 'ش', 'ش': 'س', 'ص': 'ض', 'ض': 'ص', 'ط': 'ظ', 'ظ': 'ط',
			'ع': 'غ', 'غ': 'ع', 'ف': 'ق', 'ق': 'ف', 'ک': 'گ', 'گ': 'ک',
			'ی': 'ىي', 'ن': 'ت', 'ه': 'ة', 'م': 'ح',
			'۰': '۵', '۱': '۷', '۲': '۳', '۳': '۲', '۴': '۶', '۵': '۰', '۶': '۴', '۷': '۱', '۸': '۹', '۹': '۸',
			'0': 'O', '1': 'l', '2': 'Z', '5': 'S', '8': 'B', 'A': '4', 'O': '0', 'l': '1'
		};
		var SRC1 = 'کارتن بسته‌بندی سه‌لایه';
		var SRC2 = 'A-1250 / 120';

		var togs = $$('.tog', sim);
		var pctEl = $('#simPct', root);
		var barEl = $('#simBar', root);
		var tagEl = $('#simTag', root);
		var cardEl = $('#simCard', root);
		var pbEl = $('#simPb', root);
		var outEl = $('#simOut', root);
		var resetS = $('#simReset', root);
		var on = { blur: false, dark: false, skew: false, res: false, bg: false, hand: false };

		function hash(i, salt) {
			var x = Math.sin((i + 1) * 12.9898 + salt * 78.233) * 43758.5453;
			return x - Math.floor(x);
		}
		function escapeHtml(s) {
			return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		}
		function corrupt(txt, rate, salt, unk) {
			if (rate <= 0.01) return escapeHtml(txt);
			var out = '', i;
			for (i = 0; i < txt.length; i++) {
				var ch = txt[i];
				var h = hash(i, salt);
				if (ch === '‌') { out += (h < rate * 1.4) ? '' : ch; continue; }
				if (ch === ' ') { out += (h < rate * 0.5) ? '' : ch; continue; }
				if (h < rate && CONF[ch]) {
					var set = CONF[ch];
					out += '<em>' + escapeHtml(set[Math.floor(hash(i, salt + 7) * set.length)]) + '</em>';
				} else if (h < rate * 1.12) {
					out += '<em>' + (unk || '؟') + '</em>';
				} else {
					out += escapeHtml(ch);
				}
			}
			return out;
		}

		function paint() {
			var loss = 0, names = [];
			Object.keys(FACTORS).forEach(function (k) {
				if (on[k]) { loss += FACTORS[k].w; names.push(FACTORS[k].label); }
			});
			var score = Math.max(8, 100 - loss);

			pctEl.textContent = fa(score) + '٪';
			barEl.style.width = score + '%';
			sim.classList.toggle('warn', score < 75 && score >= 45);
			sim.classList.toggle('bad', score < 45);

			var f = [];
			if (on.blur) f.push('blur(1.5px)');
			if (on.dark) f.push('brightness(.68) contrast(.82)');
			if (on.res) f.push('blur(.6px) contrast(.88) saturate(.85)');
			cardEl.style.filter = f.length ? f.join(' ') : '';
			cardEl.style.transform = on.skew
				? 'rotate(-3.6deg) skewY(1.2deg)' + (on.res ? ' scale(.94)' : '')
				: (on.res ? 'scale(.94)' : '');
			cardEl.style.fontStyle = on.hand ? 'italic' : '';
			cardEl.style.letterSpacing = on.hand ? '.4px' : '';
			pbEl.style.backgroundImage = on.bg
				? 'repeating-linear-gradient(35deg,#E7ECF5,#E7ECF5 6px,#D8E0ED 6px,#D8E0ED 12px),radial-gradient(circle at 30% 40%,rgba(21,57,124,.2),transparent 55%)'
				: '';

			var rate = Math.min(0.62, (100 - score) / 100 * 0.72);
			outEl.innerHTML =
				'<div>' + corrupt(SRC1, rate, 3, '؟') + '</div>' +
				'<div class="mono" style="font-size:12.5px;color:#5A6478">' + corrupt(SRC2, rate * 0.8, 11, '?') + '</div>';

			var msg;
			if (score >= 92) {
				msg = '<b>شرایط ایده‌آل:</b> سند چاپی، صاف، پرنور و با رزولوشن کافی.';
			} else if (score >= 75) {
				msg = '<b>قابل قبول:</b> ' + names.join('، ') + ' فعال است؛ متن هنوز تا حد زیادی درست خوانده می‌شود.';
			} else if (score >= 45) {
				msg = '<b>پرخطا:</b> ' + names.join('، ') + '. اینجاست که نقطه‌ها جابه‌جا می‌شوند و حروف چسبیده به هم اشتباه خوانده می‌شوند.';
			} else {
				msg = '<b>غیرقابل اتکا:</b> ' + names.join('، ') + '. در این شرایط خروجی باید دوباره توسط انسان بررسی شود.';
			}
			tagEl.innerHTML = msg;
		}

		togs.forEach(function (b) {
			b.addEventListener('click', function () {
				var k = b.getAttribute('data-f');
				on[k] = !on[k];
				b.setAttribute('aria-pressed', on[k] ? 'true' : 'false');
				paint();
			});
		});
		if (resetS) {
			resetS.addEventListener('click', function () {
				Object.keys(on).forEach(function (k) { on[k] = false; });
				togs.forEach(function (b) { b.setAttribute('aria-pressed', 'false'); });
				paint();
			});
		}
		paint();
	}

	/* ══════════════════════════════════════════════════════════════════════
	 * ۳ — انتخابگر پنج نوع OCR (فصل «انواع OCR»)
	 * ══════════════════════════════════════════════════════════════════════ */
	var tsel = $('#tsel', root);
	if (tsel) {
		var chips = $$('.tchip', tsel);
		var panels = $$('.tpanel', tsel);
		chips.forEach(function (c) {
			c.addEventListener('click', function () {
				var k = c.getAttribute('data-t');
				chips.forEach(function (x) {
					var sel = x === c;
					x.classList.toggle('on', sel);
					x.setAttribute('aria-selected', sel ? 'true' : 'false');
				});
				panels.forEach(function (p) { p.hidden = p.getAttribute('data-p') !== k; });
				var rail = tsel.querySelector('.tsel-rail');
				if (rail) {
					var cr = c.getBoundingClientRect(), rr = rail.getBoundingClientRect();
					if (cr.left < rr.left || cr.right > rr.right) {
						rail.scrollTo({ left: c.offsetLeft - rail.clientWidth / 2 + c.offsetWidth / 2, behavior: reduce ? 'auto' : 'smooth' });
					}
				}
			});
		});
	}
})();
