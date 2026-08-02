// À adapter avant le build de production : URL de l'API Laravel déployée
// (ex. Railway/Render). Cette valeur est figée au moment du build (Angular
// n'a pas de variables d'environnement runtime sans configuration supplémentaire).
export const environment = {
  production: true,
  apiUrl: 'https://votre-backend.exemple.com/api',
};
