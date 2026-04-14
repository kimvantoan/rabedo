import { NextRequest, NextResponse } from 'next/server';
import { getUserFromRequest } from '@/lib/auth';

export async function GET(request: NextRequest) {
  try {
    const user = await getUserFromRequest(request);

    if (!user) {
      return NextResponse.json({ message: 'Unauthorized' }, { status: 401 });
    }

    const safeUser = {
      ...user,
      id: Number(user.id),
      created_at: user.created_at?.toISOString() ?? null,
      updated_at: user.updated_at?.toISOString() ?? null,
      email_verified_at: user.email_verified_at?.toISOString() ?? null,
    };

    return NextResponse.json(safeUser);
  } catch (error: any) {
    return NextResponse.json({ message: 'Internal Server Error' }, { status: 500 });
  }
}
