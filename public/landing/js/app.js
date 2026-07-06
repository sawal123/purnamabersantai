const landingAppState = window.__landingAppState || {
  cleanup: [],
  heroIntervalId: null,
  heroParallaxFrameId: null,
  observer: null,
  swipers: [],
};

window.__landingAppState = landingAppState;

const registerCleanup = (callback) => {
  landingAppState.cleanup.push(callback);
};

const cleanupLandingPage = () => {
  landingAppState.cleanup.forEach((callback) => callback());
  landingAppState.cleanup = [];

  if (landingAppState.observer) {
    landingAppState.observer.disconnect();
    landingAppState.observer = null;
  }

  if (landingAppState.heroIntervalId) {
    window.clearInterval(landingAppState.heroIntervalId);
    landingAppState.heroIntervalId = null;
  }

  if (landingAppState.heroParallaxFrameId) {
    window.cancelAnimationFrame(landingAppState.heroParallaxFrameId);
    landingAppState.heroParallaxFrameId = null;
  }

  landingAppState.swipers.forEach((swiperInstance) => {
    if (swiperInstance && typeof swiperInstance.destroy === "function") {
      swiperInstance.destroy(true, true);
    }
  });

  landingAppState.swipers = [];
  document.body.classList.remove("mobile-nav-open", "modal-open");
};

const bindEvent = (target, eventName, handler, options) => {
  target.addEventListener(eventName, handler, options);
  registerCleanup(() => target.removeEventListener(eventName, handler, options));
};

const createSectionSwiper = (
  selector,
  paginationSelector,
  prevSelector,
  nextSelector,
  breakpoints,
) => {
  const element = document.querySelector(selector);
  if (!element || typeof Swiper === "undefined") {
    return null;
  }

  const swiperInstance = new Swiper(element, {
    slidesPerView: 1.1,
    speed: 900,
    spaceBetween: 20,
    grabCursor: true,
    loop: true,
    allowTouchMove: true,
    observer: true,
    observeParents: true,
    loopAdditionalSlides: 2,
    autoplay: {
      delay: 2200,
      disableOnInteraction: false,
      pauseOnMouseEnter: true,
      waitForTransition: false,
    },
    pagination: {
      el: paginationSelector,
      clickable: true,
    },
    navigation: {
      prevEl: prevSelector,
      nextEl: nextSelector,
    },
    breakpoints,
  });

  landingAppState.swipers.push(swiperInstance);

  return swiperInstance;
};

const initRevealObserver = () => {
  const revealElements = document.querySelectorAll(".reveal");
  if (!revealElements.length) {
    return;
  }

  if (typeof IntersectionObserver === "undefined") {
    revealElements.forEach((element) => element.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add("is-visible");
        observer.unobserve(entry.target);
      });
    },
    {
      threshold: 0.18,
      rootMargin: "0px 0px -60px 0px",
    },
  );

  revealElements.forEach((element) => observer.observe(element));
  landingAppState.observer = observer;
};

const initHeader = () => {
  const siteHeader = document.getElementById("site-header");
  const mobileMenuButton = document.getElementById("mobile-menu-button");
  const mobileNavPanel = document.getElementById("mobile-nav-panel");
  const mobileNavLinks = mobileNavPanel
    ? mobileNavPanel.querySelectorAll("a")
    : [];

  const syncHeaderScrollState = () => {
    if (!siteHeader) {
      return;
    }

    siteHeader.classList.toggle("is-scrolled", window.scrollY > 24);
  };

  const closeMobileMenu = () => {
    if (!mobileMenuButton || !mobileNavPanel) {
      return;
    }

    mobileMenuButton.setAttribute("aria-expanded", "false");
    mobileMenuButton.setAttribute("aria-label", "Open navigation menu");
    mobileNavPanel.classList.remove("is-open");
    siteHeader?.classList.remove("is-mobile-open");
    document.body.classList.remove("mobile-nav-open");
  };

  const toggleMobileMenu = () => {
    if (!mobileMenuButton || !mobileNavPanel) {
      return;
    }

    const isOpen = mobileMenuButton.getAttribute("aria-expanded") === "true";
    mobileMenuButton.setAttribute("aria-expanded", String(!isOpen));
    mobileMenuButton.setAttribute(
      "aria-label",
      isOpen ? "Open navigation menu" : "Close navigation menu",
    );
    mobileNavPanel.classList.toggle("is-open", !isOpen);
    siteHeader?.classList.toggle("is-mobile-open", !isOpen);
    document.body.classList.toggle("mobile-nav-open", !isOpen);
  };

  syncHeaderScrollState();
  bindEvent(window, "scroll", syncHeaderScrollState, { passive: true });

  if (mobileMenuButton) {
    bindEvent(mobileMenuButton, "click", toggleMobileMenu);
  }

  mobileNavLinks.forEach((link) => {
    bindEvent(link, "click", closeMobileMenu);
  });

  bindEvent(window, "resize", () => {
    if (window.innerWidth >= 1024) {
      closeMobileMenu();
    }
  });
};

