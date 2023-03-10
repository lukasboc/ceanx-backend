<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# CeanX Backend

Dieses Repository enthält den Quellcode der Backend-Anwendung von CeanX.

## Lokales Setup

- .env aus .env.example erstellen
- Dependencies mit `composer install` installieren
- Mittels `php artisan key:generate` einen APP_KEY erstellen.
- DB Migrationen mit `php artisan migrate` durchführen
- ggf. der `/config/cors.php` die URL des lokalen Frontends zu `allowed_origins` hinzufügen.
- Über `php artisan serve` Backend starten

Die `.env.example` enthält Beispiele für den lokalen und produktiven Betrieb des Backends in Verbindung mit dem [CeanX Frontend](https://github.com/lukasboc/ceanx-frontend).


## Produktives Setup

- .env aus .env.example erstellen
- Hinweis zur Erstellung der .env: `SANCTUM_STATEFUL_DOMAINS` und `SESSION_DOMAIN` auf die Domain des Frontends zeigen. (ohne http und www)
- Dependencies mit `composer install` installieren
- Mittels `php artisan key:generate` einen APP_KEY erstellen.
- DB Migrationen mit `php artisan migrate` durchführen
- In der `/config/cors.php` die URL des Frontends zu `allowed_origins` hinzufügen.
- Das Backend z.B. über FTP auf Webspace deployen. Weitere Möglichkeiten zum Deployment können der [Laravel Dokumentation](https://laravel.com/docs) entnommen werden. Zu bedachten ist insbesondere, dass den DocumentRoot auf das public-Verzeichnis zu setzen.

Achtung: Damit die Authentifizierung zwischen Frontend und Backend funktioniert, muss die [Laravel Sanctum Dokumentation](https://laravel.com/docs/9.x/sanctum#spa-authentication) bei der Wahl der Domains/Subdomains beachtet werden.

TLDR: Das Deployment muss unter derselben Domain / Subdomain stattfinden. Das Backend kann unter `backend.[FRONTEND_DOMAIN]` deployed werden.
Fehler kommen zustande, wenn z.B. das Frontend unter ceanx.example.de und das Backend unter ceanx.backend.example.de geployed werden.
