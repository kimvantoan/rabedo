@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-8 flex items-center justify-between">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ isset($article) ? 'Chỉnh sửa bài viết' : 'Viết bài mới' }}</h1>
        <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900">Quay lại Dashboard</a>
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
            <label for="thumbnail" class="block text-sm font-medium text-gray-700">Ảnh đại diện (Thumbnail)</label>
            <div class="mt-1">
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border bg-gray-50" onchange="previewThumbnail(event)">
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
            <p class="mt-2 text-sm text-gray-500">Hỗ trợ soạn thảo phong phú qua Quill JS.</p>
        </div>

        <div class="pt-5 border-t">
            <div class="flex justify-end">
                <button type="button" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Hủy</button>
                <button type="submit" class="ml-3 inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">{{ isset($article) ? 'Cập nhật bài viết' : 'Lưu bài viết' }}</button>
            </div>
        </div>
    </form>
</div>
</div>

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
@endsection
