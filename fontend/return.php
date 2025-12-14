<?php
include '../database/dbhelper.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Chính sách đổi trả - Streetwear Shop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/shopweb/css/global.css">
    <link rel="stylesheet" href="/shopweb/css/infor-page.css">

</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section class="container my-5">
            <h1 class="text-center mb-4">Chính sách đổi trả</h1>
            <div class="shipping-content">
                <p>Chúng tôi luôn mong muốn mang đến cho quý khách hàng những trải nghiệm mua sắm tốt nhất. Vì vậy, chính sách đổi trả của chúng tôi được xây dựng với mục tiêu đảm bảo quyền lợi tối đa cho khách hàng trong trường hợp sản phẩm không như mong đợi.</p>
                <ol>
                    <li>
                        <h2>Điều kiện áp dụng đổi/trả hàng</h2>
                        <p>Khách hàng có thể yêu cầu đổi hoặc trả sản phẩm trong các trường hợp sau:</p>
                        <ul>
                            <li>Sản phẩm bị lỗi kỹ thuật do nhà sản xuất (rách, bung chỉ, phai màu, lem màu, hư dây kéo, v.v.).</li>
                            <li>Giao nhầm sản phẩm: sai mẫu, sai size, sai màu so với đơn đặt hàng.</li>
                            <li>Sản phẩm bị hư hỏng trong quá trình vận chuyển.</li>
                            <li>Khách hàng muốn đổi sang size khác hoặc mẫu khác (áp dụng tùy điều kiện từng sản phẩm và tùy tình trạng còn hàng).</li>
                        </ul>
                    </li>
                    <li>
                        <h2>Thời gian yêu cầu đổi/trả</h2>
                        <p>Trong vòng <strong>3 - 7 ngày</strong> kể từ ngày nhận hàng (tùy theo khu vực).</p>
                        <p>Sản phẩm phải còn nguyên tem, mác, chưa qua sử dụng, chưa giặt, không có mùi lạ hoặc dấu hiệu đã qua sử dụng.</p>
                        <p>Đối với sản phẩm bị lỗi, quý khách vui lòng chụp ảnh/video rõ ràng để chúng tôi xác minh tình trạng sản phẩm trước khi xử lý đổi trả.</p>
                    </li>
                    <li>
                        <h2>Quy trình đổi trả</h2>
                        <p>
                            <strong>Bước 1:</strong> Liên hệ bộ phận chăm sóc khách hàng qua fanpage, Zalo hoặc số điện thoại để gửi yêu cầu đổi trả.<br>
                            <strong>Bước 2:</strong> Cung cấp thông tin đơn hàng và hình ảnh sản phẩm (nếu có lỗi).<br>
                            <strong>Bước 3:</strong> Nhân viên xác nhận điều kiện và hướng dẫn quý khách gửi hàng về kho.<br>
                            <strong>Bước 4:</strong> Sau khi nhận lại hàng, chúng tôi sẽ kiểm tra và tiến hành:<br>
                        </p>
                        <ul>
                            <li>Gửi sản phẩm mới (nếu đổi).</li>
                            <li>Hoàn tiền qua phương thức thanh toán ban đầu hoặc chuyển khoản (nếu trả hàng).</li>
                        </ul>
                    </li>
                    <li>
                        <h2>Chi phí đổi/trả</h2>
                        <p>Miễn phí đổi trả nếu lỗi từ phía cửa hàng hoặc nhà sản xuất.</p>
                        <p>Trường hợp khách hàng muốn đổi do không vừa size, đổi ý, không thích,... khách sẽ tự thanh toán phí vận chuyển hai chiều.</p>
                    </li>
                    <li>
                        <h2>Lưu ý quan trọng</h2>
                        <p>Một số sản phẩm như đồ lót, phụ kiện, sản phẩm giảm giá mạnh, đồng giá, khuyến mãi đặc biệt không áp dụng đổi/trả. Vui lòng kiểm tra kỹ mô tả sản phẩm trước khi đặt hàng.</p>
                        <p>Hàng hóa đổi/trả phải được đóng gói cẩn thận khi gửi lại để tránh hư hỏng trong quá trình vận chuyển.</p>
                    </li>
                </ol>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>