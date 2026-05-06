@extends('layouts.app')

@section('title', isset($chapter) ? 'Sửa Chương' : 'Tạo Chương Mới')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm_px-6 lg_px-8 py-8 mb-12">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($chapter) ? 'Sửa Chương' : 'Tạo Chương Mới' }}</h1>
            <p class="text-gray-500 mt-1">Truyện: {{ $article->title }}</p>
        </div>
        <div class="flex items-center space-x-4">
            @if(isset($chapter))
            <button type="button" onclick="copyShareUrl()" class="inline-flex items-center justify-center rounded-md border border-[#9d080a] bg-[#9d080a] px-3 py-1.5 text-sm font-medium text-white shadow-sm hover_bg-[#7a0608] transition" id="share-btn">
                <svg class="mr-1.5 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                <span id="share-text">Copy Link</span>
            </button>
            <script>
                function copyShareUrl() {
                    const shareUrl = '{{ rtrim(config("app.url"), "/") . route("articles.chapter", ["idOrSlug" => $article->id, "chapterNumber" => $chapter->chapter_number], false) }}/?utm_source={{ $article->user?->username ?? "admin" }}&utm_medium=social';
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
            @endif
            <a href="{{ route('admin.edit', $article->id) }}#chapters-section" class="text-indigo-600 hover_text-indigo-900 text-sm font-medium">Bỏ qua & Quay lại</a>
        </div>
    </div>

    @if ($errors->any())
    <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200">
        <div class="flex">
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Lỗi khi lưu chương:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ isset($chapter) ? route('admin.chapters.update', $chapter->id) : route('admin.chapters.store', $article->id) }}" method="POST" id="chapter-form" class="space-y-6 bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        @csrf
        @if(isset($chapter))
        @method('PUT')
        @endif

        <div class="grid grid-cols-1 md_grid-cols-4 gap-6">
            <div class="md_col-span-1">
                <label for="chapter_number" class="block text-sm font-medium text-gray-700">Số chương</label>
                <input type="number" name="chapter_number" id="chapter_number" value="{{ old('chapter_number', $chapter->chapter_number ?? $nextChapter ?? 1) }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus_border-indigo-500 focus_ring-indigo-500 sm_text-sm p-2 border bg-gray-50">
            </div>
            <div class="md_col-span-3">
                <label for="title" class="block text-sm font-medium text-gray-700">Tên chương (Ví dụ: Sự khởi đầu)</label>
                <input type="text" name="title" id="title" value="{{ old('title', $chapter->title ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus_border-indigo-500 focus_ring-indigo-500 sm_text-sm p-2 border bg-gray-50">
            </div>
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Nội dung HTML</label>
            <!-- Cần 1 div id=editor-container để Quill móc vào -->
            <div id="editor-container" class="rounded-md border border-gray-300 bg-white" style="height: 500px;">{!! old('content', $chapter->content ?? '') !!}</div>

            <!-- TextArea ẩn để submit form -->
            <textarea name="content" id="content-input" class="hidden">{{ old('content', $chapter->content ?? '') }}</textarea>
        </div>

        <div class="pt-5 flex justify-end gap-3">
            <a href="{{ route('admin.edit', $article->id) }}#chapters-section" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover_bg-gray-50">Hủy</a>
            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover_bg-indigo-700 focus_outline-none focus_ring-2 focus_ring-indigo-500 focus_ring-offset-2 transition-colors">
                Lưu chương
            </button>
        </div>
    </form>
</div>

<!-- Modal Thư viện Media cho màn hình chương -->
<div id="media-library-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50 transition-opacity">
    <div class="relative w-full max-w-4xl max-h-screen bg-white rounded-lg shadow-xl flex flex-col">
        <div class="flex items-center justify-between p-4 md_p-5 border-b rounded-t">
            <h3 class="text-xl font-semibold text-gray-900">Thư viện Media</h3>
            <button type="button" onclick="closeMediaLibrary()" class="text-gray-400 bg-transparent hover_bg-gray-200 hover_text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                </svg>
                <span class="sr-only">Đóng</span>
            </button>
        </div>
        <div class="p-4 md_p-5 flex-1 overflow-y-auto">
            <div id="media-grid" class="grid grid-cols-2 md_grid-cols-4 gap-4">
                <div class="col-span-full text-center text-gray-500 py-8">Đang tải...</div>
            </div>
        </div>
    </div>
</div>

