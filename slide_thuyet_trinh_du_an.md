# BÀI THUYẾT TRÌNH BÁO CÁO DỰ ÁN
## ĐỀ TÀI: XÂY DỰNG WEBSITE BÁN QUẦN ÁO THỜI TRANG (TAN & TUAN CLOTHING)

---

> [!NOTE]
> Tài liệu này được biên soạn đầy đủ 11 Slide báo cáo dự án bao gồm: **Nội dung hiển thị trên Slide** và **Lời thoại thuyết trình chi tiết (Speaker Notes)** cho từng slide. Bạn có thể sử dụng trực tiếp để thuyết trình trước Hội đồng hoặc Giảng viên.

---

````carousel
### SLIDE 1: TRANG BÌA BÁO CÁO

#### 📌 Nội dung hiển thị trên Slide:
* **Tên đề tài**: BÁO CÁO DỰ ÁN XÂY DỰNG WEBSITE BÁN QUẦN ÁO THỜI TRANG PHỐ
* **Thương hiệu**: TAN & TUAN CLOTHING
* **Công nghệ ứng dụng**: PHP thuần (Pure PHP) + MySQL PDO + Kiến trúc MVC
* **Thực hiện bởi**: Sinh viên thực hiện
* **Giảng viên hướng dẫn**: [Tên Giảng viên hướng dẫn]

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Kính chào thầy cô và các bạn! Em xin đại diện nhóm trình bày báo cáo dự án tốt nghiệp/kết thúc học phần với đề tài: **Xây dựng Website bán quần áo thời trang TAN & TUAN CLOTHING**. Dự án của chúng em được phát triển dựa trên nền tảng ngôn ngữ PHP thuần kết hợp với cơ sở dữ liệu MySQL theo mô hình kiến trúc MVC chuẩn hóa. Sau đây em xin phép bắt đầu phần trình bày của mình."*

<!-- slide -->

### SLIDE 2: LÝ DO CHỌN ĐỀ TÀI & MỤC TIÊU DỰ ÁN

#### 📌 Nội dung hiển thị trên Slide:
* **Tính cấp thiết**:
  * Ngành thời trang Streetwear phát triển mạnh mẽ, nhu cầu mua sắm trực tuyến tăng cao.
  * Các cửa hàng cần hệ thống bán hàng trực quan, quản lý size và tồn kho chính xác theo thời gian thực.
* **Mục tiêu dự án**:
  * **Đối với Khách hàng**: Trải nghiệm giao diện streetwear hiện đại, mượt mà, dễ dàng tìm kiếm, chọn size và đặt hàng.
  * **Đối với Quản trị viên (Admin)**: Quản lý danh mục, sản phẩm, biến thể (Size/Kho/Giá), đơn hàng và doanh thu hiệu quả.
  * **Về mặt kỹ thuật**: Áp dụng chuẩn mô hình MVC, tối ưu bảo mật (CSRF, PDO Prepared Statements) và xử lý triệt để các kịch bản tồn kho thực tế.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Thưa thầy cô, thương mại điện tử ngành thời trang đòi hỏi một giao diện bắt mắt và khả năng quản lý sản phẩm có nhiều biến thể như Size S, M, L, XL rất phức tạp. Mục tiêu của nhóm chúng em khi thực hiện dự án này là xây dựng một hệ thống website hoàn chỉnh từ giao diện Khách hàng mang phong cách Streetwear Bad Habits hiện đại, cho đến hệ thống Admin quản trị kho hàng thông minh, giúp tự động hoàn tồn kho khi hủy đơn và xử lý chính xác 100% dữ liệu."*

<!-- slide -->

### SLIDE 3: CÔNG NGHỆ SỬ DỤNG & KIẾN TRÚC HỆ THỐNG

#### 📌 Nội dung hiển thị trên Slide:

| Thành phần | Công nghệ sử dụng | Mô tả chi tiết |
| :--- | :--- | :--- |
| **Backend** | PHP 8.x (Pure PHP) | Xử lý logic nghiệp vụ, điều hướng Route |
| **Database** | MySQL PDO | Kết nối dữ liệu an toàn qua Prepared Statements |
| **Kiến trúc** | MVC (Model - View - Controller) | Tách biệt Logic, Dữ liệu và Giao diện |
| **Frontend** | HTML5, CSS3, JavaScript | UI Streetwear, Google Fonts (Space Grotesk, Syne) |
| **Bảo mật** | CSRF Token, BCRYPT Hash | Chống tấn công CSRF, mã hóa mật khẩu an toàn |

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Về mặt công nghệ, dự án sử dụng PHP thuần và MySQL PDO. Chúng em lựa chọn mô hình kiến trúc MVC để mã nguồn được phân chia rõ ràng: Models quản lý truy vấn CSDL, Controllers xử lý logic nghiệp vụ, và Views đảm nhận hiển thị giao diện. Hệ thống cũng được trang bị các cơ chế bảo mật tiêu chuẩn như CSRF Token cho biểu mẫu và mã hóa mật khẩu BCRYPT."*

