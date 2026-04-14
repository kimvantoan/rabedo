import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(request: NextRequest) {
  // Check if they are trying to access admin
  if (request.nextUrl.pathname.startsWith('/admin')) {
    const token = request.cookies.get('admin-token');
    
    // Redirect if no auth token is found
    if (!token) {
      return NextResponse.redirect(new URL('/login', request.url));
    }
  }

  // Also block logged in users from seeing login again
  if (request.nextUrl.pathname.startsWith('/login')) {
      const token = request.cookies.get('admin-token');
      if (token) {
          return NextResponse.redirect(new URL('/admin', request.url));
      }
  }

  return NextResponse.next();
}

// Specify the paths to run the middleware on
export const config = {
  matcher: ['/admin/:path*', '/login'],
};
