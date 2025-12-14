document.addEventListener("DOMContentLoaded", () => {
  const mainImage = document.getElementById("mainImage");

  document.querySelectorAll(".thumbnail").forEach((thumb) => {
    thumb.addEventListener("click", function () {
      mainImage.src = this.src;
      document.querySelectorAll(".thumbnail").forEach((el) => el.classList.remove("active"));
      this.classList.add("active");
    });
  });

  document.querySelectorAll(".size-option").forEach((btn) => {
    btn.addEventListener("click", function () {
      document.querySelectorAll(".size-option").forEach((b) => b.classList.remove("selected"));
      this.classList.add("selected");
      document.getElementById("selected-size").value = this.textContent;
    });
  });

  document.querySelector('form#add-to-cart-form').addEventListener("submit", function (e) {
    const sizeInput = document.getElementById("selected-size");
    if (sizeInput && !sizeInput.value) {
      e.preventDefault();
      alert("Vui lòng chọn kích thước.");
    }
  });

  document.querySelectorAll(".quantity-selector button").forEach((btn) => {
    btn.addEventListener("click", function () {
      const quantityDiv = document.getElementById("quantity");
      let current = parseInt(quantityDiv.textContent);
      current = isNaN(current) ? 1 : current;
      let delta = this.textContent === "+" ? 1 : -1;
      let newQty = Math.min(Math.max(current + delta, 1), 100);
      quantityDiv.textContent = newQty;
      document.getElementById("quantity-input").value = newQty;
    });
  });

  mainImage.addEventListener("click", () => {
    const img = mainImage.cloneNode();
    img.classList.add("fullscreen-img");

    const overlay = document.createElement("div");
    overlay.style.cssText = `
      position:fixed;top:0;left:0;width:100vw;height:100vh;
      background-color:rgba(0,0,0,0.95);display:flex;
      align-items:center;justify-content:center;z-index:9999`;

    const closeBtn = document.createElement("button");
    closeBtn.className = "close-btn";
    closeBtn.innerHTML = `
      <svg fill="#fff" viewBox="-3.5 0 19 19" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
        <path d="M11.383 13.644A1.03 1.03 0 0 1 9.928 15.1L6 11.172 2.072 15.1a1.03 1.03 0 1 1-1.455-1.456l3.928-3.928L.617 5.79a1.03 1.03 0 1 1 1.455-1.456L6 8.261l3.928-3.928a1.03 1.03 0 0 1 1.455 1.456L7.455 9.716z"></path>
      </svg>`;
    closeBtn.onclick = () => document.body.removeChild(overlay);

    overlay.appendChild(closeBtn);
    overlay.appendChild(img);
    document.body.appendChild(overlay);
  });
});