const buildMerchandiseProducts = (assetBase, contactUrl) => ({
  "moonlight-tee": {
    kicker: "Best Seller",
    title: "Moonlight Tee",
    price: "Rp185.000",
    description:
      "Kaos oversized berbahan cotton combed 24s dengan artwork depan-belakang bertema panggung malam Purnama Bersantai.",
    features: [
      "Ukuran tersedia S sampai XXL",
      "Sablon premium dengan hasil warna lebih tajam",
      "Cutting relaxed fit yang nyaman untuk dipakai harian",
    ],
    orderUrl: contactUrl,
    gallery: [
      {
        src: `${assetBase}/Rectangle 17.png`,
        alt: "Moonlight Tee front view",
        className: "object-[16%_25%]",
      },
      {
        src: `${assetBase}/hero/image 1.png`,
        alt: "Moonlight Tee detail artwork",
        className: "",
      },
      {
        src: `${assetBase}/image 8.png`,
        alt: "Moonlight Tee closeup fabric",
        className: "",
      },
    ],
  },
  "vinyl-pack": {
    kicker: "Limited Pack",
    title: "Midnight Pack",
    price: "Rp240.000",
    description:
      "Paket kolektor berisi vinyl sleeve display, poster lipat ukuran A3, dan photocard eksklusif untuk pengunjung festival.",
    features: [
      "Bundle 3 item dalam satu box collector",
      "Finishing glossy pada poster dan photocard",
      "Cocok untuk hadiah atau display koleksi event",
    ],
    orderUrl: contactUrl,
    gallery: [
      {
        src: `${assetBase}/hero/image 1.png`,
        alt: "Midnight Pack hero product view",
        className: "",
      },
      {
        src: `${assetBase}/hero/2022_03_29_124082_1648520536._large.jpg`,
        alt: "Midnight Pack poster preview",
        className: "",
      },
      {
        src: `${assetBase}/Rectangle 15.png`,
        alt: "Midnight Pack collector sleeve detail",
        className: "",
      },
    ],
  },
  "festival-tote": {
    kicker: "New Arrival",
    title: "Festival Tote",
    price: "Rp129.000",
    description:
      "Totebag canvas tebal dengan kompartemen luas dan print satu sisi, dibuat untuk bawa merchandise atau kebutuhan konsermu.",
    features: [
      "Material canvas tebal dan strap panjang",
      "Ruang penyimpanan luas untuk daily carry",
      "Print premium yang tetap solid untuk pemakaian rutin",
    ],
    orderUrl: contactUrl,
    gallery: [
      {
        src: `${assetBase}/Rectangle 15.png`,
        alt: "Festival Tote front view",
        className: "object-[28%_20%]",
      },
      {
        src: `${assetBase}/image 8.png`,
        alt: "Festival Tote fabric detail",
        className: "",
      },
      {
        src: `${assetBase}/hero/image 1.png`,
        alt: "Festival Tote lifestyle view",
        className: "",
      },
    ],
  },
  "lunar-cap": {
    kicker: "Fresh Drop",
    title: "Lunar Cap",
    price: "Rp115.000",
    description:
      "Topi dad cap dengan bordir logo Purnama Bersantai dan strap adjustable untuk pemakaian santai sehari-hari.",
    features: [
      "Bahan twill lembut dan nyaman dipakai lama",
      "Strap belakang adjustable untuk berbagai ukuran kepala",
      "Bordir logo premium dengan tampilan clean",
    ],
    orderUrl: contactUrl,
    gallery: [
      {
        src: `${assetBase}/logo.png`,
        alt: "Lunar Cap logo embroidery view",
        className: "object-contain bg-[#190406] p-10",
      },
      {
        src: `${assetBase}/hero/image 1.png`,
        alt: "Lunar Cap lifestyle view",
        className: "",
      },
      {
        src: `${assetBase}/image 8.png`,
        alt: "Lunar Cap detail view",
        className: "",
      },
    ],
  },
  "glow-poster": {
    kicker: "Collector",
    title: "Glow Poster",
    price: "Rp79.000",
    description:
      "Poster artwork event berukuran A3 dengan hasil cetak warna pekat untuk dekor kamar atau koleksi festival.",
    features: [
      "Ukuran A3 siap bingkai",
      "Finishing glossy dan warna lebih vivid",
      "Desain eksklusif edisi Purnama Bersantai",
    ],
    orderUrl: contactUrl,
    gallery: [
      {
        src: `${assetBase}/image 8.png`,
        alt: "Glow Poster main artwork",
        className: "",
      },
      {
        src: `${assetBase}/hero/2022_03_29_124082_1648520536._large.jpg`,
        alt: "Glow Poster wall display",
        className: "",
      },
      {
        src: `${assetBase}/Rectangle 17.png`,
        alt: "Glow Poster detail crop",
        className: "object-[50%_30%]",
      },
    ],
  },
  "backstage-lanyard": {
    kicker: "Event Gear",
    title: "Lanyard Pass",
    price: "Rp59.000",
    description:
      "Lanyard woven lengkap dengan card pass edisi festival, cocok untuk koleksi atau daily wearable accessory.",
    features: [
      "Tali woven dengan warna tahan lama",
      "Card pass desain eksklusif event",
      "Ringan dan nyaman untuk pemakaian harian",
    ],
    orderUrl: contactUrl,
    gallery: [
      {
        src: `${assetBase}/hero/2022_03_29_124082_1648520536._large.jpg`,
        alt: "Lanyard Pass main view",
        className: "",
      },
      {
        src: `${assetBase}/logo.png`,
        alt: "Lanyard Pass logo detail",
        className: "object-contain bg-[#190406] p-10",
      },
      {
        src: `${assetBase}/hero/image 1.png`,
        alt: "Lanyard Pass lifestyle view",
        className: "",
      },
    ],
  },
});

