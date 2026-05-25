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
