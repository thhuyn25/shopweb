document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.querySelector(".sidebar");
  const contentWrapper = document.getElementById("content-wrapper");
  const sidebarToggle = document.getElementById("sidebarToggle");
  const topbar = document.getElementById("topbar");

  // Toggle sidebar
  sidebarToggle.addEventListener("click", () => {
    sidebar.classList.toggle("show");
    const isSidebarVisible = sidebar.classList.contains("show");
    contentWrapper.style.marginLeft = isSidebarVisible ? "0" : "270px";
    contentWrapper.style.width = isSidebarVisible
      ? "100%"
      : "calc(100% - 270px)";
    topbar.style.width = isSidebarVisible ? "100%" : "calc(100% - 270px)";
  });

  // Handle collapse behavior
  document.querySelectorAll(".nav-link").forEach((item) => {
    item.addEventListener("click", (e) => {
      if (item.getAttribute("data-bs-toggle") === "collapse") {
        e.preventDefault();
        const targetId = item.getAttribute("data-bs-target");
        const target = document.querySelector(targetId);
        if (target) {
          const collapse = new bootstrap.Collapse(target, { toggle: true });
          target.style.transition = "height 0.3s ease, opacity 0.3s ease";
        }
      }
    });
  });

  // Highlight active menu item
  const currentPage = window.location.pathname;
  document.querySelectorAll(".nav-link, .collapse-item").forEach((link) => {
    if (link.href === currentPage || link.href === window.location.href) {
      link.classList.add("active");
      const parentCollapse = link.closest(".collapse");
      if (parentCollapse) {
        new bootstrap.Collapse(parentCollapse, { show: true });
        const parentItem = link.closest(".nav-item");
        if (parentItem) parentItem.classList.add("active");
      }
    }
  });

  // Animation for fade-in effects
  const animateElements = document.querySelectorAll(
    ".nav-link, .collapse-item"
  );
  animateElements.forEach((el, index) => {
    requestAnimationFrame(() => {
      el.style.animationDelay = `${index * 0.1}s`;
      el.classList.add("animate-fadeIn");
    });
  });
});
