(function () {

  function cfgOk() {

    if (typeof LRR_CFG !== "object") {
      return false;
    }

    LRR_CFG.storeLat = Number(LRR_CFG.storeLat);
    LRR_CFG.storeLng = Number(LRR_CFG.storeLng);
    LRR_CFG.radiusKm = Number(LRR_CFG.radiusKm)

    return (
      LRR_CFG &&
      typeof LRR_CFG.storeLat === "number" &&
      typeof LRR_CFG.storeLng === "number" &&
      typeof LRR_CFG.radiusKm === "number" &&
      LRR_CFG.radiusKm > 0 &&
      typeof LRR_CFG.redirectUrl === "string" &&
      LRR_CFG.redirectUrl.length > 5
    );
  }

  function toRad(v) {
    return (v * Math.PI) / 180;
  }

  // Haversine in KM (client-side)
  function distanceKm(lat1, lng1, lat2, lng2) {
    const R = 6371;
    const dLat = toRad(lat2 - lat1);
    const dLng = toRad(lng2 - lng1);
    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(toRad(lat1)) *
      Math.cos(toRad(lat2)) *
      Math.sin(dLng / 2) *
      Math.sin(dLng / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }

  function showModal(message) {
    const modal = document.getElementById("lrr-modal");
    const msgEl = document.getElementById("lrr-modal-message");
    if (!modal || !msgEl) {
      alert(message);
      return;
    }
    msgEl.textContent = message || "Sorry.";
    modal.setAttribute("aria-hidden", "false");
    modal.classList.add("is-open");
    document.body.classList.add("lrr-modal-open");
  }

  function closeModal() {
    const modal = document.getElementById("lrr-modal");
    if (!modal) return;
    modal.setAttribute("aria-hidden", "true");
    modal.classList.remove("is-open");
    document.body.classList.remove("lrr-modal-open");
  }

  function bindCloseEvents() {
    document.addEventListener("click", function (e) {
      const t = e.target;
      if (t && t.getAttribute && t.getAttribute("data-lrr-close") === "1") {
        e.preventDefault();
        closeModal();
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeModal();
    });
  }

  function handleOrderClick(e) {
    e.preventDefault();

    if (!cfgOk()) {
      showModal("Sorry — ordering is not configured. Please contact the restaurant.");
      return;
    }

    if (!navigator.geolocation) {
      showModal(LRR_CFG.msgNoSupport || "Sorry — location is not supported.");
      return;
    }

    navigator.geolocation.getCurrentPosition(
      function (pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

        // sanity
        if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
          showModal("Sorry — invalid location detected.");
          return;
        }

        const d = distanceKm(LRR_CFG.storeLat, LRR_CFG.storeLng, lat, lng);

        if (d <= LRR_CFG.radiusKm) {
          window.location.href = LRR_CFG.redirectUrl;
        } else {
          showModal(LRR_CFG.msgOutside || "Sorry — outside allowed range.");
        }
      },
      function (err) {
        showModal(LRR_CFG.msgDenied || "Sorry — location permission denied.");
      },
      {
        enableHighAccuracy: true,
        timeout: 15000,
        maximumAge: 0,
      }
    );
  }

  function bindTriggers() {
    // 1) Shortcode button
    document.querySelectorAll('[data-lrr-order="1"]').forEach(function (el) {

      el.classList.remove('dropdown-toggle');
      el.addEventListener("click", handleOrderClick);
    });

    // 2) Optional selector binding (menu button, etc.)
    const sel = (LRR_CFG && LRR_CFG.bindSelector) ? String(LRR_CFG.bindSelector).trim() : "";
    if (sel) {
      try {
        document.querySelectorAll(sel).forEach(function (el) {
          el.addEventListener("click", handleOrderClick);
        });
      } catch (e) {
        // invalid selector: do nothing
      }
    }
  }

  document.addEventListener("DOMContentLoaded", function () {
    bindCloseEvents();
    bindTriggers();
  });

  const observer = new MutationObserver(() => {
    document.querySelectorAll('[data-lrr-order="1"].dropdown-toggle')
      .forEach(el => el.classList.remove('dropdown-toggle'));
  });

  observer.observe(document.body, { childList: true, subtree: true });
})();
