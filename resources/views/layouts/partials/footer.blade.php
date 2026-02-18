{{-- resources/views/layouts/partials/footer.blade.php --}}
<footer class="footer">
    <div class="footer-newsletter">
        <div class="container">
            <div class="newsletter-content">
                <h3>Recevez nos offres exclusives</h3>
                <p>Inscrivez-vous à notre newsletter pour recevoir des offres spéciales</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Votre adresse email" required>
                    <button type="submit">S'abonner <i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                        <span class="logo-text">SUGNU<span class="highlight">HOTEL</span></span>
                    </div>
                    <p class="footer-description">
                        Découvrez l'excellence de l'hospitalité sénégalaise dans un cadre luxueux et moderne. 
                        Votre séjour inoubliable commence ici.
                    </p>
                    <div class="footer-social">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="{{ route('home') }}">Accueil</a></li>
                        <li><a href="{{ route('rooms') }}">Nos chambres</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Nos services</h4>
                    <ul>
                        <li><a href="#">Restaurant & Bar</a></li>
                        <li><a href="#">Spa & Bien-être</a></li>
                        <li><a href="#">Piscine</a></li>
                        <li><a href="#">Salle de sport</a></li>
                        <li><a href="#">Service en chambre</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4>Contact</h4>
                    <ul class="contact-list">
                        <li><i class="fas fa-map-marker-alt"></i> Route de Ngor, Dakar, Sénégal</li>
                        <li><i class="fas fa-phone-alt"></i> +221 78 123 45 67</li>
                        <li><i class="fas fa-envelope"></i> contact@sugnuhotel.com</li>
                        <li><i class="fas fa-clock"></i> Réception 24h/24</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; {{ date('Y') }} SugnuHotel. Tous droits réservés. | Designé avec <i class="fas fa-heart"></i> au Sénégal</p>
        </div>
    </div>
</footer>