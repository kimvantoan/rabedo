@extends('layouts.app')

@section('title', 'Get In Touch - Rabedo')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm_px-6 lg_px-8 py-12">
    <div class="bg-white px-6 py-10 shadow-sm sm_rounded-lg sm_p-12 mb-8 border border-gray-100">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6 text-center">Get In Touch</h1>
        
        <div class="md_grid md_grid-cols-2 md_gap-12 mt-10">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Contact Information</h3>
                <p class="text-gray-600 mb-6">We are always happy to hear your feedback, suggestions, or partnership proposals. Do not hesitate to drop us a message!</p>
                
                <div class="space-y-4 text-gray-600">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>contact@rabedo.com</span>
                    </div>

                </div>
            </div>
            
            <div class="mt-10 md_mt-0">
                <form action="#" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus_border-blue-500 focus_ring-blue-500 sm_text-sm py-2 px-3 border" placeholder="John Doe">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus_border-blue-500 focus_ring-blue-500 sm_text-sm py-2 px-3 border" placeholder="email@example.com">
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="message" name="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus_border-blue-500 focus_ring-blue-500 sm_text-sm py-2 px-3 border" placeholder="How can we help you?"></textarea>
                    </div>
                    <div>
                        <button type="button" onclick="alert('Thank you for contacting us! We will get back to you shortly.')" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover_bg-blue-700 focus_outline-none focus_ring-2 focus_ring-offset-2 focus_ring-blue-500">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
