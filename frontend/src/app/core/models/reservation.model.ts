import { Room } from './room.model';
import { Service } from './service.model';
import { User } from './user.model';

export type ReservationStatus = 'pending' | 'confirmed' | 'checked_in' | 'checked_out' | 'cancelled';

export interface ReservationService {
  id: number;
  quantity: number;
  price: number;
  service: Service | null;
}

export interface Reservation {
  id: number;
  reservation_number: string;
  check_in_date: string;
  check_out_date: string;
  number_of_adults: number;
  number_of_children: number;
  total_price: number;
  status: ReservationStatus;
  special_requests: string | null;
  created_at: string;
  user: User | null;
  room: Room | null;
  services: ReservationService[];
}
