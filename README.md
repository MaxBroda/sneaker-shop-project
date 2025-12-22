# Sneaker Shop E-Commerce Projekt

Ein vollständiges E-Commerce-System für den Verkauf von Sneakern, entwickelt mit PHP (Backend) und Nuxt.js/Vue.js (Frontend).

## Inhaltsverzeichnis

- [Projektübersicht](#projektübersicht)
- [Technologie-Stack](#technologie-stack)
- [Voraussetzungen](#voraussetzungen)
- [Installation & Setup](#installation--setup)
- [Projekt starten](#projekt-starten)
- [Projektstruktur](#projektstruktur)
- [Features](#features)
- [API-Endpunkte](#api-endpunkte)

## Projektübersicht

Dieses Projekt ist eine E-Commerce-Plattform für Sneaker mit folgenden Hauptfunktionen:
- Benutzerregistrierung und -authentifizierung
- Rollenbasiertes System (Käufer & Verkäufer)
- Produktverwaltung (geplant)
- Warenkorbfunktion (geplant)
- Bestellsystem (geplant)

## Technologie-Stack

### Backend
- **PHP 8.2** mit Apache
- **SQLite** als Datenbank
- **Docker** für Containerisierung
- PDO für sichere Datenbankverbindungen

### Frontend
- **Nuxt.js 4.2.1** (Vue.js 3.5)
- **TailwindCSS** für Styling
- **TypeScript** für Type-Safety
- Nuxt Icon, Nuxt Fonts, Nuxt Image Module

## Voraussetzungen

Folgende Software muss auf dem System installiert sein:

- **Node.js**: Version 23.11.1 (empfohlen via [nvm](https://github.com/nvm-sh/nvm))
- **npm**: Version 11.6.2 (kommt mit Node.js)
- **Docker & Docker Compose**: Für das Backend

### Node.js Installation mit nvm (empfohlen)

```bash
# nvm installieren (falls noch nicht vorhanden)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash

# Node.js Version 23 installieren
nvm install 23

# Node.js Version 23 verwenden
nvm use 23

# Version überprüfen
node -v
npm -v
```

## Installation & Setup

### 1. Repository klonen

```bash
git clone https://github.com/MaxBroda/sneaker-shop-project.git
cd sneaker-shop-project
```

### 2. Backend Setup (Docker)

```bash
# Backend Umgebungsvariablen konfigurieren
cp backend/.env.example backend/.env
# .env Datei mit eigenen Werten anpassen (z.B. MOLLIE_API_KEY)

# Docker Container starten
docker-compose up -d

# Überprüfen, ob Container läuft
docker ps

# Datenbank initialisieren
docker exec sneaker-shop-backend php /var/www/html/database/init_db.php

# Optional: Token-Expiry Migration ausführen (für bestehende Datenbanken)
docker exec sneaker-shop-backend php /var/www/html/database/add_token_expiry.php
```

Das Backend läuft nun auf: **http://localhost:8080**

### 3. Frontend Setup

```bash
# In das Frontend-Verzeichnis wechseln
cd frontend

# Node.js Version 23 aktivieren
nvm use 23

# Dependencies installieren
npm install

# Development Server starten
npm run dev
```

Das Frontend läuft nun auf: **http://localhost:3000**

## Projekt starten

### Komplettes Projekt starten

**Terminal 1 - Backend:**
```bash
cd sneaker-shop-project
docker-compose up
```

**Terminal 2 - Frontend:**
```bash
cd sneaker-shop-project/frontend
nvm use 23
npm run dev
```

### Projekt stoppen

```bash
# Backend stoppen
docker-compose down

# Frontend stoppen (Ctrl+C im Terminal)
```

## Projektstruktur

```
sneaker-shop-project/
├── backend/                    # PHP Backend
│   ├── api/                   # API-Endpunkte
│   │   ├── login.php         # Login-Endpunkt
│   │   ├── register.php      # Registrierungs-Endpunkt
│   │   ├── logout.php        # Logout-Endpunkt
│   │   ├── products.php      # Produkte abrufen
│   │   ├── users.php         # Benutzerliste
│   │   ├── cart.php          # Warenkorb (geplant)
│   │   └── order.php         # Bestellungen (geplant)
│   ├── database/
│   │   ├── init_db.php       # Datenbank-Initialisierung
│   │   └── database.sqlite   # SQLite-Datenbank
│   ├── models/               # Datenmodelle (geplant)
│   ├── utils/                # Hilfsfunktionen
│   │   ├── auth.php         # Authentifizierung
│   │   ├── cors.php         # CORS-Konfiguration
│   │   └── db_connection.php # Datenbankverbindung
│   └── index.php            # API Status-Endpunkt
├── frontend/                  # Nuxt.js Frontend
│   ├── assets/
│   │   └── custom.css       # Custom CSS & Farbschema
│   ├── components/
│   │   ├── modals/          # Modal-Komponenten
│   │   └── navbar/          # Navigations-Komponenten
│   ├── composables/
│   │   └── useAuth.ts       # Authentifizierungs-Composable
│   ├── layouts/
│   │   └── default.vue      # Standard-Layout
│   ├── pages/               # Routen/Seiten
│   │   ├── index.vue        # Homepage
│   │   ├── login.vue        # Login-Seite
│   │   ├── register.vue     # Registrierungs-Seite
│   │   ├── products.vue     # Produkte (geplant)
│   │   ├── about.vue        # Info-Seite
│   │   └── contact.vue      # Kontakt-Seite
│   ├── app.vue              # Root-Komponente
│   ├── nuxt.config.ts       # Nuxt-Konfiguration
│   ├── tailwind.config.ts   # TailwindCSS-Konfiguration
│   └── package.json         # Frontend-Dependencies
├── docker-compose.yml         # Docker-Konfiguration
├── Dockerfile                # Docker-Image für Backend
└── README.md                 # Diese Datei
```

## Features

### Implementiert (Phase 2)
- Benutzerregistrierung mit Adressdaten
- Login/Logout mit Token-basierter Authentifizierung
- Rollenbasiertes System (Käufer/Verkäufer)
- Session-Persistenz (localStorage)
- Responsive Navigation (Desktop & Mobile)
- Account-Dropdown mit Benutzerinformationen
- Formularvalidierung (E-Mail, Passwort)
- Sichere Passwort-Hashes (bcrypt)
- CORS-Konfiguration für Frontend-Backend-Kommunikation

### Geplant (Phase 3+)
- Produktverwaltung für Verkäufer
- Produktkatalog mit Filterung
- Warenkorbfunktion
- Bestellabwicklung
- Profilseiten

## API-Endpunkte

Basis-URL: `http://localhost:8080/api`

### Authentifizierung

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| POST | `/register.php` | Neuen Benutzer registrieren | Nein |
| POST | `/login.php` | Benutzer anmelden | Nein |
| POST | `/logout.php` | Benutzer abmelden | Ja |

### Benutzer

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| GET | `/users.php` | Alle Benutzer abrufen | Optional |

### Produkte

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| GET | `/products.php` | Alle Produkte abrufen | Nein |

### Status

| Methode | Endpunkt | Beschreibung |
|---------|----------|--------------|
| GET | `/index.php` | API-Status und Uptime |

## Design-System

Das Projekt verwendet ein konsistentes Farbschema:

```css
--primary-dark-color: #002e4f;   /* Dunkelblau */
--secondary-dark-color: #00a6dd; /* Hellblau */
--tertiary-dark-color: #daeafe;  /* Sehr helles Blau */
--accent-color: #012a4a;         /* Akzentfarbe */
--signal-red: #e63946;           /* Signalrot */
```

## Sicherheit

- Passwörter werden mit `password_hash()` (bcrypt) gehashed
- SQL-Injection-Schutz durch PDO Prepared Statements
- Token-basierte Authentifizierung mit Ablaufzeit
- Token werden als SHA-256 Hash in der Datenbank gespeichert
- CORS richtig konfiguriert
- Umgebungsvariablen für Secrets (keine hardcodierten API-Keys)
- Standardisierte API-Fehlerbehandlung mit korrekten HTTP-Statuscodes

## Troubleshooting

### Backend startet nicht
```bash
# Logs überprüfen
docker-compose logs

# Container neu starten
docker-compose down
docker-compose up -d
```

### Frontend-Build-Fehler
```bash
# Node-Version überprüfen
node -v  # muss v23 sein

# Node-Version wechseln
nvm use 23

# Dependencies neu installieren
rm -rf node_modules package-lock.json
npm install
```

## Datenbankschema

### users
- `id`, `email`, `first_name`, `last_name`, `password_hash`, `role`, `created_at`

### addresses
- `id`, `user_id`, `street`, `house_number`, `city`, `postal_code`, `country`

### products
- `id`, `name`, `description`, `price`, `image`, `category`, `seller_id`

### cart_items
- `id`, `user_id`, `product_id`, `quantity`

### orders
- `id`, `user_id`, `total`, `created_at`

### order_items
- `id`, `order_id`, `product_id`, `quantity`, `price`

### user_tokens
- `id`, `user_id`, `token`, `expires_at`, `created_at`

## Umgebungsvariablen

### Backend (.env)

```env
# Mollie Payment Gateway
MOLLIE_API_KEY=test_your_mollie_api_key_here

# Application URLs
APP_URL=http://localhost:8080
FRONTEND_URL=http://localhost:3000

# Token Configuration (in seconds)
TOKEN_EXPIRY_SECONDS=86400

# Debug Mode (set to false in production)
DEBUG_MODE=true
```

**Wichtig**: Erstellen Sie eine `.env` Datei im `backend/` Verzeichnis basierend auf `.env.example` mit Ihren eigenen Werten.

## Lizenz

Dieses Projekt ist für akademische Zwecke erstellt.

## Autor

Max Broda - Universitäts-Projekt

---

**Hinweis**: Dieses Projekt befindet sich in aktiver Entwicklung. Für Phase 2 ist die Basis-Authentifizierung und Navigation vollständig implementiert.
