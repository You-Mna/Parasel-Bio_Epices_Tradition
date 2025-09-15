@extends('layouts.app')

@section('content')
<div class="contact-page">
    <div class="contact-container">
        <div class="contact-card">
            <!-- Section Informations de contact -->
            <div class="contact-info-section">
                <div class="section-head">
                    <h2 class="section-title">Contactez-nous</h2>
                    <p class="contact-intro section-subtitle">Notre équipe est à votre disposition pour répondre à toutes vos questions et vous accompagner dans vos projets culinaires.</p>
                </div>
                
                <div class="contact-items">
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </div>
                        <div class="contact-details">
                            <h3>Téléphone</h3>
                            <p class="contact-description">Contactez-nous directement par appel</p>
                            <a href="tel:+2290195656806" class="contact-link">+229 01 95 65 68 06</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div class="contact-details">
                            <h3>WhatsApp</h3>
                            <p class="contact-description">Échangez avec nous en temps réel</p>
                            <a href="https://wa.me/22995656806" target="_blank" class="contact-link">+229 95 65 68 06</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fab fa-facebook"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Facebook</h3>
                            <p class="contact-description">Suivez nos actualités et découvrez nos nouveautés culinaires</p>
                            <a href="https://www.facebook.com/Parasel-Bio-100080471611097" target="_blank" class="contact-link">Parasel-Bio</a>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </div>
                        <div class="contact-details">
                            <h3>Email</h3>
                            <p class="contact-description">Envoyez-nous vos demandes détaillées</p>
                            <a href="mailto:mohamedwabi@yahoo.fr" class="contact-link">mohamedwabi@yahoo.fr</a>
                        </div>
                    </div>
                </div>
                
                <div class="contact-commitment">
                    <h3>Notre engagement</h3>
                    <p>Chez Parasel-Bio, nous nous engageons à répondre à toutes vos demandes dans les plus brefs délais. Notre expertise en épices traditionnelles et notre passion pour la qualité nous permettent de vous offrir un service client d'excellence.</p>
                </div>
            </div>
            
            <!-- Section Formulaire de contact -->
            <div class="contact-form-section">
                <div class="section-head">
                    <h2 class="section-title">Envoyez-nous un message</h2>
                    <p class="form-intro section-subtitle">Remplissez ce formulaire et nous vous répondrons dans les plus brefs délais.</p>
                </div>
                
                <form action="{{ route('contact.submit') }}" method="POST" class="contact-form">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom complet <span style="color: #dc2626;">*</span></label>
                            <input type="text" name="name" id="name" required class="form-input" placeholder="Votre nom complet">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email <span style="color: #dc2626;">*</span></label>
                            <input type="email" name="email" id="email" required class="form-input" placeholder="votre@email.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Téléphone <span style="color: #dc2626;">*</span></label>
                        <input type="tel" name="phone" id="phone" pattern="[0-9]+" inputmode="numeric" required class="form-input" placeholder="Votre numéro de téléphone">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Sujet <span style="color: #dc2626;">*</span></label>
                        <select name="subject" id="subject" required class="form-select">
                            <option value="">Sélectionner un sujet</option>
                            <option value="commande">Commande de produits</option>
                            <option value="conseils">Conseils culinaires</option>
                            <option value="partenariat">Partenariat commercial</option>
                            <option value="autre">Autre demande</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message <span style="color: #dc2626;">*</span></label>
                        <textarea name="message" id="message" rows="6" required class="form-input" placeholder="Décrivez votre demande en détail..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 2L11 13"></path>
                                <path d="M22 2l-7 20-4-9-9-4 20-7z"></path>
                            </svg>
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

