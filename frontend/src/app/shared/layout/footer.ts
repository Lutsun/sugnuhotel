import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-footer',
  imports: [RouterLink],
  template: `
    <footer class="bg-ink-900 text-ink-100 mt-20">
      <div class="max-w-6xl mx-auto px-4 py-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
          <a routerLink="/" class="flex items-center gap-2 font-display text-xl font-semibold text-white mb-3">
            <span class="w-8 h-8 rounded-full bg-brand-500 text-white flex items-center justify-center text-xs">SH</span>
            SugnuHotel
          </a>
          <p class="text-sm text-ink-400 leading-relaxed">
            Un séjour à la fois confortable et authentique, au cœur de Dakar. L'hospitalité sénégalaise vous attend.
          </p>
        </div>

        <div>
          <h3 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Navigation</h3>
          <ul class="space-y-2 text-sm text-ink-400">
            <li><a routerLink="/" class="hover:text-brand-300 transition">Accueil</a></li>
            <li><a routerLink="/rooms" class="hover:text-brand-300 transition">Nos chambres</a></li>
            <li><a routerLink="/booking/search" class="hover:text-brand-300 transition">Réserver</a></li>
            <li><a routerLink="/login" class="hover:text-brand-300 transition">Espace client</a></li>
          </ul>
        </div>

        <div>
          <h3 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Contact</h3>
          <ul class="space-y-2 text-sm text-ink-400">
            <li>Avenue Cheikh Anta Diop, Dakar</li>
            <li>+221 33 800 00 00</li>
            <li>contact&#64;sugnuhotel.sn</li>
          </ul>
        </div>

        <div>
          <h3 class="text-white font-semibold text-sm uppercase tracking-wide mb-4">Suivez-nous</h3>
          <div class="flex gap-3">
            @for (icon of socialIcons; track icon.label) {
              <a
                [attr.aria-label]="icon.label"
                href="#"
                class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-brand-600 transition"
              >
                <span class="text-xs font-semibold">{{ icon.label[0] }}</span>
              </a>
            }
          </div>
        </div>
      </div>

      <div class="border-t border-white/10">
        <div class="max-w-6xl mx-auto px-4 py-5 text-xs text-ink-400 flex flex-col sm:flex-row justify-between gap-2">
          <p>&copy; {{ year }} SugnuHotel. Tous droits réservés. Photos : Wikimedia Commons.</p>
          <p>Projet académique — SupInfo</p>
        </div>
      </div>
    </footer>
  `,
})
export class Footer {
  protected readonly year = new Date().getFullYear();
  protected readonly socialIcons = [{ label: 'Facebook' }, { label: 'Instagram' }, { label: 'X' }];
}
