/**
 * Photo de secours par type de chambre, utilisée tant qu'aucune photo réelle n'a été
 * téléversée pour une chambre donnée (voir RoomThumbnail). Crédits : Wikimedia Commons
 * (licences libres CC0 / CC BY-SA), voir frontend/public/images/CREDITS.md.
 */
const ROOM_TYPE_IMAGES: Record<string, string> = {
  standard: '/images/rooms/standard.jpg',
  deluxe: '/images/rooms/deluxe.jpg',
  'suite junior': '/images/rooms/suite-junior.jpg',
  'suite presidentielle': '/images/rooms/suite-presidentielle.jpg',
  familiale: '/images/rooms/familiale.jpg',
};

export function roomTypeImage(name: string | null | undefined): string | null {
  if (!name) return null;
  const normalized = name
    .toLowerCase()
    .normalize('NFD')
    .replace(/[̀-ͯ]/g, '');
  return ROOM_TYPE_IMAGES[normalized] ?? null;
}
