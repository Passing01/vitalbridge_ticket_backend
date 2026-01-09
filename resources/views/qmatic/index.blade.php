<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Qmatic - Système de gestion de files d'attente | VitalBridge</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #10b981;
            --accent: #f59e0b;
            --dark: #1e293b;
            --gray: #64748b;
            --light-gray: #f1f5f9;
            --white: #ffffff;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        
        nav.scrolled {
            padding: 0.5rem 0;
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        
        .nav-links a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }
        
        .nav-links a:hover {
            color: var(--primary);
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }
        
        /* Hero Section */
        .hero {
            padding: 120px 2rem 80px;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s linear infinite;
        }
        
        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .hero-content {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            animation: fadeInUp 0.8s ease;
        }
        
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            animation: fadeInUp 0.8s ease 0.2s both;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            animation: fadeInUp 0.8s ease 0.4s both;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Features Section */
        .features {
            padding: 80px 2rem;
            background: var(--white);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .section-header p {
            font-size: 1.125rem;
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: var(--primary);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .feature-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--dark);
        }
        
        .feature-card p {
            color: var(--gray);
            line-height: 1.7;
        }
        
        /* Stats Section */
        .stats {
            padding: 80px 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--white);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            text-align: center;
        }
        
        .stat-card {
            animation: fadeInUp 0.6s ease;
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.125rem;
            opacity: 0.9;
        }
        
        /* Languages Section */
        .languages {
            padding: 80px 2rem;
            background: var(--light-gray);
        }
        
        .language-badges {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }
        
        .language-badge {
            background: var(--white);
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .language-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        /* CTA Section */
        .cta {
            padding: 80px 2rem;
            background: var(--dark);
            color: var(--white);
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .cta p {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        /* Footer */
        footer {
            background: #0f172a;
            color: var(--white);
            padding: 3rem 2rem;
            text-align: center;
        }
        
        footer p {
            opacity: 0.7;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <a href="/" class="logo">
                🎫 VitalBridge
            </a>
            <ul class="nav-links">
                <li><a href="/">Accueil</a></li>
                <li><a href="#fonctionnalites">Fonctionnalités</a></li>
                <li><a href="#apropos">À propos</a></li>
            </ul>
            <a href="{{ route('qmatic.login') }}" class="btn btn-primary">Espace Agent</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Système de Gestion de Files d'Attente</h1>
            <p>
                Solution moderne et adaptée aux réalités du Burkina Faso pour optimiser l'accueil 
                dans les banques, hôpitaux, administrations et centres de services.
            </p>
            <div class="hero-buttons">
                <a href="{{ route('qmatic.login') }}" class="btn btn-primary">Accès Agent</a>
                <a href="#fonctionnalites" class="btn" style="background: rgba(255,255,255,0.2); color: white;">En savoir plus</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="fonctionnalites">
        <div class="container">
            <div class="section-header">
                <h2>Fonctionnalités Complètes</h2>
                <p>Tout ce dont vous avez besoin pour gérer efficacement vos files d'attente</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎫</div>
                    <h3>Prise de Ticket Simplifiée</h3>
                    <p>
                        Bornes tactiles, interface web et application mobile pour permettre 
                        aux usagers de prendre leur ticket facilement. Attribution automatique 
                        d'un numéro avec date, heure et priorité.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Gestion des Priorités</h3>
                    <p>
                        Système intelligent de priorisation : files normales, seniors, VIP et urgences. 
                        Possibilité de réaffectation manuelle selon les besoins.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📺</div>
                    <h3>Affichage en Temps Réel</h3>
                    <p>
                        Écrans de salle d'attente affichant les numéros appelés et les guichets disponibles. 
                        Support audio multilingue pour annoncer les tickets.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Interface Agent Optimisée</h3>
                    <p>
                        Tableau de bord simple pour les agents avec appel du prochain ticket, 
                        possibilité de rappel, et marquage des absents ou tickets servis.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Statistiques Détaillées</h3>
                    <p>
                        Temps d'attente moyen, temps de service, performance par agent et par service. 
                        Export en PDF et Excel pour vos rapports.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🌍</div>
                    <h3>Support Multilingue</h3>
                    <p>
                        Interface disponible en Français et langues locales du Burkina Faso (Mooré, Dioula, Fulfuldé). 
                        Annonces vocales personnalisables.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Sécurité Avancée</h3>
                    <p>
                        Authentification par rôles, journalisation des actions, protection contre 
                        les attaques CSRF/XSS, et sauvegardes automatiques.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3>Configuration Flexible</h3>
                    <p>
                        Gestion des services, horaires personnalisables, paramétrage des règles 
                        de priorité et configuration multi-agences.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Réseau Instable ?</h3>
                    <p>
                        Mode dégradé hors-ligne (optionnel) pour continuer à fonctionner même 
                        avec une connexion internet limitée ou instable.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="section-header">
                <h2 style="color: white;">Des Résultats Mesurables</h2>
                <p style="color: rgba(255,255,255,0.9);">
                    Notre système a fait ses preuves dans de nombreuses organisations
                </p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">-40%</div>
                    <div class="stat-label">Temps d'attente réduit</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">+65%</div>
                    <div class="stat-label">Satisfaction client</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">+50%</div>
                    <div class="stat-label">Efficacité des agents</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">&lt;2s</div>
                    <div class="stat-label">Temps de réponse</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Languages Section -->
    <section class="languages">
        <div class="container">
            <div class="section-header">
                <h2>Adapté à Notre Réalité</h2>
                <p>
                    Un système pensé pour le Burkina Faso avec support des langues locales
                </p>
            </div>
            
            <div class="language-badges">
                <div class="language-badge">🇫🇷 Français</div>
                <div class="language-badge">🗣️ Mooré</div>
                <div class="language-badge">🗣️ Dioula</div>
                <div class="language-badge">🗣️ Fulfuldé</div>
                <div class="language-badge">🇬🇧 Anglais (optionnel)</div>
            </div>
        </div>
    </section>

    <!-- Use Cases Section -->
    <section class="features">
        <div class="container">
            <div class="section-header">
                <h2>Pour Qui ?</h2>
                <p>Une solution adaptée à tous les secteurs</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🏥</div>
                    <h3>Centres de Santé</h3>
                    <p>
                        Hôpitaux, cliniques et centres médicaux. Gestion des consultations, 
                        urgences et rendez-vous avec priorisation intelligente.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🏦</div>
                    <h3>Banques & Finances</h3>
                    <p>
                        Agences bancaires et institutions financières. Optimisation du flux 
                        clients et réduction des temps d'attente.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🏛️</div>
                    <h3>Administrations</h3>
                    <p>
                        Services publics, mairies et préfectures. Amélioration de l'accueil 
                        des usagers et digitalisation des services.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎓</div>
                    <h3>Établissements Scolaires</h3>
                    <p>
                        Universités et écoles. Gestion des inscriptions, bourses et 
                        services administratifs étudiants.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📞</div>
                    <h3>Centres d'Appels</h3>
                    <p>
                        Call centers et services clients. Organisation des demandes et 
                        optimisation de la distribution des appels.
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🏢</div>
                    <h3>Entreprises</h3>
                    <p>
                        Centres de services et accueils d'entreprises. Meilleure gestion 
                        des visiteurs et rendez-vous professionnels.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Prêt à Transformer Votre Accueil ?</h2>
            <p>
                Rejoignez les organisations qui ont choisi VitalBridge pour optimiser 
                leur gestion de files d'attente
            </p>
            <a href="{{ route('qmatic.login') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 1rem 2.5rem;">
                Connexion Agent
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} VitalBridge. Tous droits réservés. | Développé avec ❤️ au Burkina Faso</p>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
