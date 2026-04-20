@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ isset($article) ? 'Chỉnh sửa bài viết' : 'Viết bài mới' }}</h1>
        <div class="flex items-center space-x-4">
            @if(isset($article))
            <button type="button" onclick="copyShareUrl()" class="inline-flex items-center justify-center rounded-md border border-[#9d080a] bg-[#9d080a] px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-[#7a0608] transition" id="share-btn">
                <svg class="mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                <span id="share-text">Copy Link Share</span>
            </button>
            <script>
                function copyShareUrl() {
                    const shareUrl = '{{ route("articles.show", ["idOrSlug" => $article->id]) }}?utm_source=facebook&utm_medium=social&utm_content={{ $article->user?->username ?? "admin" }}';
                    const textArea = document.createElement("textarea");
                    textArea.value = shareUrl;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        const shareText = document.getElementById('share-text');
                        const originalText = shareText.innerText;
                        shareText.innerText = 'Đã Copy!';
                        setTimeout(() => shareText.innerText = originalText, 2000);
                    } catch (err) {}
                    document.body.removeChild(textArea);
                }
            </script>
            <a href="{{ route('articles.show', [$article->id]) }}" target="_blank" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition">
                <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Xem bài viết
            </a>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Quay lại Dashboard</a>
        </div>
    </div>

    <form action="{{ isset($article) ? route('admin.update', $article->id) : route('admin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        @csrf

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Có lỗi xảy ra:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề bài viết</label>
            <div class="mt-1">
                <input type="text" name="title" id="title" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border placeholder-gray-400" placeholder="Nhập tiêu đề..." value="{{ old('title', $article->title ?? '') }}" required>
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả ngắn (Description)</label>
            <div class="mt-1">
                <textarea name="description" id="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border placeholder-gray-400" placeholder="Nhập mô tả ngắn hiện dưới tiêu đề...">{{ old('description', $article->description ?? '') }}</textarea>
            </div>
            <p class="mt-2 text-sm text-gray-500">Đoạn text ngắn hiển thị ngay dưới Tiêu đề và thay cho Meta Description.</p>
        </div>

        <div>
            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Ảnh đại diện (Thumbnail)</label>
            <div class="mt-1 flex items-center space-x-4">
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border bg-gray-50" onchange="previewThumbnail(event)">
                <button type="button" onclick="openMediaLibrary()" class="inline-flex items-center px-4 py-2 border border-blue-500 shadow-sm text-sm font-medium rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 whitespace-nowrap transition-colors">
                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Chọn từ Thư Viện
                </button>
                <input type="hidden" name="existing_thumbnail" id="existing_thumbnail" value="{{ old('existing_thumbnail', (isset($article) && Str::startsWith($article->thumbnail, '/storage')) ? $article->thumbnail : '') }}">
            </div>
            
            <div class="mt-3" id="thumbnail-preview-container" style="display: {{ (isset($article) && !empty($article->thumbnail)) ? 'block' : 'none' }}">
                <p class="text-sm text-gray-500 mb-1">Ảnh hiện tại/Preview:</p>
                <img id="thumbnail-preview" src="{{ (isset($article) && !empty($article->thumbnail)) ? asset($article->thumbnail) : '' }}" alt="Thumbnail preview" class="h-32 w-auto rounded-lg object-cover border border-gray-200">
            </div>
            
            <script>
                function previewThumbnail(event) {
                    const reader = new FileReader();
                    reader.onload = function(){
                        const output = document.getElementById('thumbnail-preview');
                        output.src = reader.result;
                        document.getElementById('thumbnail-preview-container').style.display = 'block';
                        // Clear the existing hidden link to avoid ambiguity
                        document.getElementById('existing_thumbnail').value = '';
                    };
                    if(event.target.files[0]){
                        reader.readAsDataURL(event.target.files[0]);
                    }
                }
            </script>
        </div>


        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Nội dung HTML</label>
            <div class="mt-1">
                <input type="hidden" name="content" id="content-input" value="{{ old('content', $article->content ?? '') }}">
                <div id="quill-editor" class="bg-white rounded-b-md border-gray-300 shadow-sm">{!! old('content', $article->content ?? '') !!}</div>
            </div>
            <p class="mt-2 text-[13px] text-gray-500">Hỗ trợ soạn thảo phong phú qua Quill JS. <span class="italic text-orange-600 font-medium">(Lưu ý: Không bắt buộc nhập nếu làm Truyện nhiều chương. Bạn cứ Lưu bài rồi cuộn để viết Chương ở bảng bên dưới).</span></p>
        </div>

        <div class="pt-5 border-t">
            <div class="flex justify-end">
                <button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Hủy</button>
                <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ isset($article) ? 'Lưu Truyện/Bài viết' : 'Lưu Truyện/Bài viết' }}</button>
            </div>
        </div>
    </form>
</div>
</div>
</div>