<!-- slide -->

### SLIDE 4: THIẾT KẾ CƠ SỞ DỮ LIỆU (DATABASE SCHEMA)

#### 📌 Nội dung hiển thị trên Slide:
* **Các bảng dữ liệu chính trong MySQL**:
  1. `users`: Lưu trữ tài khoản (Khách hàng & Admin, Mã hóa password).
  2. `san_pham`: Lưu thông tin chung của sản phẩm (Tên, hình ảnh, mô tả, danh mục).
  3. `chi_tiet_san_pham`: Bảng biến thể sản phẩm (Khóa ngoại trỏ đến `san_pham.id` - `ON DELETE CASCADE`, lưu Size, Giá bán riêng, Số lượng tồn kho).
  4. `orders` & `order_items`: Lưu thông tin đơn hàng và chi tiết sản phẩm được đặt.
  5. `categories`, `suppliers`, `reviews`, `password_resets`.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Đây là cấu trúc CSDL của dự án. Điểm nổi bật nhất là việc tách biệt bảng `san_pham` và bảng biến thể `chi_tiet_san_pham`. Điều này cho phép một sản phẩm có thể có nhiều Size khác nhau với giá bán và số lượng tồn kho riêng biệt. Ràng buộc khóa ngoại `ON DELETE CASCADE` đảm bảo khi xóa sản phẩm gốc thì các biến thể liên quan cũng được tự động dọn dẹp sạch sẽ."*

<!-- slide -->

### SLIDE 5: CHỨC NĂNG KHÁCH HÀNG (CLIENT SIDE) - GIAO DIỆN & MUA SẮM

#### 📌 Nội dung hiển thị trên Slide:
* **Trải nghiệm Giao diện (UI/UX)**:
  * Thiết kế theo phong cách Streetwear Bad Habits năng động.
  * Ticker thông báo khuyến mãi chạy ngang header, logo nhận diện TAN & TUAN CLOTHING.
* **Chức năng Khách hàng**:
  * Đăng ký, Đăng nhập, Đổi mật khẩu & Quên mật khẩu qua Token Email.
  * Trang chủ có Banner Hero, danh mục sản phẩm, bộ lọc và **phân trang tự động**.
  * Trang chi tiết sản phẩm: Chọn Size trực quan, tự động làm mờ (`disabled`) các Size đã hết hàng.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Về phía Khách hàng, giao diện được thiết kế hiện đại, ấn tượng ngay từ cái nhìn đầu tiên. Khách hàng có thể dễ dàng duyệt sản phẩm theo danh mục, phân trang mượt mà. Tại trang chi tiết sản phẩm, các Size đã hết hàng sẽ tự động bị ẩn hoặc mờ đi, giúp khách hàng không chọn lầm sản phẩm hết kho."*

<!-- slide -->

### SLIDE 6: GIỎ HÀNG & THANH TOÁN THÔNG MINH

#### 📌 Nội dung hiển thị trên Slide:
* **Chức năng Giỏ hàng (`Cart`)**:
  * Thêm sản phẩm theo Size đã chọn.
  * Kiểm tra tồn kho realtime trong DB mỗi khi mở giỏ hàng.
  * Cập nhật / Xóa sản phẩm trực tiếp.
* **Quy trình Thanh toán (`Checkout`)**:
  * Nhập thông tin nhận hàng (Họ tên, SĐT, Địa chỉ, Ghi chú).
  * Hỗ trợ 2 phương thức thanh toán:
    1. **COD**: Thanh toán khi nhận hàng.
    2. **Chuyển khoản QR Ngân hàng**: Mã QR Techcombank động theo mã đơn hàng.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Chức năng Giỏ hàng và Thanh toán được tối ưu rất kỹ. Khi khách hàng tiến hành thanh toán, hệ thống hỗ trợ 2 hình thức: COD truyền thống và Chuyển khoản QR Techcombank tiện lợi. Giỏ hàng cũng liên tục đối chiếu với kho thực tế để tránh tình trạng đặt hàng vượt quá số lượng khả dụng."*

<!-- slide -->

### SLIDE 7: CHỨC NĂNG QUẢN TRỊ (ADMIN DASHBOARD)

