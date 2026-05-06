@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm_px-6 lg_px-8">
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ isset($article) ? 'Chỉnh sửa bài viết' : 'Viết bài mới' }}</h1>
        <div class="flex items-center space-x-4">
            @if(isset($article))
            <button type="button" onclick="copyShareUrl()" class="inline-flex items-center justify-center rounded-md border border-[#9d080a] bg-[#9d080a] px-4 py-2 text-sm font-medium text-white shadow-sm hover_bg-[#7a0608] transition" id="share-btn">
                <svg class="mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                <span id="share-text">Copy Link Share</span>
            </button>
            <script>
                function copyShareUrl() {
                    const shareUrl = '{{ rtrim(config("app.url"), "/") . route("articles.show", ["idOrSlug" => $article->id], false) }}/?utm_source={{ $article->user?->username ?? "admin" }}&utm_medium=social';
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
            <a href="{{ rtrim(config('app.url'), '/') . route('articles.show', [$article->id], false) }}" target="_blank" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover_bg-gray-50 transition">
                <svg class="mr-2 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                Xem bài viết
            </a>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-500 hover_text-gray-900 border-r border-gray-300 pr-4">Quay lại Dashboard</a>
            <form action="{{ route('logout') }}" method="POST" class="inline pl-4">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-600 hover_text-red-800">
                    Đăng xuất
                </button>
            </form>
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
                            @foreach (array_unique($errors->all()) as $error)
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
                <input type="text" name="title" id="title" class="block w-full rounded-md border-gray-300 shadow-sm focus_border-indigo-500 focus_ring-indigo-500 sm_text-sm p-3 border placeholder-gray-400" placeholder="Nhập tiêu đề..." value="{{ old('title', $article->title ?? '') }}" required>
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả ngắn (Description)</label>
            <div class="mt-1">
                <textarea name="description" id="description" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus_border-indigo-500 focus_ring-indigo-500 sm_text-sm p-3 border placeholder-gray-400" placeholder="Nhập mô tả ngắn hiện dưới tiêu đề...">{{ old('description', $article->description ?? '') }}</textarea>
            </div>
            <p class="mt-2 text-sm text-gray-500">Đoạn text ngắn hiển thị ngay dưới Tiêu đề và thay cho Meta Description.</p>
        </div>

        <div>
            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Ảnh đại diện (Thumbnail) <span class="text-red-500">*</span></label>
            <div class="mt-1 flex items-center space-x-4">
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="block w-full rounded-md border-gray-300 shadow-sm focus_border-indigo-500 focus_ring-indigo-500 sm_text-sm p-2 border bg-gray-50" onchange="previewThumbnail(event)">
                <button type="button" onclick="openMediaLibrary()" class="inline-flex items-center px-4 py-2 border border-blue-500 shadow-sm text-sm font-medium rounded-md text-blue-600 bg-blue-50 hover_bg-blue-100 focus_outline-none focus_ring-2 focus_ring-offset-2 focus_ring-blue-500 whitespace-nowrap transition-colors">
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

            @if($errors->has('thumbnail'))
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->first('thumbnail') }}</p>
            @elseif($errors->has('existing_thumbnail'))
            <p class="mt-2 text-sm text-red-600 font-medium">{{ $errors->first('existing_thumbnail') }}</p>
            @endif
            <p id="thumbnail-js-error" class="mt-2 text-sm text-red-600 font-medium hidden">Vui lòng tải lên hoặc chọn ảnh đại diện (thumbnail) từ thư viện.</p>
            <p id="thumbnail-size-error" class="mt-2 text-sm text-red-600 font-medium hidden">Ảnh tải lên vượt quá dung lượng cho phép (tối đa 5MB). Vui lòng chọn ảnh nhẹ hơn.</p>

            <script>
                function previewThumbnail(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Kiểm tra dung lượng 5MB (5 * 1024 * 1024 bytes)
                    if (file.size > 5242880) {
                        document.getElementById('thumbnail-size-error').classList.remove('hidden');
                        event.target.value = ''; // Xoá file đã chọn
                        document.getElementById('thumbnail-preview-container').style.display = 'none';
                        return;
                    } else {
                        document.getElementById('thumbnail-size-error').classList.add('hidden');
                    }

                    const reader = new FileReader();
                    reader.onload = function() {
                        const output = document.getElementById('thumbnail-preview');
                        output.src = reader.result;
                        document.getElementById('thumbnail-preview-container').style.display = 'block';
                        // Clear the existing hidden link to avoid ambiguity
                        document.getElementById('existing_thumbnail').value = '';
                    };
                    reader.readAsDataURL(file);
                }
            </script>
        </div>


        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Nội dung HTML</label>
            <div class="mt-1">
                <input type="hidden" name="content" id="content-input" value="{{ old('content', $article->content ?? '') }}">
                <div id="quill-editor" class="bg-white rounded-b-md border-gray-300 shadow-sm">{!! old('content', $article->content ?? '') !!}</div>
            </div>
            <span class="italic text-orange-600 font-medium">(Lưu ý: Không bắt buộc nhập nếu làm Truyện nhiều chương. Bạn cứ Lưu bài rồi cuộn để viết Chương ở bảng bên dưới).</span></p>
        </div>

        <div class="pt-5 border-t">
            <div class="flex justify-end">
                <button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover_bg-gray-50 focus_outline-none focus_ring-2 focus_ring-indigo-500 focus_ring-offset-2">Hủy</button>
                <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover_bg-indigo-700 focus_outline-none focus_ring-2 focus_ring-indigo-500 focus_ring-offset-2">{{ isset($article) ? 'Lưu Truyện/Bài viết' : 'Lưu Truyện/Bài viết' }}</button>
            </div>
        </div>
    </form>
