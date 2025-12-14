let currentIndex = 0;
let bannerScroll = null;
let totalSlides = 0;

function initializeBanner() {
    bannerScroll = document.getElementById('bannerScroll');
    totalSlides = document.querySelectorAll('.banner-slide').length;

    if (!bannerScroll || totalSlides === 0) {
        console.error("Banner scroll element or slides not found!");
        return;
    }

    // Tự động cuộn mỗi 5 giây
    setInterval(() => scrollBanner(1), 5000);
}

function scrollBanner(direction) {
    if (!bannerScroll) return;

    currentIndex += direction;
    if (currentIndex < 0) currentIndex = totalSlides - 1;
    if (currentIndex >= totalSlides) currentIndex = 0;

    const offset = -currentIndex * 100;
    bannerScroll.style.transform = `translateX(${offset}%)`;
}

// Gọi hàm khởi tạo khi trang tải xong
document.addEventListener('DOMContentLoaded', initializeBanner);