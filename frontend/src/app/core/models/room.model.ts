export interface RoomType {
  id: number;
  name: string;
  description: string | null;
  base_price: number;
  max_occupancy: number;
  image: string | null;
  rooms_count?: number;
}

export interface RoomImage {
  id: number;
  image_path: string;
  url: string;
}

export type RoomStatus = 'available' | 'occupied' | 'maintenance' | 'out_of_service';

export interface Room {
  id: number;
  room_number: string;
  room_type_id: number;
  floor: number;
  price_per_night: number;
  max_occupancy: number;
  status: RoomStatus;
  room_type: RoomType | null;
  images: RoomImage[];
}