const initTicketModal = () => {
  const ticketModal = document.getElementById("ticket-modal");
  const ticketModalBatch = document.getElementById("ticket-modal-batch");
  const ticketModalTitle = document.getElementById("ticket-modal-title");
  const ticketModalPrice = document.getElementById("ticket-modal-price");
  const ticketModalLinks = document.getElementById("ticket-modal-links");
  const ticketCloseTriggers = ticketModal
    ? ticketModal.querySelectorAll("[data-ticket-close]")
    : [];
  const ticketOptionsElement = document.getElementById(
    "ticket-purchase-options-json",
  );

  if (
    !ticketModal ||
    !ticketModalBatch ||
    !ticketModalTitle ||
    !ticketModalPrice ||
    !ticketModalLinks ||
    !ticketOptionsElement
  ) {
    return;
  }

  const ticketOptions = JSON.parse(ticketOptionsElement.textContent || "{}");

  const closeTicketModal = () => {
    ticketModal.classList.remove("is-open");
    ticketModal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
  };

  const openTicketModal = (ticketKey) => {
    const ticket = ticketOptions[ticketKey];
    if (!ticket) {
      return;
    }

    ticketModalBatch.textContent = ticket.batchLabel || "Purchase Options";
    ticketModalTitle.textContent = ticket.title;
    ticketModalPrice.textContent = ticket.price;
    ticketModalLinks.innerHTML = ticket.links
      .map(
        (item) => `
          <a
            href="${item.url}"
            class="ticket-modal-link"
          >
            <span>${item.label}</span>
            <span class="ticket-modal-link-arrow" aria-hidden="true">+</span>
          </a>
        `,
      )
      .join("");

    ticketModal.classList.add("is-open");
    ticketModal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");
    ticketModal
      .querySelector(".ticket-modal-close")
      ?.focus();
  };

  bindEvent(document, "click", (event) => {
    if (!(event.target instanceof Element)) {
      return;
    }

    const trigger = event.target.closest("[data-ticket-trigger]");
    if (!trigger) {
      return;
    }

    const ticketKey = trigger.getAttribute("data-ticket-trigger");
    openTicketModal(ticketKey);
  });

  ticketCloseTriggers.forEach((trigger) => {
    bindEvent(trigger, "click", closeTicketModal);
  });

  bindEvent(document, "keydown", (event) => {
    if (event.key === "Escape" && ticketModal.classList.contains("is-open")) {
      closeTicketModal();
    }
  });
};