#### 📌 Nội dung hiển thị trên Slide:
* **Tổng quan Quản trị (`Dashboard`)**:
  * Thống kê tổng số sản phẩm, đơn hàng, khách hàng và tổng doanh thu.
* **Quản lý Biến thể Size / Tồn kho / Giá bán**:
  * Xem danh sách tất cả biến thể của 1 sản phẩm.
  * Thêm mới biến thể Size (S, M, L, XL...), sửa giá bán & cập nhật nhanh số lượng kho.
  * Cảnh báo tự động các sản phẩm **sắp hết hàng** (`Số lượng < 5`).
* **Quản lý Danh mục, Nhà cung cấp, Đánh giá sản phẩm & Tài khoản người dùng**.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Trang Admin cung cấp cho quản trị viên bộ công cụ quản lý toàn diện. Đặc biệt là tính năng Quản lý biến thể Size và Tồn kho: Admin có thể dễ dàng điều chỉnh số lượng tồn kho từng size, đặt giá bán riêng cho từng size và nhận được cảnh báo nổi bật khi tồn kho của một size xuống dưới 5 sản phẩm."*

<!-- slide -->

### SLIDE 8: QUẢN LÝ ĐƠN HÀNG & TỰ ĐỘNG HOÀN TỒN KHO

#### 📌 Nội dung hiển thị trên Slide:
* **Vòng đời Đơn hàng (Order Status Lifecycle)**:
  ```
  [Chờ xác nhận (Pending)] ──► [Xác nhận (Confirmed)] ──► [Đã giao (Completed)]
            │                                 │
            └───────────────► [Đã hủy (Cancelled)] ◄┘
  ```
* **Cơ chế Hoàn tồn kho tự động (Stock Rollback)**:
  * Khi đơn hàng chuyển sang trạng thái **Đã hủy** (do Khách hàng hủy hoặc Admin hủy):
  * Hệ thống chạy **Database Transaction** tự động cộng trả lại đúng số lượng tồn kho cho các biến thể Size tương ứng trong `chi_tiet_san_pham`.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Một điểm kỹ thuật quan trọng trong xử lý đơn hàng là tính năng Tự động hoàn lại tồn kho. Khi một đơn hàng bị hủy ở trạng thái Chờ xác nhận hoặc Xác nhận, hệ thống sẽ thực hiện một Transaction trong CSDL để cộng trả lại chính xác số lượng sản phẩm vào kho, giúp số lượng tồn kho luôn khớp 100% với thực tế mà không cần Admin phải chỉnh tay."*

<!-- slide -->

### SLIDE 9: XỬ LÝ 6 KỊCH BẢN TỒN KHO THỰC TẾ & BẢO MẬT

#### 📌 Nội dung hiển thị trên Slide:

```
1. SP đủ hàng ─────────────► Thêm giỏ thành công & tính chính xác dòng tiền
2. SP hết hàng ────────────► Chặn ngay từ giao diện & ném Exception
3. SP bị người khác mua hết ──► Tự cập nhật giỏ & cảnh báo tại trang Checkout
4. Giỏ hàng trống ─────────► Chặn Checkout & chuyển hướng về trang sản phẩm
5. Đặt quá tồn kho ────────► Chặn bởi SQL Atomic Update (so_luong >= qty)
6. Đơn hàng bị hủy ────────► Database Transaction khôi phục kho 100%
```

* **Bảo mật hệ thống**:
  * **Anti SQL Injection**: 100% truy vấn dùng PDO Prepared Statements.
  * **Anti XSS**: Mã hóa đầu ra HTML bằng hàm `e()` / `htmlspecialchars()`.
  * **Anti CSRF**: Kiểm tra Token cho tất cả biểu mẫu POST.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Để website đạt chất lượng thực tế, nhóm đã rà soát và xử lý triệt để 6 kịch bản tồn kho phát sinh trong mua sắm thực tế, ví dụ như trường hợp hai người cùng mua một sản phẩm cuối cùng, hay việc khách hàng cố tình thay đổi số lượng vượt quá kho. Đồng thời, toàn bộ truy vấn CSDL đều sử dụng Prepared Statements để ngăn chặn triệt để lỗ hổng SQL Injection."*

<!-- slide -->

### SLIDE KÌM THỬ PHẦN MỀM (SOFTWARE TESTING & QA)

#### 📌 Nội dung hiển thị trên Slide:
* **Phương pháp kiểm thử áp dụng**:
  * **Black-box Testing**: Kiểm thử chức năng giao diện Khách hàng & Quản trị Admin.
  * **Integration Testing**: Kiểm thử tích hợp giữa MVC Controllers, Models và CSDL MySQL PDO.
  * **Security Testing**: Kiểm thử bảo mật chống SQL Injection, XSS, CSRF và Hạn định Session.