</div>
</div>
</div>

@if(isset($article))
<div class="mx-auto max-w-5xl px-4 pb-8 sm_px-6 lg_px-8 mt-4" id="chapters-section">
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 border-t-4 border-t-indigo-600">
        <div class="flex flex-col sm_flex-row sm_justify-between sm_items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900 uppercase">CÁC CHƯƠNG/TẬP CỦA BÀI VIẾT NÀY</h2>
                <p class="text-sm text-gray-500 mt-1">Dành riêng cho việc đăng truyện nhiều kỳ.</p>
            </div>
            <a href="{{ route('admin.chapters.create', $article->id) }}" class="mt-4 sm_mt-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover_bg-indigo-700 transition border border-transparent shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
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
                    <tr class="hover_bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">#{{ $chapter->chapter_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 font-medium clamp-1">{{ $chapter->title }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $chapter->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-end space-x-4">
                                <a href="{{ rtrim(config('app.url'), '/') . route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $chapter->chapter_number], false) }}" class="text-indigo-600 hover_text-indigo-900" target="_blank" title="Xem Chapter">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>

                                <button type="button" onclick="copyListShareUrl(this, '{{ rtrim(config('app.url'), '/') . route('articles.chapter', ['idOrSlug' => $article->id, 'chapterNumber' => $chapter->chapter_number], false) }}?utm_source={{ $article->user?->username ?? 'admin' }}&utm_medium=social')" class="text-[#9d080a] hover_text-[#7a0608]" title="Copy Link Share">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                                    </svg>
                                </button>

                                <a href="{{ route('admin.chapters.edit', $chapter->id) }}" class="text-green-600 hover_text-green-900" title="Sửa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </a>

                                <form action="{{ route('admin.chapters.destroy', $chapter->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xoá chương này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover_text-red-900 shrink-0" title="Xoá">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
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
        [{
            header: [1, 2, 3, false]
        }],
        ["bold", "italic", "underline", "strike", "blockquote"],
        [{
            color: []
        }, {
            background: []
        }],
        [{
            align: []
        }],
        [{
            list: "ordered"
        }, {
            list: "bullet"
        }],
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

            if (file.size > 5242880) {
                alert('Ảnh tải lên vượt quá dung lượng cho phép (tối đa 5MB). Vui lòng chọn ảnh nhẹ hơn.');
                return;
            }

            const formData = new FormData();
            formData.append('upload', file);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("admin.upload_image") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.url) {
                        const range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', result.url);
                        quill.setSelection(range.index + 1);
                    } else if (result.error) {
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
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm_block sm_p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="closeMediaLibrary()"></div>
        <span class="hidden sm_inline-block sm_align-middle sm_h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm_my-8 sm_align-middle sm_max-w-5xl sm_w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm_p-6 sm_pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Cửa Sổ Chọn Ảnh Từ Thư Viện</h3>
                    <button type="button" class="text-gray-400 hover_text-gray-500" onclick="closeMediaLibrary()">
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

                <div id="media-grid" class="grid grid-cols-2 sm_grid-cols-3 md_grid-cols-4 lg_grid-cols-5 gap-4 max-h-[60vh] overflow-y-auto hidden p-2">
                    <!-- Images will be injected here via JS -->
                </div>
                <div id="media-empty" class="text-center py-10 hidden border-2 border-dashed border-gray-300 rounded-lg">
                    <p class="text-gray-500">Chưa có bức ảnh nào trong thư viện (bạn cần tự upload ảnh thumbnail hoặc ảnh chèn nội dung trước). </p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm_px-6 sm_flex sm_flex-row-reverse">
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover_bg-gray-50 focus_outline-none focus_ring-2 focus_ring-offset-2 focus_ring-indigo-500 sm_mt-0 sm_ml-3 sm_w-auto sm_text-sm transition-colors" onclick="closeMediaLibrary()">
                    Trở Lại
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentMediaPage = 1;
    let isLoadingMedia = false;

    function openMediaLibrary() {
        document.getElementById('media-library-modal').classList.remove('hidden');
        currentMediaPage = 1;
        const grid = document.getElementById('media-grid');
        if (grid) grid.innerHTML = '';
        loadMediaLibrary(currentMediaPage);
    }

    function closeMediaLibrary() {
        document.getElementById('media-library-modal').classList.add('hidden');
    }

    function loadMediaLibrary(page = 1) {
        if (isLoadingMedia) return;
        isLoadingMedia = true;

        const grid = document.getElementById('media-grid');
        const loading = document.getElementById('media-loading');
        const empty = document.getElementById('media-empty');
        let loadMoreBtn = document.getElementById('media-load-more');

        if (page === 1) {
            loading.classList.remove('hidden');
            grid.classList.add('hidden');
            empty.classList.add('hidden');
            if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
            grid.innerHTML = '';
        } else {
            if (loadMoreBtn) {
                loadMoreBtn.innerText = 'Đang tải...';
                loadMoreBtn.disabled = true;
            }
        }

        fetch(`{{ route("admin.media") }}?page=${page}`)
            .then(res => res.json())
            .then(result => {
                isLoadingMedia = false;
                if (page === 1) loading.classList.add('hidden');

                const images = result.data || [];
                if (page === 1 && images.length === 0) {
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
                    img.loading = 'lazy';
                    img.className = 'block object-cover w-full h-full transform group-hover_scale-110 transition duration-300 ease-out';

                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 bg-indigo-500 bg-opacity-0 group-hover_bg-opacity-20 transition duration-300 flex items-center justify-center';

                    div.appendChild(img);
                    div.appendChild(overlay);
                    grid.appendChild(div);
                });

                if (result.has_more) {
                    if (!loadMoreBtn) {
                        const btnDiv = document.createElement('div');
                        btnDiv.className = 'col-span-full flex justify-center py-4 w-full';
                        btnDiv.innerHTML = `<button id="media-load-more" type="button" class="px-6 py-2 bg-indigo-50 hover_bg-indigo-100 text-indigo-700 rounded-md text-sm font-medium transition-colors border border-indigo-200" onclick="loadMoreMedia()">Tải thêm hình ảnh</button>`;
                        grid.appendChild(btnDiv);
                        loadMoreBtn = document.getElementById('media-load-more');
                    } else {
                        loadMoreBtn.innerText = 'Tải thêm hình ảnh';
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.classList.remove('hidden');
                        grid.appendChild(loadMoreBtn.parentElement); // Move to the bottom
                    }
                } else if (loadMoreBtn && loadMoreBtn.parentElement) {
                    loadMoreBtn.parentElement.classList.add('hidden');
                }
            })
            .catch(err => {
                isLoadingMedia = false;
                if (page === 1) {
                    loading.classList.add('hidden');
                    empty.classList.remove('hidden');
                    empty.innerHTML = '<p class="text-red-500 px-4 py-2">Xảy ra lỗi mạng khi kết nối máy chủ Media. Hãy thử lại.</p>';
                } else {
                    if (loadMoreBtn) {
                        loadMoreBtn.innerText = 'Tải lại';
                        loadMoreBtn.disabled = false;
                    }
                    alert('Lỗi tải thêm ảnh. Vui lòng thử lại.');
                }
            });
    }

    function loadMoreMedia() {
        currentMediaPage++;
        loadMediaLibrary(currentMediaPage);
    }

    function selectMedia(url) {
        document.getElementById('existing_thumbnail').value = url;
        // Xoá nội dung file (nếu có) để nhường quyền ưu tiên cho ảnh lấy từ thư viện
        document.getElementById('thumbnail').value = '';

        // Hiển thị Preview
        const output = document.getElementById('thumbnail-preview');
        output.src = url;
        document.getElementById('thumbnail-preview-container').style.display = 'block';
        
        const errorP = document.getElementById('thumbnail-js-error');
        if (errorP) errorP.classList.add('hidden');

        closeMediaLibrary();
    }

    function copyListShareUrl(btn, url) {
        const textArea = document.createElement("textarea");
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            const originalHTML = btn.innerHTML;
            // Hiển thị icon Check xanh
            btn.innerHTML = `<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`;
            setTimeout(() => btn.innerHTML = originalHTML, 2000);
        } catch (err) {
            console.error('Lỗi copy', err);
        }
        document.body.removeChild(textArea);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[enctype="multipart/form-data"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                const fileInput = document.getElementById('thumbnail');
                const hiddenInput = document.getElementById('existing_thumbnail');
                
                if (!fileInput.value && !hiddenInput.value) {
                    e.preventDefault();
                    
                    const errorP = document.getElementById('thumbnail-js-error');
                    if (errorP) errorP.classList.remove('hidden');
                    
                    // Cuộn màn hình tới vị trí thẻ input ảnh
                    fileInput.parentElement.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
        
        // Ẩn lỗi khi người dùng thay đổi ảnh
        document.getElementById('thumbnail').addEventListener('change', function() {
             const errorP = document.getElementById('thumbnail-js-error');
             if (errorP) errorP.classList.add('hidden');
        });
    });
</script>
@endsection