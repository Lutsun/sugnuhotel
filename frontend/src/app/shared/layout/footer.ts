import { Component } from '@angular/core';

@Component({
  selector: 'app-footer',
  template: `
    <footer class="bg-slate-900 text-slate-300 mt-16">
      <div class="max-w-6xl mx-auto px-4 py-8 text-sm flex flex-col sm:flex-row justify-between gap-4">
        <p>&copy; {{ year }} SugnuHotel. Tous droits réservés.</p>
        <p>Dakar, Sénégal</p>
      </div>
    </footer>
  `,
})
export class Footer {
  protected readonly year = new Date().getFullYear();
}
