document.addEventListener('DOMContentLoaded', function () {
  var nav = document.getElementById('mainNav');
  if (nav) {
    var onScroll = function () {
      if (window.scrollY > 40) {
        nav.classList.add('nav-scrolled');
      } else {
        nav.classList.remove('nav-scrolled');
      }
    };
    onScroll();
    window.addEventListener('scroll', onScroll);
  }

  function normalizePath(path) {
    var clean = path.replace(/\/index\.php$/, '').replace(/\/$/, '');
    return clean === '' ? '/' : clean;
  }

  document.querySelectorAll('a[data-scroll]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var href = link.getAttribute('href');
      if (!href) {
        return;
      }

      var url = new URL(href, window.location.origin);
      var currentPath = normalizePath(window.location.pathname);
      var linkPath = normalizePath(url.pathname);

      if (url.hash && linkPath === currentPath) {
        var target = document.querySelector(url.hash);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      }
    });
  });

  var voucherApply = document.getElementById('voucherApply');
  var voucherCode = document.getElementById('voucherCode');
  var voucherStatus = document.getElementById('voucherStatus');
  var discountPreview = document.getElementById('discountPreview');
  var totalPreview = document.getElementById('totalPreview');
  var discountInput = document.getElementById('voucherDiscountInput');

  if (voucherApply && voucherCode && voucherStatus && discountPreview && totalPreview) {
    voucherApply.addEventListener('click', function () {
      var code = voucherCode.value.trim();
      if (code === '') {
        voucherStatus.textContent = 'Masukkan kode voucher.';
        voucherStatus.className = 'voucher-status error';
        return;
      }

      voucherApply.disabled = true;
      voucherApply.textContent = 'Checking...';

      var payload = new URLSearchParams();
      payload.append('code', code);
      payload.append('ticket_id', voucherApply.getAttribute('data-ticket-id') || '0');
      payload.append('qty', voucherApply.getAttribute('data-qty') || '1');

      fetch('actions/validate_voucher.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: payload.toString()
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data && data.ok) {
            voucherStatus.textContent = data.message;
            voucherStatus.className = 'voucher-status success';
            discountPreview.textContent = data.discount_label;
            totalPreview.textContent = data.total_label;
            if (discountInput) {
              discountInput.value = data.discount;
            }
          } else {
            voucherStatus.textContent = (data && data.message) ? data.message : 'Voucher tidak valid.';
            voucherStatus.className = 'voucher-status error';
            discountPreview.textContent = 'Rp 0';
            totalPreview.textContent = data && data.total_label ? data.total_label : totalPreview.textContent;
            if (discountInput) {
              discountInput.value = '0';
            }
          }
        })
        .catch(function () {
          voucherStatus.textContent = 'Gagal memeriksa voucher.';
          voucherStatus.className = 'voucher-status error';
        })
        .finally(function () {
          voucherApply.disabled = false;
          voucherApply.textContent = 'Apply';
        });
    });
  }
});
