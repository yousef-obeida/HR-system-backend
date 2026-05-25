# Smart HR API

## Project Overview
Smart HR is a robust Applicant Tracking System (ATS) and HR management backend. It allows HR professionals and Admins to manage job postings, track candidates through various stages, schedule interviews, and automatically parse and analyze candidate CVs using AI.

## Features
- **Job Management:** Create, update, and manage job postings.
- **Candidate Tracking:** Move candidates through a customizable pipeline (Kanban style).
- **Interview Scheduling:** Schedule interviews and automatically send email invitations to candidates.
- **AI CV Analysis:** Automatically parse uploaded PDF CVs and use Google Gemini AI to analyze candidate qualifications against job requirements.
- **Role-Based Access Control:** Separate roles for Admins (managing users) and HR (managing jobs and candidates).
- **Email Notifications:** Automated emails for application received, interview scheduled, offers, and rejections.

## Tech Stack
- **Framework:** Laravel 11 (PHP)
- **Database:** MySQL / SQLite
- **Authentication:** Laravel Sanctum
- **Queue System:** Database / Redis (for email and AI processing)
- **AI Integration:** Google Gemini API
- **PDF Extraction:** `spatie/pdf-to-text` (requires `pdftotext` system dependency)

## Setup Steps
1. Clone the repository and install dependencies:
   ```bash
   composer install
   ```
2. Copy the environment file and generate the app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Configure your `.env` file (Database, SMTP, Gemini API Key, etc.). **Never commit your `.env` file!**
4. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
5. Serve the application:
   ```bash
   php artisan serve
   ```

## Postman Collection
To test the API endpoints, you can use the provided Postman collection and postman environment file. Ensure you authenticate by logging in (`POST /api/login`) to obtain a Sanctum Bearer token for protected routes.

## Queue Instructions
The application uses background queues to handle time-consuming tasks like sending emails and analyzing CVs with AI.
To run the queue worker locally, execute:
```bash
php artisan queue:work
```

## AI Features
When a candidate applies with a PDF CV, the application dispatches an `AnalyzeCVJob`. This job extracts text from the PDF using `pdftotext` and sends it to the Google Gemini API. The AI evaluates the CV against the job description and generates structured insights (strengths, weaknesses, match score) which are stored in the database.

## Production Queue Setup
> **⚠️ IMPORTANT:** Since the application heavily relies on queues for mail delivery and AI jobs, you **MUST** run a queue worker in production.
Configure Supervisor (or a similar process monitor) to keep the queue worker running permanently in your production environment:
```bash
php artisan queue:work --tries=3 --timeout=90
```
Failure to run the queue worker in production will result in unsent emails and unprocessed CVs.

> When deploying with Docker (below), a dedicated `queue` container already runs the worker for you — no Supervisor setup is needed.

---

## 🐳 Docker Deployment (VPS)

The project ships with a production-ready Docker setup. The stack is composed of:

| Service     | Image / Build            | Purpose                                              |
|-------------|--------------------------|------------------------------------------------------|
| `app`       | built from `Dockerfile`  | PHP 8.3-FPM — runs migrations & cache warming on boot |
| `nginx`     | `nginx:1.27-alpine`      | Public web entrypoint, proxies PHP to `app`          |
| `queue`     | built from `Dockerfile`  | `queue:work` — emails & AI CV-analysis jobs          |
| `scheduler` | built from `Dockerfile`  | `schedule:work` — Laravel scheduled tasks            |
| `mysql`     | `mysql:8.0`              | Database (persistent volume)                         |
| `redis`     | `redis:7-alpine`         | Cache / sessions / queue backend (persistent volume) |

The image installs the `pdftotext` binary (`poppler-utils`) required for CV parsing, plus the `pdo_mysql`, `redis`, `intl`, `zip`, `gd`, `bcmath`, `pcntl` and `opcache` PHP extensions.

### Prerequisites on the VPS
- Docker Engine 24+ and the Docker Compose plugin (`docker compose`).

### First deployment
```bash
# 1. Clone the repo
git clone <your-repo-url> hr-system && cd hr-system

# 2. Create the production env file and fill in real secrets
cp .env.production.example .env
#    -> set DB_PASSWORD, DB_ROOT_PASSWORD, MAIL_*, GEMINI_API_KEY, APP_URL ...

# 3. Build the image
docker compose build

# 4. Generate an application key and paste it into .env as APP_KEY=
#    (APP_KEY must come from the environment — it is not baked into the image)
docker compose run --rm --no-deps --entrypoint php app artisan key:generate --show

# 5. Bring the whole stack up
docker compose up -d
```

The `app` container automatically waits for MySQL, runs `php artisan migrate --force`, links storage, and caches config/routes/views on every start.

The API is now served on `http://<vps-ip>:8080` (change the host port via `APP_PORT` in `.env`).

> **Seeding (optional):** to create the initial roles/admin user run
> `docker compose run --rm app php artisan db:seed --force`.

### TLS / domain
Put a reverse proxy in front of the published port to terminate HTTPS — e.g. host **Caddy**, **Traefik**, or an Nginx vhost proxying to `127.0.0.1:8080`. Then set `APP_URL=https://your-domain.com` in `.env`.

### Updating / redeploying
Use the helper script (it rebuilds, refreshes the code, and preserves the database, Redis and uploaded-file volumes):
```bash
./deploy.sh
```

### Automated deploys (GitHub Actions)
`.github/workflows/deploy.yml` auto-deploys on every push to `main` (and can be run manually from the **Actions** tab). It SSHes into the VPS and runs `deploy.sh`, so the VPS builds the image itself.

**One-time setup on the VPS:**
1. Clone the repo, create `.env`, and run the [first deployment](#first-deployment) once by hand.
2. Make sure the deploy user can run Docker without sudo: `sudo usermod -aG docker $USER` (re-login afterwards).
3. If the repo is **private**, configure git auth on the VPS (a read-only deploy key or a token) so `git pull` inside `deploy.sh` works non-interactively.
4. Create an SSH key pair for CI and authorize it:
   ```bash
   ssh-keygen -t ed25519 -f ci_deploy -N ""        # run locally
   ssh-copy-id -i ci_deploy.pub user@your-vps      # add public key to the VPS
   ```

**Add these repository secrets** (Settings → Secrets and variables → Actions):

| Secret | Example | Description |
|--------|---------|-------------|
| `VPS_HOST` | `203.0.113.10` | VPS IP or hostname |
| `VPS_USER` | `deploy` | SSH user (must be in the `docker` group) |
| `VPS_SSH_KEY` | *(contents of `ci_deploy`)* | The **private** key authorized above |
| `VPS_PORT` | `22` | SSH port |
| `VPS_PROJECT_PATH` | `/home/deploy/hr-system` | Absolute path to the project on the VPS |

Once the secrets are set, every push to `main` rebuilds and restarts the stack on the VPS automatically.

### Useful commands
```bash
docker compose ps                         # service status
docker compose logs -f app                # app logs
docker compose logs -f queue              # worker logs
docker compose exec app php artisan tinker
docker compose exec app php artisan migrate:status
docker compose down                       # stop (data volumes kept)
```

> **Switching the queue backend:** the stack defaults to `QUEUE_CONNECTION=redis`. Set it to `database` in `.env` if you prefer the DB-backed queue — the `queue` container works with either.