const initMerchModal = (assetBase, contactUrl) => {
  const merchModal = document.getElementById("merch-modal");
  const merchModalGallery = document.getElementById("merch-modal-gallery");
  const merchModalImage = document.getElementById("merch-modal-image");
  const merchModalKicker = document.getElementById("merch-modal-kicker");
  const merchModalTitle = document.getElementById("merch-modal-title");
  const merchModalPrice = document.getElementById("merch-modal-price");
  const merchModalDescription = document.getElementById("merch-modal-description");
  const merchModalFeatures = document.getElementById("merch-modal-features");
  const merchModalOrder = document.getElementById("merch-modal-order");
  const merchModalCloseButton = merchModal?.querySelector(".merch-modal-close");
  const merchCloseTriggers = merchModal
    ? merchModal.querySelectorAll("[data-merch-close]")
    : [];

  if (!merchModal || !merchModalGallery || !merchModalImage || !merchModalOrder) {
    return;
  }

  const merchandiseProductsElement = document.getElementById(
    "merchandise-products-json",
  );
  const merchandiseProducts = merchandiseProductsElement
    ? JSON.parse(merchandiseProductsElement.textContent || "{}")
    : buildMerchandiseProducts(assetBase, contactUrl);

  const setActiveMerchImage = (gallery, activeIndex) => {
    const activeItem = gallery[activeIndex];
    if (!activeItem) {
      return;
    }

    merchModalImage.src = activeItem.src;
    merchModalImage.alt = activeItem.alt;
    merchModalImage.className = activeItem.className || "";

    merchModalGallery.querySelectorAll("[data-merch-thumb]").forEach((thumb, index) => {
      thumb.classList.toggle("is-active", index === activeIndex);
    });
  };

  const closeMerchModal = () => {
    merchModal.classList.remove("is-open");
    merchModal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
  };

  const openMerchModal = (productKey) => {
    const product = merchandiseProducts[productKey];
    if (!product) {
      return;
    }

    merchModalKicker.textContent = product.kicker;
    merchModalTitle.textContent = product.title;
    merchModalPrice.textContent = product.price;
    merchModalDescription.textContent = product.description;
    merchModalOrder.setAttribute("href", product.orderUrl);
    merchModalGallery.innerHTML = product.gallery
      .map(
        (item, index) => `
          <button
            type="button"
            class="merch-modal-thumb${index === 0 ? " is-active" : ""}"
            data-merch-thumb
            data-thumb-index="${index}"
            aria-label="View merchandise image ${index + 1}"
          >
            <img
              src="${item.src}"
              alt="${item.alt}"
              class="${item.className || ""}"
            />
          </button>
        `,
      )
      .join("");
    merchModalFeatures.innerHTML = product.features
      .map((feature) => `<li>${feature}</li>`)
      .join("");

    setActiveMerchImage(product.gallery, 0);

    merchModal.classList.add("is-open");
    merchModal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");

    merchModalGallery.querySelectorAll("[data-merch-thumb]").forEach((thumb) => {
      thumb.onclick = () => {
        const index = Number(thumb.getAttribute("data-thumb-index"));
        setActiveMerchImage(product.gallery, index);
      };
    });

    merchModalCloseButton?.focus();
  };

  bindEvent(document, "click", (event) => {
    if (!(event.target instanceof Element)) {
      return;
    }

    const trigger = event.target.closest("[data-merch-trigger]");
    if (!trigger) {
      return;
    }

    const productKey = trigger.getAttribute("data-merch-trigger");
    openMerchModal(productKey);
  });

  merchCloseTriggers.forEach((trigger) => {
    bindEvent(trigger, "click", closeMerchModal);
  });

  bindEvent(document, "keydown", (event) => {
    if (event.key === "Escape" && merchModal.classList.contains("is-open")) {
      closeMerchModal();
    }
  });
};

const initHeroRotation = (assetBase) => {
  const heroImagesElement = document.getElementById("landing-hero-images-json");
  const heroImages = heroImagesElement
    ? JSON.parse(heroImagesElement.textContent || "[]")
    : [
        `${assetBase}/hero/image 1.png`,
        `${assetBase}/hero/2022_03_29_124082_1648520536._large.jpg`,
        `${assetBase}/hero/Marko_Perkovi%C4%87_Thompson_2025_Zagreb_Hipodrom_concert.jpg`,
      ];

  const heroLayerA = document.getElementById("hero-bg-a");
  const heroLayerB = document.getElementById("hero-bg-b");

  if (!heroLayerA || !heroLayerB || heroImages.length <= 1) {
    return;
  }

  let activeLayer = heroLayerA;
  let inactiveLayer = heroLayerB;
  let heroIndex = 0;

  landingAppState.heroIntervalId = window.setInterval(() => {
    heroIndex = (heroIndex + 1) % heroImages.length;
    inactiveLayer.src = heroImages[heroIndex];
    inactiveLayer.classList.add("is-active");
    activeLayer.classList.remove("is-active");
    [activeLayer, inactiveLayer] = [inactiveLayer, activeLayer];
  }, 3000);
};

