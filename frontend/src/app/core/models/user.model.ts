export type UserRole = 'client' | 'receptionist' | 'admin';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  phone: string | null;
  address: string | null;
  email_verified_at: string | null;
  created_at?: string;
}
