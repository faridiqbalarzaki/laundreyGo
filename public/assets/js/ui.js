/* ui.js — perilaku UI lintas-halaman (pengganti sebagian App.* dari app.js).
   pos.js / order.js (langkah berikutnya) memakai LG.fetchJson dan LG.ui.* */
(() => {
  "use strict";

  const LG = (window.LG = window.LG || {});
  const body = document.body;
  const meta = document.querySelector('meta[name="csrf-token"]');
  LG.base = body.dataset.base || "";
  LG.csrf = meta ? meta.content : "";
  LG.url = (p) => LG.base + "/" + String(p).replace(/^\/+/, "");

  const store = {
    get(k, d) { try { const v = localStorage.getItem(k); return v === null ? d : v; } catch (e) { return d; } },
    set(k, v) { try { localStorage.setItem(k, v); } catch (e) {} },
  };
  const $ = (sel) => document.querySelector(sel);

  /* ---------- Fetch JSON ke endpoint PHP (sudah menyertakan CSRF) ---------- */
  LG.fetchJson = async (path, { method = "POST", data = null } = {}) => {
    const res = await fetch(LG.url(path), {
      method,
      credentials: "same-origin",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRF-Token": LG.csrf,
        "X-Requested-With": "XMLHttpRequest",
      },
      body: data === null ? undefined : JSON.stringify(data),
    });
    if (res.status === 401) { location.href = LG.url("/login"); throw new Error("Sesi berakhir"); }
    let json = null;
    try { json = await res.json(); } catch (e) {}
    if (!res.ok) {
      const err = new Error((json && json.error) || "Terjadi kesalahan");
      err.status = res.status;
      err.payload = json;
      throw err;
    }
    return json;
  };

  /* ---------- UI helpers ---------- */
  const ui = (LG.ui = {});
  let soundOn = store.get("lg.sound", "1") === "1";
  let toastTimer = null;

  ui.playPop = () => {
    if (!soundOn) return;
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.type = "sine";
      osc.frequency.setValueAtTime(520, ctx.currentTime);
      osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.065);
      gain.gain.setValueAtTime(0.08, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.07);
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.start();
      osc.stop(ctx.currentTime + 0.075);
    } catch (e) {}
  };

  ui.renderSound = () => {
    const on = $("#sound-icon .snd-on");
    const off = $("#sound-icon .snd-off");
    if (on && off) { on.hidden = !soundOn; off.hidden = soundOn; }
  };
  ui.toggleSound = () => {
    soundOn = !soundOn;
    store.set("lg.sound", soundOn ? "1" : "0");
    if (soundOn) ui.playPop();
    ui.renderSound();
  };
  ui.toggleTheme = () => {
    const dark = body.classList.toggle("dark");
    store.set("lg.theme", dark ? "dark" : "light");
  };

  ui.openDrawer = (html) => {
    const d = $("#order-drawer");
    if (!d) return;
    if (typeof html === "string") d.innerHTML = html;   // html dari server/partial tepercaya
    d.classList.add("open");
    $("#drawer-backdrop")?.classList.add("open");
  };
  ui.closeDrawer = () => {
    $("#order-drawer")?.classList.remove("open");
    $("#drawer-backdrop")?.classList.remove("open");
  };
  ui.openModal = (html) => {
    const m = $("#modal-backdrop");
    if (!m) return;
    if (typeof html === "string") $("#modal-content").innerHTML = html;
    m.style.display = "flex";
  };
  ui.closeModal = () => {
    const m = $("#modal-backdrop");
    if (m) m.style.display = "none";
  };

  /** Toast 5 detik; bila onUndo diberikan, tampil tombol "Urungkan" (panggil endpoint undo di callback). */
  ui.showToast = (message, onUndo = null) => {
    const box = $("#toast-container");
    if (!box) return;
    clearTimeout(toastTimer);
    box.replaceChildren();
    const item = document.createElement("div");
    item.className = "toast-item";
    const text = document.createElement("span");
    text.textContent = message;
    item.append(text);
    if (typeof onUndo === "function") {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "toast-undo-btn";
      btn.textContent = "Urungkan (5 dtk)";
      btn.addEventListener("click", () => {
        clearTimeout(toastTimer);
        box.replaceChildren();
        onUndo();
      });
      item.append(btn);
    }
    box.append(item);
    toastTimer = setTimeout(() => box.replaceChildren(), 5000);
  };

  /** Perbarui pill topbar & badge sidebar dari respons API. */
  ui.setCounts = ({ active, today, selesai } = {}) => {
    if (active !== undefined) $("#pill-active-orders").textContent = "Pesanan aktif: " + active;
    if (today !== undefined) $("#pill-today-orders").textContent = "Hari ini: " + today + " pesanan";
    if (selesai !== undefined) document.querySelectorAll('[data-badge="selesai"]').forEach((b) => (b.textContent = selesai));
  };

  /* ---------- Event delegation ---------- */
  const builtin = {
    "toggle-theme": ui.toggleTheme,
    "toggle-sound": ui.toggleSound,
    "drawer-close": ui.closeDrawer,
    "modal-close": ui.closeModal,
  };
  document.addEventListener("click", (ev) => {
    const target = ev.target.closest("[data-action]");
    if (!target) return;
    const action = target.dataset.action;
    if (builtin[action]) { builtin[action](target, ev); return; }
    // Aksi lain (mis. "shortcuts", "pos-add-item") ditangani modul halaman:
    document.dispatchEvent(new CustomEvent("lg:action", { detail: { action, el: target, originalEvent: ev } }));
  });

  const search = $("#global-search");
  if (search) {
    search.addEventListener("input", () =>
      document.dispatchEvent(new CustomEvent("lg:search", { detail: { value: search.value } })));
  }

  document.addEventListener("keydown", (ev) => {
    const typing = /^(INPUT|TEXTAREA|SELECT)$/.test(document.activeElement?.tagName || "");
    if (ev.key === "Escape") { ui.closeModal(); ui.closeDrawer(); }
    if (search && (ev.key === "F1" || (ev.key === "/" && !typing))) {
      ev.preventDefault();
      search.focus();
    }
  });

  ui.renderSound();
})();
