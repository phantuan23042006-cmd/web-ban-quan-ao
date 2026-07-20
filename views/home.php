<?php

$user = $_SESSION['user'] ?? [];

$fullName = trim($user['full_name'] ?? 'Khách hàng');
$userEmail = trim($user['email'] ?? '');
$userAddress = trim($user['address'] ?? '');

if (!function_exists('homeEscape')) {
    function homeEscape($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

?>

<style>
    .home-page {
        display: grid;
        gap: 34px;
    }

    /*
    |--------------------------------------------------------------------------
    | BANNER CHÍNH
    |--------------------------------------------------------------------------
    */

    .home-hero {
        position: relative;
        min-height: 470px;
        padding: 62px 58px;
        display: flex;
        align-items: center;
        overflow: hidden;
        border-radius: 28px;
        background:
            linear-gradient(
                110deg,
                rgba(15, 23, 42, 0.96) 0%,
                rgba(15, 23, 42, 0.85) 45%,
                rgba(124, 58, 237, 0.48) 100%
            ),
            url("<?= BASE_URL ?>assets/uploads/home-banner.jpg")
                center / cover no-repeat;
        box-shadow: 0 25px 55px rgba(15, 23, 42, 0.16);
    }

    .home-hero::before {
        content: "";
        position: absolute;
        top: -130px;
        right: -100px;
        width: 390px;
        height: 390px;
        border-radius: 50%;
        background: rgba(219, 39, 119, 0.17);
        filter: blur(10px);
    }

    .home-hero::after {
        content: "";
        position: absolute;
        right: 160px;
        bottom: -190px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: rgba(124, 58, 237, 0.24);
        filter: blur(10px);
    }

    .home-hero-content {
        position: relative;
        z-index: 2;
        max-width: 620px;
    }

    .home-welcome {
        margin-bottom: 20px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.9);
        font-size: 13px;
        font-weight: 700;
        backdrop-filter: blur(10px);
    }

    .home-welcome-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #4ade80;
        box-shadow: 0 0 0 5px rgba(74, 222, 128, 0.15);
    }

    .home-hero h1 {
        margin: 0;
        color: #ffffff;
        font-size: clamp(39px, 5vw, 64px);
        line-height: 1.08;
        letter-spacing: -1.5px;
    }

    .home-hero h1 span {
        color: #e9d5ff;
    }

    .home-hero-description {
        max-width: 565px;
        margin: 24px 0 0;
        color: rgba(255, 255, 255, 0.77);
        font-size: 17px;
        line-height: 1.8;
    }

    .home-hero-actions {
        margin-top: 34px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 13px;
    }

    .home-primary-button,
    .home-secondary-button {
        min-height: 50px;
        padding: 0 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        border-radius: 13px;
        font-size: 15px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.22s ease;
    }

    .home-primary-button {
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
        box-shadow: 0 13px 28px rgba(124, 58, 237, 0.35);
    }

    .home-primary-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 17px 32px rgba(124, 58, 237, 0.42);
    }

    .home-secondary-button {
        border: 1px solid rgba(255, 255, 255, 0.23);
        background: rgba(255, 255, 255, 0.09);
        color: #ffffff;
        backdrop-filter: blur(10px);
    }

    .home-secondary-button:hover {
        background: rgba(255, 255, 255, 0.16);
        transform: translateY(-2px);
    }

    /*
    |--------------------------------------------------------------------------
    | CAM KẾT DỊCH VỤ
    |--------------------------------------------------------------------------
    */

    .home-benefits {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .home-benefit-card {
        min-height: 128px;
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
        transition: 0.22s ease;
    }

    .home-benefit-card:hover {
        border-color: #ddd6fe;
        transform: translateY(-4px);
        box-shadow: 0 15px 34px rgba(15, 23, 42, 0.09);
    }

    .home-benefit-icon {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        border-radius: 15px;
        background: #f3e8ff;
        font-size: 23px;
    }

    .home-benefit-content h3 {
        margin: 0 0 6px;
        color: #0f172a;
        font-size: 15px;
    }

    .home-benefit-content p {
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | TIÊU ĐỀ KHU VỰC
    |--------------------------------------------------------------------------
    */

    .home-section {
        display: grid;
        gap: 22px;
    }

    .home-section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .home-section-heading-content span {
        display: block;
        margin-bottom: 7px;
        color: #7c3aed;
        font-size: 12px;
        font-weight: 850;
        letter-spacing: 1.2px;
        text-transform: uppercase;
    }

    .home-section-heading h2 {
        margin: 0;
        color: #0f172a;
        font-size: 29px;
        letter-spacing: -0.5px;
    }

    .home-section-heading p {
        max-width: 620px;
        margin: 8px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .home-view-all {
        flex-shrink: 0;
        color: #7c3aed;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
    }

    .home-view-all:hover {
        color: #6d28d9;
        text-decoration: underline;
    }

    /*
    |--------------------------------------------------------------------------
    | DANH MỤC
    |--------------------------------------------------------------------------
    */

    .home-category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .home-category-card {
        position: relative;
        min-height: 245px;
        padding: 24px;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
        border-radius: 20px;
        color: #ffffff;
        text-decoration: none;
        box-shadow: 0 13px 34px rgba(15, 23, 42, 0.11);
        transition: 0.25s ease;
    }

    .home-category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 42px rgba(15, 23, 42, 0.18);
    }

    .home-category-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(
                180deg,
                transparent 25%,
                rgba(15, 23, 42, 0.83) 100%
            );
    }

    .home-category-men {
        background:
            linear-gradient(135deg, #475569, #0f172a);
    }

    .home-category-women {
        background:
            linear-gradient(135deg, #ec4899, #7c3aed);
    }

    .home-category-accessory {
        background:
            linear-gradient(135deg, #f59e0b, #b45309);
    }

    .home-category-new {
        background:
            linear-gradient(135deg, #06b6d4, #2563eb);
    }

    .home-category-decoration {
        position: absolute;
        top: -25px;
        right: -18px;
        font-size: 125px;
        opacity: 0.19;
        transform: rotate(-8deg);
    }

    .home-category-content {
        position: relative;
        z-index: 2;
    }

    .home-category-content span {
        display: block;
        margin-bottom: 7px;
        color: rgba(255, 255, 255, 0.75);
        font-size: 12px;
        font-weight: 700;
    }

    .home-category-content h3 {
        margin: 0;
        font-size: 24px;
    }

    .home-category-content p {
        margin: 9px 0 0;
        color: rgba(255, 255, 255, 0.76);
        font-size: 13px;
        line-height: 1.6;
    }

    /*
    |--------------------------------------------------------------------------
    | SẢN PHẨM MẪU
    |--------------------------------------------------------------------------
    */

    .home-product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .home-product-card {
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
        transition: 0.23s ease;
    }

    .home-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 17px 38px rgba(15, 23, 42, 0.11);
    }

    .home-product-image {
        position: relative;
        height: 270px;
        display: grid;
        place-items: center;
        overflow: hidden;
        background:
            linear-gradient(
                145deg,
                #f1f5f9,
                #e9d5ff
            );
        font-size: 92px;
    }

    .home-product-image::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            radial-gradient(
                circle at top right,
                rgba(255, 255, 255, 0.8),
                transparent 42%
            );
    }

    .home-product-badge {
        position: absolute;
        top: 13px;
        left: 13px;
        z-index: 2;
        padding: 7px 10px;
        border-radius: 999px;
        background: #0f172a;
        color: #ffffff;
        font-size: 10px;
        font-weight: 850;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .home-product-favorite {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 2;
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.65);
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.82);
        cursor: pointer;
        font-size: 17px;
        backdrop-filter: blur(8px);
    }

    .home-product-content {
        padding: 18px;
    }

    .home-product-category {
        color: #7c3aed;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .home-product-name {
        margin: 8px 0 0;
        color: #0f172a;
        font-size: 16px;
        line-height: 1.45;
    }

    .home-product-rating {
        margin-top: 9px;
        color: #f59e0b;
        font-size: 13px;
    }

    .home-product-rating span {
        color: #94a3b8;
    }

    .home-product-bottom {
        margin-top: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .home-product-price {
        color: #db2777;
        font-size: 17px;
        font-weight: 850;
    }

    .home-add-cart {
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        border: 0;
        border-radius: 12px;
        background: #f3e8ff;
        color: #6d28d9;
        cursor: pointer;
        font-size: 17px;
        transition: 0.2s ease;
    }

    .home-add-cart:hover {
        background: #7c3aed;
        color: #ffffff;
    }

    /*
    |--------------------------------------------------------------------------
    | THÔNG TIN TÀI KHOẢN
    |--------------------------------------------------------------------------
    */

    .home-account-banner {
        padding: 32px;
        display: grid;
        grid-template-columns: 1.35fr 0.65fr;
        align-items: center;
        gap: 30px;
        overflow: hidden;
        border-radius: 22px;
        background:
            linear-gradient(
                135deg,
                #ede9fe,
                #fce7f3
            );
    }

    .home-account-content h2 {
        margin: 0;
        color: #0f172a;
        font-size: 27px;
    }

    .home-account-content p {
        max-width: 650px;
        margin: 11px 0 0;
        color: #64748b;
        line-height: 1.75;
    }

    .home-account-information {
        margin-top: 21px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .home-account-information span {
        padding: 9px 12px;
        border: 1px solid rgba(124, 58, 237, 0.15);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.62);
        color: #475569;
        font-size: 12px;
    }

    .home-account-action {
        display: flex;
        justify-content: flex-end;
    }

    .home-account-action a {
        min-height: 48px;
        padding: 0 21px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #0f172a;
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        text-decoration: none;
        transition: 0.2s ease;
    }

    .home-account-action a:hover {
        background: #7c3aed;
        transform: translateY(-2px);
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    @media (max-width: 1050px) {
        .home-benefits,
        .home-category-grid,
        .home-product-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 760px) {
        .home-hero {
            min-height: 430px;
            padding: 45px 30px;
        }

        .home-account-banner {
            grid-template-columns: 1fr;
        }

        .home-account-action {
            justify-content: flex-start;
        }

        .home-section-heading {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 560px) {
        .home-page {
            gap: 28px;
        }

        .home-hero {
            min-height: 430px;
            padding: 36px 22px;
            border-radius: 20px;
        }

        .home-hero h1 {
            font-size: 37px;
        }

        .home-hero-description {
            font-size: 15px;
        }

        .home-hero-actions {
            align-items: stretch;
            flex-direction: column;
        }

        .home-primary-button,
        .home-secondary-button {
            width: 100%;
        }

        .home-benefits,
        .home-category-grid,
        .home-product-grid {
            grid-template-columns: 1fr;
        }

        .home-category-card {
            min-height: 210px;
        }

        .home-product-image {
            height: 245px;
        }

        .home-account-banner {
            padding: 25px 21px;
        }
    }
</style>

<div class="home-page">

    <!-- Banner chính -->
    <section class="home-hero">
        <div class="home-hero-content">
            <div class="home-welcome">
                <span class="home-welcome-dot"></span>

                Xin chào,
                <?= homeEscape($fullName) ?>
            </div>

            <h1>
                Thời trang mới,
                <span>phong cách mới</span>
            </h1>

            <p class="home-hero-description">
                Khám phá những sản phẩm thời trang hiện đại,
                lựa chọn màu sắc và kích thước phù hợp với phong cách
                của riêng bạn.
            </p>

            <div class="home-hero-actions">
                <a
                    class="home-primary-button"
                    href="<?= BASE_URL ?>?action=products"
                >
                    Mua sắm ngay
                    <span>→</span>
                </a>

                <a
                    class="home-secondary-button"
                    href="<?= BASE_URL ?>?action=orders"
                >
                    Xem đơn hàng
                </a>
            </div>
        </div>
    </section>

    <!-- Cam kết dịch vụ -->
    <section class="home-benefits">
        <article class="home-benefit-card">
            <div class="home-benefit-icon">
                🚚
            </div>

            <div class="home-benefit-content">
                <h3>Giao hàng nhanh</h3>

                <p>
                    Giao hàng thuận tiện trên toàn quốc.
                </p>
            </div>
        </article>

        <article class="home-benefit-card">
            <div class="home-benefit-icon">
                ↩
            </div>

            <div class="home-benefit-content">
                <h3>Đổi trả dễ dàng</h3>

                <p>
                    Hỗ trợ đổi sản phẩm theo chính sách.
                </p>
            </div>
        </article>

        <article class="home-benefit-card">
            <div class="home-benefit-icon">
                🔒
            </div>

            <div class="home-benefit-content">
                <h3>Thanh toán an toàn</h3>

                <p>
                    Hỗ trợ COD và chuyển khoản.
                </p>
            </div>
        </article>

        <article class="home-benefit-card">
            <div class="home-benefit-icon">
                ☎
            </div>

            <div class="home-benefit-content">
                <h3>Hỗ trợ khách hàng</h3>

                <p>
                    Tiếp nhận và giải đáp yêu cầu nhanh chóng.
                </p>
            </div>
        </article>
    </section>

    <!-- Danh mục -->
    <section class="home-section">
        <div class="home-section-heading">
            <div class="home-section-heading-content">
                <span>Danh mục nổi bật</span>

                <h2>Mua sắm theo phong cách</h2>

                <p>
                    Lựa chọn danh mục phù hợp để tìm kiếm sản phẩm
                    nhanh chóng hơn.
                </p>
            </div>

            <a
                class="home-view-all"
                href="<?= BASE_URL ?>?action=categories"
            >
                Xem tất cả →
            </a>
        </div>

        <div class="home-category-grid">
            <a
                class="home-category-card home-category-men"
                href="<?= BASE_URL ?>?action=products&category=men"
            >
                <span class="home-category-decoration">
                    👔
                </span>

                <div class="home-category-content">
                    <span>Phong cách hiện đại</span>
                    <h3>Thời trang nam</h3>
                    <p>Áo, quần và phụ kiện dành cho nam.</p>
                </div>
            </a>

            <a
                class="home-category-card home-category-women"
                href="<?= BASE_URL ?>?action=products&category=women"
            >
                <span class="home-category-decoration">
                    👗
                </span>

                <div class="home-category-content">
                    <span>Thanh lịch và trẻ trung</span>
                    <h3>Thời trang nữ</h3>
                    <p>Trang phục đa dạng cho mọi phong cách.</p>
                </div>
            </a>

            <a
                class="home-category-card home-category-accessory"
                href="<?= BASE_URL ?>?action=products&category=accessory"
            >
                <span class="home-category-decoration">
                    👜
                </span>

                <div class="home-category-content">
                    <span>Điểm nhấn hoàn hảo</span>
                    <h3>Phụ kiện</h3>
                    <p>Túi, mũ và nhiều phụ kiện thời trang.</p>
                </div>
            </a>

            <a
                class="home-category-card home-category-new"
                href="<?= BASE_URL ?>?action=products&sort=newest"
            >
                <span class="home-category-decoration">
                    ✨
                </span>

                <div class="home-category-content">
                    <span>Cập nhật liên tục</span>
                    <h3>Hàng mới về</h3>
                    <p>Khám phá những sản phẩm vừa ra mắt.</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Sản phẩm nổi bật -->
    <section class="home-section">
        <div class="home-section-heading">
            <div class="home-section-heading-content">
                <span>Sản phẩm đề xuất</span>

                <h2>Sản phẩm nổi bật</h2>

                <p>
                    Danh sách hiện là dữ liệu giao diện mẫu.
                    Sau khi làm chức năng sản phẩm, phần này sẽ lấy dữ liệu
                    trực tiếp từ cơ sở dữ liệu.
                </p>
            </div>

            <a
                class="home-view-all"
                href="<?= BASE_URL ?>?action=products"
            >
                Xem tất cả →
            </a>
        </div>

        <div class="home-product-grid">
            <article class="home-product-card">
                <div class="home-product-image">
                    <span class="home-product-badge">
                        Mới
                    </span>

                    <button
                        class="home-product-favorite"
                        type="button"
                        title="Yêu thích"
                    >
                        ♡
                    </button>

                    👕
                </div>

                <div class="home-product-content">
                    <span class="home-product-category">
                        Áo nam
                    </span>

                    <h3 class="home-product-name">
                        Áo thun nam cổ tròn basic
                    </h3>

                    <div class="home-product-rating">
                        ★★★★★
                        <span>(12 đánh giá)</span>
                    </div>

                    <div class="home-product-bottom">
                        <span class="home-product-price">
                            249.000đ
                        </span>

                        <button
                            class="home-add-cart"
                            type="button"
                            title="Thêm vào giỏ"
                            onclick="
                                alert(
                                    'Chức năng giỏ hàng sẽ được làm ở bước tiếp theo.'
                                );
                            "
                        >
                            🛒
                        </button>
                    </div>
                </div>
            </article>

            <article class="home-product-card">
                <div class="home-product-image">
                    <span class="home-product-badge">
                        Bán chạy
                    </span>

                    <button
                        class="home-product-favorite"
                        type="button"
                        title="Yêu thích"
                    >
                        ♡
                    </button>

                    👗
                </div>

                <div class="home-product-content">
                    <span class="home-product-category">
                        Váy nữ
                    </span>

                    <h3 class="home-product-name">
                        Váy nữ dáng dài thanh lịch
                    </h3>

                    <div class="home-product-rating">
                        ★★★★★
                        <span>(20 đánh giá)</span>
                    </div>

                    <div class="home-product-bottom">
                        <span class="home-product-price">
                            459.000đ
                        </span>

                        <button
                            class="home-add-cart"
                            type="button"
                            title="Thêm vào giỏ"
                            onclick="
                                alert(
                                    'Chức năng giỏ hàng sẽ được làm ở bước tiếp theo.'
                                );
                            "
                        >
                            🛒
                        </button>
                    </div>
                </div>
            </article>

            <article class="home-product-card">
                <div class="home-product-image">
                    <span class="home-product-badge">
                        Giảm giá
                    </span>

                    <button
                        class="home-product-favorite"
                        type="button"
                        title="Yêu thích"
                    >
                        ♡
                    </button>

                    👖
                </div>

                <div class="home-product-content">
                    <span class="home-product-category">
                        Quần jean
                    </span>

                    <h3 class="home-product-name">
                        Quần jean ống rộng cá tính
                    </h3>

                    <div class="home-product-rating">
                        ★★★★☆
                        <span>(8 đánh giá)</span>
                    </div>

                    <div class="home-product-bottom">
                        <span class="home-product-price">
                            389.000đ
                        </span>

                        <button
                            class="home-add-cart"
                            type="button"
                            title="Thêm vào giỏ"
                            onclick="
                                alert(
                                    'Chức năng giỏ hàng sẽ được làm ở bước tiếp theo.'
                                );
                            "
                        >
                            🛒
                        </button>
                    </div>
                </div>
            </article>

            <article class="home-product-card">
                <div class="home-product-image">
                    <span class="home-product-badge">
                        Phổ biến
                    </span>

                    <button
                        class="home-product-favorite"
                        type="button"
                        title="Yêu thích"
                    >
                        ♡
                    </button>

                    🧥
                </div>

                <div class="home-product-content">
                    <span class="home-product-category">
                        Áo khoác
                    </span>

                    <h3 class="home-product-name">
                        Áo khoác phong cách đường phố
                    </h3>

                    <div class="home-product-rating">
                        ★★★★★
                        <span>(16 đánh giá)</span>
                    </div>

                    <div class="home-product-bottom">
                        <span class="home-product-price">
                            599.000đ
                        </span>

                        <button
                            class="home-add-cart"
                            type="button"
                            title="Thêm vào giỏ"
                            onclick="
                                alert(
                                    'Chức năng giỏ hàng sẽ được làm ở bước tiếp theo.'
                                );
                            "
                        >
                            🛒
                        </button>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!-- Thông tin tài khoản -->
    <section class="home-account-banner">
        <div class="home-account-content">
            <h2>
                Tài khoản của <?= homeEscape($fullName) ?>
            </h2>

            <p>
                Cập nhật đầy đủ thông tin cá nhân để quá trình đặt hàng
                và nhận hàng diễn ra thuận tiện hơn.
            </p>

            <div class="home-account-information">
                <?php if ($userEmail !== ''): ?>
                    <span>
                        ✉ <?= homeEscape($userEmail) ?>
                    </span>
                <?php endif; ?>

                <span>
                    <?= $userAddress !== ''
                        ? '✓ Đã có địa chỉ nhận hàng'
                        : '⚠ Chưa cập nhật địa chỉ' ?>
                </span>
            </div>
        </div>

        <div class="home-account-action">
            <a href="<?= BASE_URL ?>?action=profile">
                Cập nhật hồ sơ
            </a>
        </div>
    </section>
</div>