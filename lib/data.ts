const baseArticles = [
  {
    id: "1",
    title: "Trí tuệ nhân tạo (AI) đang định hình tương lai như thế nào?",
    description: "Cùng tìm hiểu sâu về cách AI đang làm thay đổi thế giới kỹ thuật số của chúng ta và những tác động trực tiếp đến người dùng mỗi ngày.",
    category: "Công nghệ",
    type: "Admin",
    content: `
      <p>Trí tuệ nhân tạo (AI) không còn là một khái niệm khoa học viễn tưởng. Nó đang hiện diện trong mọi ngóc ngách của cuộc sống kỹ thuật số, từ những thuật toán gợi ý video trên YouTube đến các hệ thống tự động hóa công việc phức tạp tại các doanh nghiệp lớn.</p>
      <p>Sự trỗi dậy của các mô hình ngôn ngữ lớn (LLMs) như GPT-4 đã chứng minh khả năng học hỏi và sáng tạo không giới hạn của máy móc. Tuy nhiên, cùng với đó là nhiều thách thức về mặt đạo đức và quyền riêng tư.</p>
      <h2>Tương lai sẽ ra sao?</h2>
      <p>Chắc chắn rằng AI sẽ tiếp tục phát triển với tốc độ chóng mặt. Những công việc mang tính lặp đi lặp lại sẽ dần được tự động hóa hoàn toàn. Thay vì thay thế con người, AI sẽ trở thành một người đồng hành mạnh mẽ, giúp chúng ta đạt được hiệu suất công việc cao hơn.</p>
      <blockquote>"Công nghệ không phải là định mệnh, công nghệ là công cụ nằm trong tay chúng ta."</blockquote>
      <p>Chúng ta cần học cách làm chủ công nghệ thay vì sợ hãi nó. Bằng việc trang bị những kiến thức nền tảng, mọi người có thể tự tin bước vào một thế giới nơi AI là trung tâm của sự đổi mới.</p>
    `,
    thumbnail: "https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=1600&h=900",
    slug: "ai-dinh-hinh-tuong-lai",
    date: "14 tháng 4, 2026",
    author: "Nguyễn Văn A",
  },
  {
    id: "2",
    title: "Cẩm nang thiết kế Web theo phong cách hiện đại năm 2026",
    description: "Khám phá các nguyên tắc cốt lõi giúp bạn tạo ra những ứng dụng web đẹp mắt, nhiều tính năng và lấy người dùng làm trung tâm.",
    category: "Cẩm nang",
    type: "AI",
    content: `
      <p>Thiết kế web không chỉ dừng lại ở mặt thẩm mỹ. Đó là câu chuyện về trải nghiệm người dùng (UX) và khả năng tiếp cận (Accessibility).</p>
      <p>Năm 2026 chứng kiến sự lên ngôi của các xu hướng tối giản, sử dụng các dải màu nổi bật kết hợp cùng typography to và rõ ràng. Người dùng ngày nay mong muốn một giao diện sạch sẽ, tải trang nhanh và nội dung dễ tiêu thụ.</p>
      <h2>Các yếu tố quan trọng</h2>
      <ul>
        <li><strong>Tốc độ tải trang:</strong> Ảnh hưởng trực tiếp đến tỷ lệ chuyển đổi.</li>
        <li><strong>Responsive Design:</strong> Hiển thị hoàn hảo trên mọi thiết bị.</li>
        <li><strong>Hiệu ứng vi mô (Micro-interactions):</strong> Tạo cảm giác phấn khích và sống động.</li>
      </ul>
      <p>Hãy bắt đầu xây dựng chiến lược thiết kế của bạn với một tư duy lấy người dùng làm trung tâm.</p>
    `,
    thumbnail: "https://images.unsplash.com/photo-1547658719-da2b51159128?auto=format&fit=crop&q=80&w=1600&h=900",
    slug: "cam-nang-thiet-ke-web-hien-dai",
    date: "12 tháng 4, 2026",
    author: "Hệ thống AI",
  },
  {
    id: "3",
    title: "Làm chủ React Server Components từ số không",
    description: "Hướng dẫn toàn diện về cách tận dụng sức mạnh của React Server Components nhằm tối ưu hóa hiệu suất website và điểm số SEO.",
    category: "Lập trình",
    type: "Admin",
    content: `
      <p>React Server Components (RSC) là một bước ngoặt kiến trúc lớn đối với hệ sinh thái React. Bạn không cần phải gửi toàn bộ JavaScript xuống client nữa. RSC cho phép bạn render các component linh hoạt ngay trên server và gửi HTML tĩnh về client.</p>
      <p>Khi sử dụng RSC, bạn có thể dễ dàng truy xuất trực tiếp vào cơ sở dữ liệu hoặc hệ thống tệp tin tại server. Quá trình này giúp nâng cao tính bảo mật và cải thiện tối đa các chỉ số Core Web Vitals của Google.</p>
      <h2>Bắt đầu như thế nào?</h2>
      <p>Với Next.js, RSC được kích hoạt mặc định trong App Router. Tất cả những gì bạn cần làm là thay thế các hooks truyền thống bằng việc fetch trực tiếp dữ liệu ở Root Component.</p>
    `,
    thumbnail: "https://images.unsplash.com/photo-1555099962-4199c345e5dd?auto=format&fit=crop&q=80&w=1600&h=900",
    slug: "lam-chu-react-server-components",
    date: "10 tháng 4, 2026",
    author: "Lê Hoàng C",
  },
  {
    id: "4",
    title: "Tại sao Typography lại vô cùng quan trọng trong thiết kế UI/UX",
    description: "Font chữ tốt là yếu tố cốt lõi để mang đến trải nghiệm tuyệt vời. Bạn sẽ được tìm hiểu nghệ thuật lựa chọn và phối hợp các font chữ hiệu quả.",
    category: "Cẩm nang",
    type: "AI",
    content: `
      <p>Bạn có biết rằng 90% giao diện kỹ thuật số là văn bản? Vì vậy, việc lựa chọn Typography tốt chính là nền tảng của một thiết kế xuất sắc.</p>
      <p>Typography không chỉ đơn giản là chọn một font chữ. Nó là việc bạn sử dụng kích thước, độ dày, khoảng cách dòng (line-height) sao cho mắt người dùng có thể lướt qua nội dung một cách tự nhiên và thoải mái nhất.</p>
      <h2>Những quy tắc vàng</h2>
      <p>Luôn đảm bảo độ tương phản màu sắc giữa văn bản và nền tảng. Hạn chế sử dụng quá 2 loại font chữ trên cùng một dự án. Bạn có thể chọn một font Serif cho tiêu đề và font Sans-serif cho nội dung văn bản.</p>
    `,
    thumbnail: "https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&q=80&w=1600&h=900",
    slug: "tai-sao-typography-quan-trong",
    date: "8 tháng 4, 2026",
    author: "Hệ thống AI",
  },
  {
    id: "5",
    title: "Xây dựng kiến trúc mở rộng Scalable Architecture cho Startup",
    description: "Giới thiệu những best practice và thiết kế để xây dựng hệ thống backend chịu tải cao, sẵn sàng bứt phá ngay từ những ngày đầu khởi nghiệp.",
    category: "Hệ thống",
    type: "Admin",
    content: `
      <p>Ngày đầu khi xây dựng sản phẩm, các startup thường có xu hướng tập trung vào tính năng (Go-to-market). Tuy nhiên, khi sản phẩm bất ngờ "viral," kiến trúc không tối ưu sẽ lập tức sụp đổ.</p>
      <p>Bài viết này phân tích tầm quan trọng của việc xây dựng Microservices kết hợp với Serverless để giải quyết bài toán chi phí ở giai đoạn đầu, nhưng vẫn hoàn toàn sẵn sàng trước các đợt lưu lượng truy cập lớn.</p>
      <h2>Mô hình cơ bản</h2>
      <p>Hãy phân tách hệ thống caching (như Redis) ra khỏi database chính kết hợp với quy tắc chia nhỏ dịch vụ logic. Đó là bước đi an toàn và hiệu quả về mặt lâu dài.</p>
    `,
    thumbnail: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1600&h=900",
    slug: "scalable-architecture-cho-startup",
    date: "5 tháng 4, 2026",
    author: "Vũ Minh E",
  },
  {
    id: "6",
    title: "Phân tích sâu về các điểm mới mẻ trong Tailwind CSS v4",
    description: "Chưa bao giờ việc thiết kế UI lại đơn giản đến thế. Trải nghiệm những tính năng và tối ưu hóa vượt bậc trong bản phát hành Tailwind v4.",
    category: "Cẩm nang",
    type: "AI",
    content: `
      <p>Tailwind v4 đã chính thức ra mắt, thay đổi hoàn toàn cục diện so với các phiên bản trước nhờ việc chuyển đổi kiến trúc biên dịch bằng Rust. Kết quả? Tốc độ build nhanh gấp hàng chục lần.</p>
      <p>Phiên bản mới mang lại hệ thống không gian màu sắc linh hoạt (OKLCH color space) giúp các màu sắc hiển thị sinh động và thống nhất hơn rất nhiều so với RGB truyền thống.</p>
      <h2>OKLCH là gì?</h2>
      <p>Bạn không cần phải đoán hay thiết lập mã hex cực nhọc nữa. OKLCH cho phép bạn tăng hoặc giảm độ sáng cũng như độ chói của một màu duy nhất trong khi giữ nguyên tông màu cốt lõi. Hãy cùng bắt đầu thử nghiệm nó ngay hôm nay!</p>
    `,
    thumbnail: "https://images.unsplash.com/photo-1526040652367-600053e1b72e?auto=format&fit=crop&q=80&w=1600&h=900",
    slug: "phan-tich-tailwind-css-v4",
    date: "1 tháng 4, 2026",
    author: "Hệ thống AI",
  },
];

// Generate 45 more dummy articles for pagination testing
const generatedArticles = Array.from({ length: 45 }).map((_, index) => {
  const isAI = index % 4 === 0; // 25% AI
  return {
    id: `g-${index}`,
    title: `[Chuyên đề ${index + 1}] Báo cáo phân tích chuyên sâu về thị trường công nghệ và xu hướng phát triển`,
    description: `Báo cáo chi tiết số ${index + 1} tập trung phân tích các khía cạnh thị trường...`,
    category: index % 3 === 0 ? "Công nghệ" : index % 3 === 1 ? "Kinh doanh" : "Bảo mật",
    type: isAI ? "AI" : "Admin",
    content: `<p>Đây là nội dung báo cáo phân tích tổng hợp từ các bản tin nội bộ. Bài viết số ${index + 1}.</p>`,
    thumbnail: "https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&q=80&w=1600&h=900",
    slug: `chuyen-de-phan-tich-${index + 1}`,
    date: `${(index % 28) + 1} tháng 3, 2026`,
    author: isAI ? "Hệ thống AI" : "Admin Team",
  };
});

export const dummyArticles = [...baseArticles, ...generatedArticles];