<!-- Include thư viện Editor chuyên nghiệp giống trang chính -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    let currentImageSelection = null;
    let quillObj = null;

    document.addEventListener("DOMContentLoaded", function() {
        var toolbarOptions = [
            [{
                'header': [2, 3, 4, false]
            }],
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'script': 'sub'
            }, {
                'script': 'super'
            }],
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }],
            [{
                'direction': 'rtl'
            }],
            [{
                'color': []
            }, {
                'background': []
            }],
            [{
                'align': []
            }],
            ['link', 'image', 'video'],
            ['clean']
        ];

        var quill = new Quill('#editor-container', {
            modules: {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        image: imageHandler
                    }
                }
            },
            theme: 'snow',
            placeholder: 'Viết nội dung chương truyện ở đây ...'
        });
        quillObj = quill;

        function imageHandler() {
            var range = quill.getSelection();
            currentImageSelection = range;
            openMediaLibrary();
        }

        var form = document.querySelector('#chapter-form');
        form.onsubmit = function() {
            var contentInput = document.querySelector('#content-input');
            contentInput.value = quill.root.innerHTML;
        };
    });

    // Mượn API thư viện media sẵn có của AdminController
    let currentMediaPage = 1;
    let isLoadingMedia = false;

    function openMediaLibrary() {
        var modal = document.getElementById('media-library-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        currentMediaPage = 1;
        const grid = document.getElementById('media-grid');
        if (grid) grid.innerHTML = '<div id="media-loading" class="col-span-full text-center text-gray-500 py-8">Đang tải...</div>';
        loadMediaLibrary(currentMediaPage);
    }

    function loadMediaLibrary(page = 1) {
        if (isLoadingMedia) return;
        isLoadingMedia = true;
        let loadMoreBtn = document.getElementById('media-load-more');

        if (page > 1 && loadMoreBtn) {
            loadMoreBtn.innerText = 'Đang tải...';
            loadMoreBtn.disabled = true;
        }

        fetch(`{{ route('admin.media') }}?page=${page}`)
            .then(res => res.json())
            .then(data => {
                isLoadingMedia = false;
                const grid = document.getElementById('media-grid');
                const loading = document.getElementById('media-loading');
                if (loading) loading.remove();

                if (page === 1) grid.innerHTML = '';

                if (data.data && data.data.length > 0) {
                    data.data.forEach(url => {
                        const div = document.createElement('div');
                        div.className = 'relative group cursor-pointer border rounded bg-gray-100 aspect-square overflow-hidden hover_border-indigo-500';
                        div.onclick = function() {
                            selectMediaForQuill(url);
                        };

                        const img = document.createElement('img');
                        img.src = url;
                        img.loading = 'lazy';
                        img.className = 'w-full h-full object-cover';
                        div.appendChild(img);

                        const txt = document.createElement('div');
                        txt.className = 'absolute bottom-0 w-full bg-black bg-opacity-70 text-white text-[10px] truncate px-1 py-0.5 opacity-0 group-hover_opacity-100';
                        txt.innerText = url.split('/').pop();
                        div.appendChild(txt);

                        grid.appendChild(div);
                    });

                    if (data.has_more) {
                        if (!loadMoreBtn) {
                            const btnDiv = document.createElement('div');
                            btnDiv.className = 'col-span-full flex justify-center py-4 w-full';
                            btnDiv.innerHTML = `<button id="media-load-more" type="button" class="px-6 py-2 bg-indigo-50 hover_bg-indigo-100 text-indigo-700 rounded-md text-sm font-medium transition-colors border border-indigo-200" onclick="loadMoreMedia()">Tải thêm hình ảnh</button>`;
                            grid.appendChild(btnDiv);
                        } else {
                            loadMoreBtn.innerText = 'Tải thêm hình ảnh';
                            loadMoreBtn.disabled = false;
                            grid.appendChild(loadMoreBtn.parentElement);
                        }
                    } else if (loadMoreBtn && loadMoreBtn.parentElement) {
                        loadMoreBtn.parentElement.remove();
                    }
                } else if (page === 1) {
                    grid.innerHTML = '<div class="col-span-full py-10 text-center text-gray-500">Chưa có bức ảnh nào trong hệ thống. Hãy upload ảnh ở bài viết khác trước.</div>';
                }
            })
            .catch(err => {
                isLoadingMedia = false;
                document.getElementById('media-grid').innerHTML = '<div class="col-span-full text-center text-red-500">Lỗi tải dữ liệu thư viện.</div>';
            });
    }

    function loadMoreMedia() {
        currentMediaPage++;
        loadMediaLibrary(currentMediaPage);
    }

    function closeMediaLibrary() {
        var modal = document.getElementById('media-library-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentImageSelection = null;
    }

    function selectMediaForQuill(url) {
        if (currentImageSelection && quillObj) {
            quillObj.insertEmbed(currentImageSelection.index, 'image', url);
            quillObj.setSelection(currentImageSelection.index + 1);
        }
        closeMediaLibrary();
    }
</script>
@endsection