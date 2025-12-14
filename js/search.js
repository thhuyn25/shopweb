document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('search-form');
    const input = document.getElementById('search-input');
    const resultDiv = document.getElementById('search-results');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const query = input.value.trim();
        if (query === '') {
            resultDiv.innerHTML = '<p>Vui lòng nhập từ khóa.</p>';
            return;
        }

        fetch('/shopweb/fontend/search.php?q=' + encodeURIComponent(query))
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                resultDiv.innerHTML = '<p>Có lỗi xảy ra khi tìm kiếm.</p>';
                console.error(error);
            });
    });
});
