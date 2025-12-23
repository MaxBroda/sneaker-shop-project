# Sneaker Shop E-Commerce Projekt

Ein vollständiges E-Commerce-System für den Verkauf von Sneakern, entwickelt mit PHP (Backend) und Nuxt.js/Vue.js (Frontend).

## Inhaltsverzeichnis

- [Projektübersicht](#projektübersicht)
- [Quick Start](#quick-start)
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
- Produktverwaltung für Verkäufer
- Warenkorbfunktion
- Bestellsystem mit Mollie-Integration
- Profilseiten mit Bestellhistorie

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

- **Node.js**: Version v20.19.4 (oder höher) (empfohlen via [nvm](https://github.com/nvm-sh/nvm))
- **npm**: Version 10.8.2 (kommt mit Node.js)
- **Docker & Docker Compose**: Für das Backend

> **Hinweis:** Dieses Projekt wurde mit Node.js `v24.12.0` entwickelt und getestet.

### Node.js Installation mit nvm (empfohlen)

```bash
# nvm installieren (falls noch nicht vorhanden)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash

# Node.js Version 24 installieren
nvm install 24

# Node.js Version 24 verwenden
nvm use 24

# Version überprüfen
node -v
npm -v
```

## Installation & Setup

### Wichtige Hinweise vor der Installation

**Was wird vom Repository kopiert:**
- ✅ Gesamter Quellcode (Backend & Frontend)
- ✅ Datenbank-Schema und Seed-Skripte
- ✅ Platzhalter-Bilder für Seed-Produkte (in `backend/uploads/`)
- ✅ `.env.example` Konfigurationsdatei

**Was NICHT vom Repository kopiert wird:**
- ❌ Die Datenbank selbst (`database.sqlite`) - wird lokal erstellt
- ❌ `.env` Datei mit echten API-Keys - muss manuell erstellt werden
- ❌ `vendor/` und `node_modules/` Verzeichnisse - werden automatisch installiert
- ❌ `storage/*.json` Dateien (z.B. Payment-Logs)
- ❌ Von Benutzern hochgeladene Produktbilder (`backend/uploads/product_*`)

**Das bedeutet:** Beim Klonen des Repositories startest du mit einer leeren Datenbank. Die Testdaten (Benutzer, Produkte) werden durch das Seed-Skript erstellt. Die Platzhalter-Bilder für die 22 Seed-Produkte sind bereits im Repository enthalten. Wenn Benutzer neue Produkte mit eigenen Bildern hochladen, werden diese Bilder lokal gespeichert, aber nicht ins Repository committed.

### 1. Repository klonen

```bash
git clone https://github.com/MaxBroda/sneaker-shop-project.git
cd sneaker-shop-project
```

### 2. Backend Setup (Docker)

```bash
# Backend Umgebungsvariablen konfigurieren
cp backend/.env.example backend/.env
# .env Datei öffnen und MOLLIE_API_KEY mit dem bereitgestellten Wert ersetzen

# Docker Container starten
docker-compose up -d

# Überprüfen, ob Container läuft
docker ps

# Datenbank initialisieren (erstellt alle Tabellen)
docker exec sneaker-shop-backend php /var/www/html/database/init_db.php

# Datenbank mit Testdaten befüllen (WICHTIG für erste Inbetriebnahme!)
docker exec sneaker-shop-backend php /var/www/html/database/seed_db.php
```

Das Backend läuft nun auf: **http://localhost:8080**

#### Testdaten nach dem Seeding

Nach dem Ausführen von `seed_db.php` werden folgende Daten erstellt:

**Benutzer (Login-Daten):**

| Rolle | Name | E-Mail | Passwort | Adressen |
|-------|------|--------|----------|----------|
| Verkäufer | Peter Petersen | emailPeter@example.com | test | 3 |
| Verkäufer | Max Mustermann | emailMax@example.com | test | 2 |
| Käufer | Lisa Lustig | emailLisa@example.com | test | 4 |

**Produkte:**
- **22 Produkte insgesamt**
  - 14 Produkte von Peter
  - 8 Produkte von Max
- Kategorien: Sneaker, Running, Training, Outdoor
- Preise: 69,99 € bis 229,99 €

**Wichtig:** Alle Testbenutzer haben das Passwort `test` für einfachen Zugriff während der Entwicklung.

### 3. Frontend Setup

```bash
# In das Frontend-Verzeichnis wechseln
cd frontend

# Node.js Version 24 aktivieren
nvm use 24

# Dependencies installieren
npm install

# Development Server starten
npm run dev
```

Das Frontend läuft nun auf: **http://localhost:3000**

## Projekt starten

### Komplettes Projekt starten

**Hinweis:** Die Datenbank-Initialisierung und das Seeding müssen nur einmalig beim ersten Setup ausgeführt werden. Danach reicht es, nur Backend und Frontend zu starten.

**Terminal 1 - Backend:**
```bash
cd sneaker-shop-project
docker-compose up
```

**Terminal 2 - Frontend:**
```bash
cd sneaker-shop-project/frontend
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
├── backend/                       # PHP Backend
│   ├── api/                       # API-Endpunkte
│   │   ├── addresses.php          # Adressverwaltung
│   │   ├── cart.php               # Warenkorbfunktion
│   │   ├── login.php              # Login-Endpunkt
│   │   ├── logout.php             # Logout-Endpunkt
│   │   ├── mollie-payment.php     # Zahlungsabwicklung
│   │   ├── mollie-webhook.php     # Webhook für Zahlungsstatus
│   │   ├── orders.php             # Bestellverwaltung
│   │   ├── product.php            # Produktverwaltung
│   │   ├── register.php           # Registrierungs-Endpunkt
│   │   ├── seller-orders.php      # Verkäufer-Bestellungen
│   │   ├── upload.php             # Datei-Upload
│   │   └── users.php              # Benutzerliste
│   ├── database/
│   │   ├── database.sqlite        # SQLite-Datenbank (lokal)
│   │   ├── init_db.php            # Datenbank-Initialisierung
│   │   └── seed_db.php            # Testdaten generieren
│   ├── models/                    # Datenmodelle
│   │   ├── Cart.php               # Warenkorb-Modell
│   │   ├── Order.php              # Bestellungs-Modell
│   │   ├── Product.php            # Produkt-Modell
│   │   └── User.php               # Benutzer-Modell
│   ├── utils/                     # Hilfsfunktionen
│   │   ├── auth.php               # Authentifizierung
│   │   ├── config.php             # Konfiguration
│   │   ├── db_connection.php      # Datenbankverbindung
│   │   ├── response.php           # API-Response-Handler
│   │   └── validation.php         # Validierungsfunktionen
│   ├── uploads/                   # Hochgeladene Produktbilder
│   │   └── placeholder_*.jpg      # Platzhalter-Bilder für Seed-Daten
│   ├── storage/                   # JSON-Dateien (z.B. Payments)
│   ├── vendor/                    # Composer-Dependencies
│   ├── index.php                  # API Status-Endpunkt
│   ├── composer.json              # Composer-Konfiguration
│   └── composer.lock              # Composer Lock-File
├── frontend/                      # Nuxt.js Frontend
│   ├── assets/
│   │   └── custom.css             # Custom CSS & Farbschema
│   ├── components/
│   │   ├── checkout/              # Checkout-Komponenten
│   │   ├── modals/                # Modal-Komponenten
│   │   ├── navbar/                # Navigations-Komponenten
│   │   ├── profile/               # Profil-Komponenten
│   │   └── ui/                    # UI-Komponenten
│   ├── composables/
│   │   ├── useApi.ts              # API-Composable
│   │   ├── useAuth.ts             # Authentifizierungs-Composable
│   │   ├── useCart.ts             # Warenkorb-Composable
│   │   └── useProductForm.ts      # Produktformular-Composable
│   ├── layouts/
│   │   └── default.vue            # Standard-Layout
│   ├── pages/                     # Routen/Seiten
│   │   ├── products/
│   │   │   ├── [slug].vue         # Einzelne Produktseite
│   │   │   └── index.vue          # Produktübersicht
│   │   ├── checkout/              # Checkout-Unterseiten
│   │   ├── about.vue              # Über uns
│   │   ├── add-product.vue        # Produkt hinzufügen (Verkäufer)
│   │   ├── careers.vue            # Karriere
│   │   ├── cart.vue               # Warenkorb
│   │   ├── checkout.vue           # Checkout
│   │   ├── contact.vue            # Kontakt
│   │   ├── faq.vue                # FAQ
│   │   ├── index.vue              # Homepage
│   │   ├── legal.vue              # Rechtliches
│   │   ├── login.vue              # Login
│   │   ├── privacy.vue            # Datenschutz
│   │   ├── profile.vue            # Benutzer-Profil
│   │   ├── register.vue           # Registrierung
│   │   ├── returns.vue            # Rückgabe
│   │   ├── shipping.vue           # Versand
│   │   ├── size-guide.vue         # Größentabelle
│   │   └── terms.vue              # AGB
│   ├── plugins/
│   │   └── auth.client.ts         # Auth-Plugin (Client-Side)
│   ├── public/                    # Statische Dateien
│   │   ├── favicon.svg            # Favicon
│   │   └── *.png, *.svg           # Payment-Icons
│   ├── app.vue                    # Root-Komponente
│   ├── nuxt.config.ts             # Nuxt-Konfiguration
│   ├── tailwind.config.ts         # TailwindCSS-Konfiguration
│   ├── tsconfig.json              # TypeScript-Konfiguration
│   ├── package.json               # Frontend-Dependencies
│   └── package-lock.json          # NPM Lock-File
├── docker-compose.yml             # Docker-Konfiguration
├── Dockerfile                     # Docker-Image für Backend
├── docker-entrypoint.sh           # Docker-Entrypoint-Script
├── apache-config.conf             # Apache-Konfiguration
├── .gitignore                     # Git-Ignore-Regeln
└── README.md                      # Diese Datei
```

## Features

### ✅ Vollständig implementiert

**Authentifizierung & Benutzerverwaltung:**
- Benutzerregistrierung mit Adressdaten
- Login/Logout mit Token-basierter Authentifizierung
- Rollenbasiertes System (Käufer/Verkäufer)
- Session-Persistenz (localStorage)
- Token-Ablauf mit automatischer Benachrichtigung
- Mehrere Adressen pro Benutzer mit Standard-Adresse

**Produktverwaltung:**
- Produktkatalog mit Filterung nach Kategorien
- Produktdetailseiten mit technischen Spezifikationen
- Verkäufer können eigene Produkte erstellen und bearbeiten
- Bild-Upload für Produkte
- Größenauswahl und Verfügbarkeit

**Warenkorb & Checkout:**
- Vollständige Warenkorbfunktion
- Gastwarenkorb (ohne Login)
- Adressauswahl beim Checkout
- Verschiedene Zahlungsmethoden (via Mollie)
- Bestellbestätigung und Tracking

**Bestellverwaltung:**
- Bestellhistorie für Käufer
- Bestellverwaltung für Verkäufer
- Payment-Status-Tracking
- Order-Nummern-System

**UI/UX:**
- Responsive Design (Desktop & Mobile)
- Mobile Navigation
- Modal-Komponenten für verschiedene Aktionen
- Account-Dropdown mit Benutzerinformationen
- Formularvalidierung
- Fehlerbehandlung mit aussagekräftigen Meldungen

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

### Adressen

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| GET | `/addresses.php` | Adressen des Benutzers abrufen | Ja |
| POST | `/addresses.php` | Neue Adresse hinzufügen | Ja |
| PUT | `/addresses.php` | Adresse aktualisieren | Ja |
| DELETE | `/addresses.php` | Adresse löschen | Ja |

### Produkte

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| GET | `/product.php` | Alle Produkte oder einzelnes Produkt abrufen | Nein |
| POST | `/product.php` | Neues Produkt erstellen (nur Verkäufer) | Ja |
| PUT | `/product.php` | Produkt aktualisieren (nur eigene) | Ja |
| DELETE | `/product.php` | Produkt löschen (nur eigene) | Ja |
| POST | `/upload.php` | Produktbild hochladen | Ja |

### Warenkorb

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| GET | `/cart.php` | Warenkorb abrufen | Optional (Session) |
| POST | `/cart.php` | Artikel zum Warenkorb hinzufügen | Optional |
| PUT | `/cart.php` | Warenkorbmenge aktualisieren | Optional |
| DELETE | `/cart.php` | Artikel aus Warenkorb entfernen | Optional |

### Bestellungen

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| GET | `/orders.php` | Bestellungen des Benutzers abrufen | Ja |
| POST | `/orders.php` | Neue Bestellung erstellen | Ja |
| GET | `/seller-orders.php` | Bestellungen für Verkäufer-Produkte | Ja (Verkäufer) |

### Payment

| Methode | Endpunkt | Beschreibung | Auth erforderlich |
|---------|----------|--------------|-------------------|
| POST | `/mollie-payment.php` | Mollie-Zahlung initiieren | Ja |
| POST | `/mollie-webhook.php` | Mollie Webhook für Status-Updates | Nein (Webhook) |

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

### Datenbank-Probleme

**"Tabelle nicht gefunden" Fehler:**
```bash
# Datenbank initialisieren
docker exec sneaker-shop-backend php /var/www/html/database/init_db.php
```

**Keine Testdaten vorhanden / Kann mich nicht einloggen:**
```bash
# Seed-Skript ausführen
docker exec sneaker-shop-backend php /var/www/html/database/seed_db.php
```

**Datenbank ist korrupt oder fehlerhaft:**
```bash
# Datenbank komplett zurücksetzen
rm backend/database/database.sqlite
docker exec sneaker-shop-backend php /var/www/html/database/init_db.php
docker exec sneaker-shop-backend php /var/www/html/database/seed_db.php
```

### Frontend-Build-Fehler
```bash
# Node-Version überprüfen
node -v  # muss v24 sein

# Node-Version wechseln
nvm use 24

# Dependencies neu installieren
rm -rf node_modules package-lock.json
npm install
```

## Datenbankschema

### users
- `id`, `email`, `first_name`, `last_name`, `password_hash`, `role`, `created_at`

### addresses
- `id`, `user_id`, `street`, `house_number`, `city`, `postal_code`, `country`, `is_default`

### products
- `id`, `name`, `description`, `price`, `image`, `category`, `technical_specs`, `tag_icon`, `tag_text`, `seller_id`

### cart_items
- `id`, `user_id`, `session_id`, `product_id`, `quantity`, `size`, `created_at`

### orders
- `id`, `user_id`, `order_number`, `total`, `status`, `payment_status`, `billing_address`, `shipping_address`, `payment_method`, `created_at`

### order_items
- `id`, `order_id`, `product_id`, `quantity`, `size`, `price`

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

**Stand:** Dezember 2024 - Vollständige E-Commerce-Funktionalität implementiert