const initHeroParallax = () => {
  const heroSection = document.getElementById("home");
  const heroVisual = document.querySelector("[data-hero-parallax]");
  const prefersReducedMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)",
  );

  if (!heroSection || !heroVisual || prefersReducedMotion.matches) {
    heroVisual?.style.removeProperty("--hero-parallax-y");
    return;
  }

  const updateParallax = () => {
    landingAppState.heroParallaxFrameId = null;

    const sectionRect = heroSection.getBoundingClientRect();
    const viewportHeight = window.innerHeight || 1;

    if (sectionRect.bottom < 0 || sectionRect.top > viewportHeight) {
      return;
    }

    const maxOffset = Math.min(viewportHeight * 0.14, 150);
    const progress = Math.min(
      1,
      Math.max(0, -sectionRect.top / Math.max(sectionRect.height, 1)),
    );
    const offset = Math.round(progress * maxOffset);

    heroVisual.style.setProperty("--hero-parallax-y", `${offset}px`);
  };

  const requestParallaxUpdate = () => {
    if (landingAppState.heroParallaxFrameId) {
      return;
    }

    landingAppState.heroParallaxFrameId =
      window.requestAnimationFrame(updateParallax);
  };

  requestParallaxUpdate();
  bindEvent(window, "scroll", requestParallaxUpdate, { passive: true });
  bindEvent(window, "resize", requestParallaxUpdate);
  bindEvent(prefersReducedMotion, "change", () => {
    if (prefersReducedMotion.matches) {
      heroVisual.style.removeProperty("--hero-parallax-y");
      return;
    }

    requestParallaxUpdate();
  });
};

const initLandingPage = () => {
  cleanupLandingPage();

  const assetBase = document.body.dataset.landingAssetBase || "/landing/assets";
  const contactUrl = document.body.dataset.landingContactUrl || "/contact";

  initRevealObserver();
  initHeader();

  createSectionSwiper(
    ".lineup-swiper",
    ".lineup-pagination",
    ".lineup-prev",
    ".lineup-next",
    {
      0: { slidesPerView: 1.15, spaceBetween: 18 },
      640: { slidesPerView: 2.1, spaceBetween: 22 },
      1024: { slidesPerView: 4, spaceBetween: 24 },
      1280: { slidesPerView: 4.2, spaceBetween: 24 },
    },
  );

  createSectionSwiper(
    ".ticket-swiper",
    ".ticket-pagination",
    ".ticket-prev",
    ".ticket-next",
    {
      0: { slidesPerView: 1.1, spaceBetween: 18 },
      640: { slidesPerView: 2, spaceBetween: 20 },
      1024: { slidesPerView: 2.6, spaceBetween: 22 },
      1280: { slidesPerView: 3.55, spaceBetween: 22 },
    },
  );

  createSectionSwiper(
    ".merch-swiper",
    ".merch-pagination",
    ".merch-prev",
    ".merch-next",
    {
      0: { slidesPerView: 1.15, spaceBetween: 16 },
      640: { slidesPerView: 2.2, spaceBetween: 18 },
      1024: { slidesPerView: 3, spaceBetween: 20 },
      1280: { slidesPerView: 4, spaceBetween: 20 },
    },
  );

  createSectionSwiper(
    ".moments-swiper",
    ".moments-pagination",
    ".moments-prev",
    ".moments-next",
    {
      0: { slidesPerView: 1.1, spaceBetween: 18 },
      640: { slidesPerView: 2, spaceBetween: 22 },
      1024: { slidesPerView: 2.6, spaceBetween: 24 },
      1280: { slidesPerView: 3.2, spaceBetween: 24 },
    },
  );

  initTicketModal();
  initMerchModal(assetBase, contactUrl);
  initHeroRotation(assetBase);
  initHeroParallax();
};

if (!window.__landingAppBooted) {
  window.__landingAppBooted = true;

  document.addEventListener("livewire:navigating", cleanupLandingPage);
  document.addEventListener("livewire:navigated", initLandingPage);

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initLandingPage, { once: true });
  } else {
    initLandingPage();
  }
}