* **Bảng kết quả kiểm thử các luồng trọng tâm**:

| Kịch bản Kiểm thử | Thao tác thực hiện | Kết quả mong đợi | Trạng thái |
| :--- | :--- | :--- | :---: |
| **Xác thực người dùng** | Đăng ký, Đăng nhập, Token Quên MK | Mã hóa BCRYPT, Token hết hạn chuẩn | **PASS** (100%) |
| **Tồn kho & Giỏ hàng** | Đặt quá kho, Mua SP hết hàng | Tự mờ Size (`disabled`), chặn đặt quá kho | **PASS** (100%) |
| **Hoàn kho khi hủy đơn** | Hủy đơn hàng (User / Admin) | Database Transaction tự cộng lại kho | **PASS** (100%) |
| **Phân quyền Route** | User truy cập trang Admin | Chuyển hướng chặn quyền truy cập trái phép | **PASS** (100%) |
| **Kiểm thử Bảo mật** | Chèn chuỗi SQLi, Script XSS | PDO Prepared Statements chặn 100% mã độc | **PASS** (100%) |

* **Đánh giá tổng quan**: Tổng số **45/45 Test Cases** đạt yêu cầu (100% Passed). System vận hành ổn định, không phát sinh lỗi xung đột dữ liệu.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Thưa thầy cô, để đảm bảo hệ thống vận hành ổn định trước khi đưa ra thị trường, nhóm chúng em đã xây dựng bộ kiểm thử phần mềm toàn diện với 45 Test Cases. Nhóm tập trung kiểm thử kỹ lưỡng 3 mảng chính: Kiểm thử chức năng nghiệp vụ Khách hàng/Admin, Kiểm thử 6 kịch bản tồn kho thực tế và Kiểm thử bảo mật an toàn dữ liệu. Kết quả cho thấy 100% Test Cases đều vượt qua (Passed), hệ thống ngăn chặn hoàn toàn các lỗi xung đột tồn kho và các lỗ hổng bảo mật như SQL Injection hay CSRF."*

<!-- slide -->

### SLIDE 10: KẾT QUẢ ĐẠT ĐƯỢC & ĐỊNH HƯỚNG PHÁT TRIỂN

#### 📌 Nội dung hiển thị trên Slide:
* **Kết quả đạt được**:
  * Hoàn thiện 100% các chức năng cốt lõi cho cả Khách hàng và Admin.
  * Giao diện độc đáo phong cách Streetwear TAN & TUAN CLOTHING.
  * Quản lý biến thể kho hàng và đơn hàng chính xác, ổn định.
* **Định hướng phát triển tương lai**:
  * Tích hợp cổng thanh toán trực tuyến tự động (VNPay / MoMo / ZaloPay).
  * Xây dựng hệ thống gợi ý sản phẩm thông minh dựa trên lịch sử mua hàng.
  * Tích hợp Chatbot AI hỗ trợ tư vấn chọn Size quần áo tự động cho khách hàng.

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Tóm lại, dự án đã hoàn thành đầy đủ các mục tiêu đề ra với một website bán hàng hoạt động mượt mà, giao diện ấn tượng và logic xử lý backend chặt chẽ. Trong tương lai, hệ thống có thể dễ dàng mở rộng để tích hợp thêm cổng thanh toán VNPay cũng như các tính năng tư vấn Size bằng AI."*

<!-- slide -->

### SLIDE 11: LỜI CẢM ƠN & HỎI ĐÁP (Q&A)

#### 📌 Nội dung hiển thị trên Slide:
* **XIN CHÂN THÀNH CẢM ƠN THẦY CÔ VÀ CÁC BẠN ĐÃ LẮNG NGHE!**
* **Dự án**: Website Bán Quần Áo Thời Trang TAN & TUAN CLOTHING
* **Hệ thống**: PHP Pure + MySQL PDO + MVC Architecture
* *Rất mong nhận được sự góp ý và câu hỏi từ Hội đồng!*

---

#### 🎙️ Lời thoại thuyết trình (Speaker Notes):
> *"Bài trình bày của em đến đây là kết thúc. Em xin chân thành cảm ơn thầy cô và các bạn đã chú ý lắng nghe. Em rất mong nhận được những nhận xét, góp ý và câu hỏi từ thầy cô trong Hội đồng để dự án của em được hoàn thiện hơn nữa. Em xin cảm ơn!"*
````