@if(isset($article))
<div class="mx-auto max-w-5xl px-4 pb-8 sm:px-6 lg:px-8 mt-4" id="chapters-section">
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-indigo-600">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 uppercase">CÁC CHƯƠNG/TẬP CỦA BÀI VIẾT NÀY</h2>
                <p class="text-sm text-gray-500 mt-1">Dành riêng cho việc đăng truyện nhiều kỳ.</p>
            </div>
            <a href="{{ route('admin.chapters.create', $article->id) }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition border border-transparent shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tạo Chương Mới
            </a>
        </div>

        @if($article->chapters && $article->chapters->count() > 0)
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Chương</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tên chương</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ngày đăng</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($article->chapters as $chapter)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#{{ $chapter->chapter_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 font-medium clamp-1">{{ $chapter->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $chapter->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.chapters.edit', $chapter->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold px-2 py-1 rounded bg-indigo-50 hover:bg-indigo-100 transition inline-block mr-2">Sửa</a>
                            <form action="{{ route('admin.chapters.destroy', $chapter->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xoá chương này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold px-2 py-1 rounded bg-red-50 hover:bg-red-100 transition">Xoá</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-10 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Bài viết độc lập</h3>
            <p class="mt-1 text-sm text-gray-500">Bài này hiện chưa có chương nào. Ấn "Tạo Chương Mới" nếu bạn muốn biến nó thành Truyện nhiều tập.</p>
        </div>
        @endif
    </div>
</div>
@endif
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .ql-editor {
        min-height: 400px;
        font-family: inherit;
        font-size: 16px;
    }
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        background-color: #f9fafb;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
</style>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    const toolbarOptions = [
      [{ header: [1, 2, 3, false] }],
      ["bold", "italic", "underline", "strike", "blockquote"],
      [{ color: [] }, { background: [] }],
      [{ align: [] }],
      [{ list: "ordered" }, { list: "bullet" }],
      ["link", "image", "video"],
      ["clean"]
    ];

    const quill = new Quill('#quill-editor', {
        theme: 'snow',
        placeholder: 'Bắt đầu viết nội dung của bạn...',
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: {
                    image: imageHandler
                }
            }
        }
    });

    const contentInput = document.getElementById('content-input');
    
    // Đồng bộ HTML từ Quill sang thẻ input ẩn trước khi Submit
    quill.on('text-change', function() {
        contentInput.value = quill.root.innerHTML;
    });

    function imageHandler() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();

        input.onchange = () => {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('upload', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("admin.upload_image") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if(result.url) {
                    const range = quill.getSelection(true);
                    quill.insertEmbed(range.index, 'image', result.url);
                    quill.setSelection(range.index + 1);
                } else if(result.error) {
                    alert(result.error.message || 'Lỗi tải ảnh lên.');
                }
            })
            .catch(error => {
                console.error(error);
                alert('Có lỗi xảy ra khi tải ảnh lên.');
            });
        };
    }
</script>

<!-- Media Library Modal -->
<div id="media-library-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeMediaLibrary()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Cửa Sổ Chọn Ảnh Từ Thư Viện</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeMediaLibrary()">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div id="media-loading" class="text-center py-10">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-500 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-500 inline-block">Đang tải...</p>
                </div>
                
                <div id="media-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto hidden p-2">
                    <!-- Images will be injected here via JS -->
                </div>
                <div id="media-empty" class="text-center py-10 hidden border-2 border-dashed border-gray-300 rounded-lg">
                    <p class="text-gray-500">Chưa có bức ảnh nào trong thư viện (bạn cần tự upload ảnh thumbnail hoặc ảnh chèn nội dung trước). </p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors" onclick="closeMediaLibrary()">
                    Trở Lại
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openMediaLibrary() {
        document.getElementById('media-library-modal').classList.remove('hidden');
        loadMediaLibrary();
    }

    function closeMediaLibrary() {
        document.getElementById('media-library-modal').classList.add('hidden');
    }

    function loadMediaLibrary() {
        const grid = document.getElementById('media-grid');
        const loading = document.getElementById('media-loading');
        const empty = document.getElementById('media-empty');
        
        loading.classList.remove('hidden');
        grid.classList.add('hidden');
        empty.classList.add('hidden');
        grid.innerHTML = '';
        
        fetch('{{ route("admin.media") }}')
            .then(res => res.json())
            .then(result => {
                loading.classList.add('hidden');
                const images = result.data || [];
                if (images.length === 0) {
                    empty.classList.remove('hidden');
                    return;
                }
                
                grid.classList.remove('hidden');
                images.forEach(url => {
                    const div = document.createElement('div');
                    div.className = 'relative group rounded-md overflow-hidden border border-gray-200 cursor-pointer aspect-w-4 aspect-h-3 h-32 w-full';
                    div.onclick = () => selectMedia(url);
                    
                    const img = document.createElement('img');
                    img.src = url;
                    img.className = 'block object-cover w-full h-full transform group-hover:scale-110 transition duration-300 ease-out';
                    
                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 bg-indigo-500 bg-opacity-0 group-hover:bg-opacity-20 transition duration-300 flex items-center justify-center';
                    
                    div.appendChild(img);
                    div.appendChild(overlay);
                    grid.appendChild(div);
                });
            })
            .catch(err => {
                loading.classList.add('hidden');
                empty.classList.remove('hidden');
                empty.innerHTML = '<p class="text-red-500 px-4 py-2">Xảy ra lỗi mạng khi kết nối máy chủ Media. Hãy thử lại.</p>';
            });
    }

    function selectMedia(url) {
        document.getElementById('existing_thumbnail').value = url;
        // Xoá nội dung file (nếu có) để nhường quyền ưu tiên cho ảnh lấy từ thư viện
        document.getElementById('thumbnail').value = '';
        
        // Hiển thị Preview
        const output = document.getElementById('thumbnail-preview');
        output.src = url;
        document.getElementById('thumbnail-preview-container').style.display = 'block';
        
        closeMediaLibrary();
    }
</script>
@endsection
