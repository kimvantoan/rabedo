@extends('layouts.app')

@section('content')
<div class="flex min-h-full items-center justify-center py-12 px-4 sm_px-6 lg_px-8 mt-10">
  <div class="w-full max-w-md space-y-8 bg-white p-10 rounded-2xl shadow-sm border border-gray-100">
    <div>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">Đăng nhập Admin</h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Hoặc <a href="{{ url('/') }}" class="font-medium text-indigo-600 hover_text-indigo-500">về trang chủ</a>
      </p>
    </div>
    
    <form class="mt-8 space-y-6" action="{{ url('/login') }}" method="POST">
      @csrf
      
      @if ($errors->any())
        <div class="text-sm text-red-600 text-center">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
      @endif

      <div class="-space-y-px rounded-md shadow-sm">
        <div>
          <label for="email-address" class="sr-only">Email address</label>
          <input id="email-address" name="email" type="email" autocomplete="email" required class="relative block w-full appearance-none rounded-none rounded-t-md border border-gray-300 px-3 py-3 text-gray-900 placeholder-gray-500 focus_z-10 focus_border-indigo-500 focus_outline-none focus_ring-indigo-500 sm_text-sm" placeholder="Email">
        </div>
        <div>
          <label for="password" class="sr-only">Password</label>
          <input id="password" name="password" type="password" autocomplete="current-password" required class="relative block w-full appearance-none rounded-none rounded-b-md border border-gray-300 px-3 py-3 text-gray-900 placeholder-gray-500 focus_z-10 focus_border-indigo-500 focus_outline-none focus_ring-indigo-500 sm_text-sm" placeholder="Mật khẩu">
        </div>
      </div>

      <div>
        <button type="submit" class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-sm font-medium text-white hover_bg-indigo-700 focus_outline-none focus_ring-2 focus_ring-indigo-500 focus_ring-offset-2">
          Đăng nhập
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
