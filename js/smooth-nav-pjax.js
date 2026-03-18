/**
 * Smooth Navigation (Vanilla PJAX)
 *
 * Intercepts internal link clicks, fetches the target page via AJAX,
 * swaps #main-content innerHTML, and pushes browser history.
 * Alpine.js components are re-initialised after each swap.
 */
(function () {
  var MAIN_SEL = '#main-content';
  var NAV_SEL  = '#starter-main-nav';
  var bar      = null;
  var trickle  = null;

  // --- Progress bar helpers ---
  function createBar() {
    bar = document.createElement('div');
    bar.id = 'starter-pjax-bar';
    bar.style.cssText = 'position:fixed;top:0;left:0;height:3px;background:#2563eb;z-index:9999;transition:width .2s;width:0;';
    document.body.appendChild(bar);
  }

  function startBar() {
    if (!bar) createBar();
    bar.style.width = '0';
    bar.style.opacity = '1';
    var w = 10;
    trickle = setInterval(function () {
      w += (90 - w) * 0.08;
      bar.style.width = w + '%';
    }, 150);
  }

  function finishBar() {
    clearInterval(trickle);
    if (bar) {
      bar.style.width = '100%';
      setTimeout(function () { bar.style.opacity = '0'; }, 200);
    }
  }

  // --- Should we intercept this click? ---
  function shouldIntercept(anchor) {
    if (!anchor || !anchor.href) return false;
    if (anchor.target === '_blank') return false;
    if (anchor.hasAttribute('data-no-pjax')) return false;
    if (anchor.hasAttribute('download')) return false;

    var url;
    try { url = new URL(anchor.href); } catch (e) { return false; }

    if (url.origin !== location.origin) return false;
    if (url.pathname === location.pathname && url.hash) return false;
    if (url.pathname.indexOf('/wp-admin') === 0 || url.pathname.indexOf('/wp-login') === 0) return false;

    return true;
  }

  // --- Navigate via PJAX ---
  function navigate(url, pushState) {
    var mainEl = document.querySelector(MAIN_SEL);
    if (!mainEl) { window.location = url; return; }

    startBar();

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function (res) {
        if (!res.ok) throw new Error(res.status);
        return res.text();
      })
      .then(function (html) {
        var doc = new DOMParser().parseFromString(html, 'text/html');
        var newMain = doc.querySelector(MAIN_SEL);

        if (!newMain) {
          window.location = url;
          return;
        }

        // Sync dark mode from localStorage
        var savedTheme = localStorage.getItem('starter-theme');
        if (savedTheme === 'dark') {
          document.documentElement.classList.add('dark');
        } else if (savedTheme === 'light') {
          document.documentElement.classList.remove('dark');
        } else {
          var newHtml = doc.documentElement;
          if (newHtml.classList.contains('dark')) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
        }
        // Re-sync theme toggle icon
        var icon = document.getElementById('starter-theme-icon');
        if (icon) icon.textContent = document.documentElement.classList.contains('dark') ? '☀️' : '🌙';

        // Destroy Alpine tree on old content before replacing
        if (window.Alpine && typeof Alpine.destroyTree === 'function') {
          Alpine.destroyTree(mainEl);
        }

        // Copy attributes from new <main> to current <main>
        Array.from(newMain.attributes).forEach(function (attr) {
          mainEl.setAttribute(attr.name, attr.value);
        });
        Array.from(mainEl.attributes).forEach(function (attr) {
          if (attr.name !== 'id' && !newMain.hasAttribute(attr.name)) {
            mainEl.removeAttribute(attr.name);
          }
        });

        mainEl.innerHTML = newMain.innerHTML;

        // Update page title
        var newTitle = doc.querySelector('title');
        if (newTitle) document.title = newTitle.textContent;

        if (pushState !== false) {
          history.pushState({ pjax: true }, '', url);
        }

        // Re-init Alpine on new content
        if (window.Alpine && typeof Alpine.initTree === 'function') {
          Alpine.initTree(mainEl);
        }

        updateActiveNav(url);
        window.scrollTo(0, 0);

        document.dispatchEvent(new CustomEvent('smooth-nav:content-swapped', { detail: { url: url } }));

        finishBar();
      })
      .catch(function () {
        finishBar();
        window.location = url;
      });
  }

  // --- Update active class on nav links ---
  function updateActiveNav(url) {
    var nav = document.querySelector(NAV_SEL);
    if (!nav) return;
    var currentPath = new URL(url, location.origin).pathname.replace(/\/+$/, '');
    nav.querySelectorAll('a[href]').forEach(function (a) {
      var linkPath = new URL(a.href, location.origin).pathname.replace(/\/+$/, '');
      var isActive = linkPath === currentPath;
      a.classList.toggle('text-brand', isActive);
      a.classList.toggle('dark:text-brand-light', isActive);
      a.classList.toggle('font-semibold', isActive);
    });
  }

  // --- Event listeners ---

  document.addEventListener('click', function (e) {
    var anchor = e.target.closest('a');
    if (!anchor) return;
    if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
    if (e.defaultPrevented) return;

    var mainEl = document.querySelector(MAIN_SEL);
    if (mainEl && mainEl.hasAttribute('data-no-pjax')) {
      return;
    }

    if (shouldIntercept(anchor)) {
      e.preventDefault();
      navigate(anchor.href, true);
    }
  });

  window.addEventListener('popstate', function () {
    navigate(location.href, false);
  });
})();
