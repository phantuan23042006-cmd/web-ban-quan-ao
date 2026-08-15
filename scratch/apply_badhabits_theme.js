const fs = require('fs');

const path = 'C:/laragon/www/DuAn1/views/main.php';
let content = fs.readFileSync(path, 'utf8');

// Replace top bar announcement with Bad Habits ticker
const oldTopBar = /<div class="user-topbar">[sS]*?</div>s*</div>/;
const newTopBar = `<div class="user-topbar" style="background:#090d16;color:#fff;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:8px 0;border-bottom:1px solid #1e293b">
            <div class="container" style="display:flex;justify-content:between;align-items:center;overflow:hidden">
                <div class="ticker-text" style="display:flex;gap:30px;white-space:nowrap;animation:marquee 25s linear infinite">
                    <span>🔥 BAD HABITS - DOUBLEBAD STUDIO</span>
                    <span>•</span>
                    <span>BASED IN SAIGON</span>
                    <span>•</span>
                    <span>MIỄN PHÍ VẬN CHUYỂN ĐƠN HÀNG TỪ 500.000Đ</span>
                    <span>•</span>
                    <span>BADDEST GEAR YOU WANT SO BAD</span>
                    <span>•</span>
                    <span>HOT STREETWEAR COLLECTION 2026</span>
                </div>
            </div>
        </div>`;

content = content.replace(oldTopBar, newTopBar);

// Replace Brand Header logo
const oldLogo = /<a class="brand"[sS]*?</a>/;
const newLogo = `<a class="brand" href="<?= BASE_URL ?>" style="display:flex;align-items:center;gap:10px;text-decoration:none">
                    <div style="background:#000;color:#fff;padding:6px 12px;border-radius:8px;font-family:'Space Grotesk',sans-serif;font-weight:900;font-size:18px;letter-spacing:1.5px;border:1px solid #334155;text-transform:uppercase">
                        BAD HABITS
                    </div>
                    <div style="display:flex;flex-direction:column">
                        <span style="font-family:'Space Grotesk',sans-serif;font-weight:800;font-size:15px;color:#0f172a;letter-spacing:1px;text-transform:uppercase">
                            DOUBLEBAD <span style="color:#e11d48">STUDIO</span>
                        </span>
                        <span style="font-size:10px;font-weight:700;color:#64748b;letter-spacing:0.5px;text-transform:uppercase">
                            Official Streetwear Store
                        </span>
                    </div>
                </a>`;

content = content.replace(oldLogo, newLogo);

// Replace Footer content with Bad Habits footer
const oldFooter = /<footer class="user-footer">[sS]*?</footer>/;
const newFooter = `<footer class="user-footer" style="background:#090d16;color:#f8fafc;padding:60px 0 20px;border-top:2px solid #000;margin-top:60px">
            <div class="container user-footer-main" style="display:grid;grid-template-columns:1.8fr 1fr 1fr 1.2fr;gap:40px">

                <div>
                    <div class="footer-brand" style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                        <div style="background:#fff;color:#000;padding:6px 12px;border-radius:8px;font-family:'Space Grotesk',sans-serif;font-weight:900;font-size:18px;letter-spacing:1px">
                            BAD HABITS
                        </div>
                        <span style="font-weight:800;font-size:16px;color:#ffffff;letter-spacing:1px;text-transform:uppercase">
                            DOUBLEBAD STUDIO
                        </span>
                    </div>

                    <p class="footer-description" style="color:#94a3b8;font-size:13.5px;line-height:1.7;margin-bottom:20px">
                        Bad Habits - Baddest gear that you want so bad. Explore a wide range of Streetwear clothing & accessories inspired by Vietnamese Culture. |Based in SAIGON|
                    </p>

                    <div style="display:flex;gap:12px;font-size:14px;color:#cbd5e1">
                        <span style="background:#1e293b;padding:6px 12px;border-radius:8px;font-weight:700">📍 SAIGON</span>
                        <span style="background:#1e293b;padding:6px 12px;border-radius:8px;font-weight:700">📍 HANOI</span>
                        <span style="background:#1e293b;padding:6px 12px;border-radius:8px;font-weight:700">📍 DANANG</span>
                    </div>
                </div>

                <div class="footer-column">
                    <h3 style="font-family:'Space Grotesk',sans-serif;text-transform:uppercase;font-size:14px;letter-spacing:1px;color:#fff;margin-bottom:16px">BỘ SƯU TẬP</h3>
                    <a href="<?= BASE_URL ?>?action=products" style="color:#94a3b8">Tất cả sản phẩm</a>
                    <a href="<?= BASE_URL ?>?action=products&sort=rating" style="color:#94a3b8">Đánh giá cao</a>
                    <a href="<?= BASE_URL ?>?action=categories" style="color:#94a3b8">Danh mục Streetwear</a>
                    <a href="<?= BASE_URL ?>?action=cart" style="color:#94a3b8">Giỏ hàng của bạn</a>
                </div>

                <div class="footer-column">
                    <h3 style="font-family:'Space Grotesk',sans-serif;text-transform:uppercase;font-size:14px;letter-spacing:1px;color:#fff;margin-bottom:16px">HỆ THỐNG STORE</h3>
                    <span style="color:#94a3b8;display:block;margin-bottom:8px">🏢 93 Đặng Văn Ngữ, P.14, Q.Phú Nhuận, TP.HCM</span>
                    <span style="color:#94a3b8;display:block;margin-bottom:8px">🏢 117 Trần Quang Diệu, P.14, Q.3, TP.HCM</span>
                    <span style="color:#94a3b8;display:block;margin-bottom:8px">🏢 Vincom Bà Triệu, Hà Nội</span>
                </div>

                <div class="footer-column">
                    <h3 style="font-family:'Space Grotesk',sans-serif;text-transform:uppercase;font-size:14px;letter-spacing:1px;color:#fff;margin-bottom:16px">LIÊN HỆ & BẢO HÀNH</h3>
                    <span style="color:#94a3b8;display:block;margin-bottom:6px">📞 Hotline: <strong>1900 63 60 99</strong></span>
                    <span style="color:#94a3b8;display:block;margin-bottom:6px">✉️ Email: support@badhabitsstore.vn</span>
                    <span style="color:#94a3b8;display:block;margin-bottom:6px">⏰ Giờ mở cửa: 09:30 - 21:30 hàng ngày</span>
                </div>

            </div>

            <div class="footer-bottom" style="border-top:1px solid #1e293b;margin-top:40px;padding-top:20px;background:#030712">
                <div class="container footer-bottom-inner" style="display:flex;justify-content:between;color:#64748b;font-size:12px">
                    <span>© <?= date('Y') ?> BAD HABITS OFFICIAL STORE — DOUBLEBAD STUDIO. All rights reserved.</span>
                    <span>CHÍNH HÃNG 100% · CAM KẾT ĐỔI TRẢ 7 NGÀY · SHIP COD TOÀN QUỐC</span>
                </div>
            </div>
        </footer>`;

content = content.replace(oldFooter, newFooter);

fs.writeFileSync(path, content, 'utf8');
console.log('main.php updated with Bad Habits header and footer');
