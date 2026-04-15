import { NextRequest, NextResponse } from 'next/server';
import prisma from '@/lib/prisma';
import bcrypt from 'bcryptjs';
import { signToken } from '@/lib/auth';

export async function POST(request: NextRequest) {
  try {
    const { email, password } = await request.json();

    if (!email || !password) {
      return NextResponse.json({ message: 'Email và mật khẩu là bắt buộc' }, { status: 400 });
    }

    const user = await prisma.users.findUnique({
      where: { email },
    });

    if (!user) {
      return NextResponse.json({ message: 'Email hoặc mật khẩu không chính xác.' }, { status: 401 });
    }

    const formattedPassword = user.password.replace(/^\$2y\$/, '$2a$');
    const passwordMatch = await bcrypt.compare(password, formattedPassword);

    if (!passwordMatch) {
      return NextResponse.json({ message: 'Email hoặc mật khẩu không chính xác.' }, { status: 401 });
    }

    // Passwords match in Node.js bcrypt for a Laravel bcrypt hash because it's standard bcrypt format ($2y$ can be handled or replaced with $2a$ if needed, but bcrypt package usually handles $2y$ transparently).
    
    // To properly convert BigInt to Number for JSON serialization since Prisma bigint returns a BigInt type
    const safeUser = {
      ...user,
      id: Number(user.id),
      created_at: user.created_at?.toISOString() ?? null,
      updated_at: user.updated_at?.toISOString() ?? null,
      email_verified_at: user.email_verified_at?.toISOString() ?? null,
    };

    const token = signToken({ id: safeUser.id, email: safeUser.email });

    return NextResponse.json({
      token,
      user: safeUser,
    });
  } catch (error: any) {
    return NextResponse.json({ message: 'Internal Server Error', error: error.message }, { status: 500 });
  }
}
