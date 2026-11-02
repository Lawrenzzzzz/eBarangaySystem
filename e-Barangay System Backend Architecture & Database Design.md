# **e-Barangay System: Backend Architecture & Database Design**

*Backend documentation · PHP 8.3 · MySQL 8.0 · REST API v1 · Version 1.0 · 27 October 2026*

## **1\. Purpose and scope**

This document defines how the e-Barangay backend is built: the system modules, the folder and file layout, the MySQL database (tables, views, triggers, events), and the REST API. It is derived from the 20 Figma screens in four groups:

* **Public site:** landing page, sign-in, register.  
* **Resident portal:** Home, Documents, Blotter, Announcements, Track.  
* **Chairman's admin:** Dashboard, Budget, Residents, Reports, Settings.  
* **Officials admin:** Dashboard, Residents, Document Requests, Blotter, Announcements, Payments, Reports, Settings.

This is a design document. It contains SQL and short snippets only where they clarify a design decision. Section 12 lists inconsistencies in the Figma screens that should be settled before coding.

**Document map:** 1 Purpose · 2 System overview · 3 Technology stack · 4 Architecture · 5 Folder and file structure · 6 Database design · 7 Views · 8 Triggers · 9 Events · 10 API design · 11 Security, operations and roadmap · 12 Open decisions.

## **2\. System overview**

### **2.1 Actors and roles**

| Role code | Who | What they use |
| :---- | :---- | :---- |
| resident | Registered, verified resident | Resident portal |
| staff | Encoder or clerk at the barangay hall | Officials admin (limited: encode residents, log blotter, take payments) |
| kagawad | Barangay councilor | Officials admin (review requests and blotter) |
| secretary | Barangay Secretary | Officials admin (full operations, issue documents) |
| treasurer | Barangay Treasurer | Officials admin (payments, budget entry) |
| captain | Punong Barangay (the Chairman) | Officials admin plus Chairman's dashboard (budget, analytics, reports) |
| super\_admin | Municipal administrator (sidebar label: MUNICIPAL ADMIN) | All barangays in the municipality |

Roles map to fine-grained permissions (section 6.3, tables roles, permissions, role\_permissions), so the same API serves every portal and the server decides what each person may do.

### **2.2 Modules**

| Module | Responsibility | Screens served |
| :---- | :---- | :---- |
| Auth and Accounts | Registration, login, tokens, password reset, email verification, lockout | Sign in, Register |
| Residents and Households | Resident records, households, puroks and sitios, verification of resident identity | Residents (both admins), resident Home profile card |
| Document Requests | Request, review, approve, pay, print, release barangay documents | Documents, Track, Document Requests, dashboard queue |
| Payments | Fees, official receipts, free services, payment status | Payments, dashboard fees collected |
| Blotter | Complaints, parties, hearings (Lupong Tagapamayapa), resolution | Blotter (resident and admin), Track |
| Announcements | Advisories, notices, events, scheduled publishing | Announcements, landing Community Board |
| Community Programs | Assistance programs (health, senior, livelihood) and applications | Landing: Community assistance |
| Budget and Finance | Fiscal years, sector allocations, itemized outlays | Chairman: Dashboard, Budget |
| Reports | PDF and CSV exports of official records | Reports (both admins) |
| Notifications and Audit | In-app and email notices, immutable activity ledger | Recent Activity, resident notices |
| Settings | Barangay profile, document fees, user accounts | Settings (both admins) |

### **2.3 Multi-barangay tenancy**

The landing page is a municipal portal (Select your barangay on Register), and the admin sidebar says MUNICIPAL ADMIN. The database therefore supports many barangays in one install:

* Every tenant-owned table carries barangay\_id, and every repository query filters by it.  
* A TenantMiddleware reads barangay\_id from the authenticated user and puts it in the request context. Repositories never accept barangay\_id from client input, except for super\_admin, who may send the X-Barangay-Id header.  
* Global (shared) tables: municipalities, roles, permissions, role\_permissions, budget\_sectors, blotter\_categories.

## **3\. Technology stack**

| Concern | Choice | Notes |
| :---- | :---- | :---- |
| Language | PHP 8.3 | declare(strict\_types=1), enums, readonly properties, constructor promotion |
| Autoloading | Composer, PSR-4 (App\\\\ maps to src/) |  |
| Database | MySQL 8.0, InnoDB, utf8mb4 | Needs event scheduler, triggers, CHECK constraints, generated columns, JSON type |
| DB access | PDO with prepared statements only | Exceptions mode on; emulated prepares off |
| Routing | nikic/fast-route | Fast and tiny; routes live in routes/\*.php |
| Dependency injection | php-di/php-di | Autowiring from bootstrap/container.php |
| Config | vlucas/phpdotenv | Secrets only in .env, never committed |
| Auth | firebase/php-jwt | Short-lived access JWT plus rotating refresh token |
| Validation | respect/validation or a small in-house Validator | Used by FormRequest classes |
| PDF | dompdf/dompdf | Certificates, clearances, reports |
| QR codes | endroid/qr-code | Document verification QR |
| CSV | league/csv | Report exports |
| Mail | phpmailer/phpmailer | SMTP |
| Logging | monolog/monolog | File logs in storage/logs/ |
| IDs | ramsey/uuid | Public identifiers in URLs |
| Tests | phpunit/phpunit | Unit and API tests against a test database |
| Web server | Nginx or Apache | Document root is public/ only |

The layers (Controller, Service, Repository) are framework-agnostic. If you later prefer Laravel or Slim, the same structure maps almost one to one.

## **4\. Architecture**

### **4.1 Layers**

Browser / SPA  (Figma UI)  
      \|  HTTPS, JSON  
      v  
public/index.php  (front controller)  
      v  
Kernel \-\> Middleware pipeline  
      Cors \-\> RateLimit \-\> Auth(JWT) \-\> Tenant \-\> Permission \-\> Validation  
      v  
Router (FastRoute)  \-\>  Controller  
      v  
Service  (business rules, transactions, authorization checks)  
      v  
Repository  (SQL via PDO, one per aggregate)  
      v  
MySQL 8  (tables \+ views \+ triggers \+ events)

Side services used by Services:  
FileStorage · PdfGenerator · QrGenerator · Mailer · AuditLogger · NotificationService

### **4.2 Layer rules**

* **Controller:** thin. It reads the request, calls one service method, returns a Resource. No SQL, no business rules.  
* **Request (FormRequest):** validates and normalizes input before the controller runs. Invalid input returns HTTP 422\.  
* **Service:** owns business rules and transactions. Examples: only a pending request can be approved; a paid fee is required before release; a closed fiscal year is read-only.  
* **Repository:** owns all SQL. Returns arrays or DTOs, never HTTP objects.  
* **Resource:** shapes DB rows into the exact JSON the UI expects (camelCase, formatted peso values, status labels).  
* **Policy:** answers can this user do X to this record (for example, residents may only read their own requests).  
* **Database:** enforces integrity that must never be bypassed: foreign keys, unique reference numbers, status transition checks, immutable audit and payment records. The rules in triggers are the safety net; services stay the primary place for rules.

### **4.3 Request lifecycle**

1. Request hits public/index.php, which loads bootstrap/app.php (env, container, Kernel).  
2. Kernel runs middleware in order: CORS, rate limit, JWT authentication, tenant resolution, permission check.  
3. Router dispatches to a Controller method.  
4. FormRequest validates input.  
5. Controller calls a Service. The Service opens a DB transaction when it writes more than one table.  
6. Repositories run prepared statements. Triggers fire inside the same transaction.  
7. Service writes an audit entry and creates notifications when needed.  
8. Controller returns a Resource; Kernel serializes the JSON envelope.  
9. Any exception goes to bootstrap/error\_handler.php, which maps it to a JSON error (section 10.1).

## **5\. Folder and file structure**

### **5.1 Project tree**

e-barangay-api/  
├── public/                              \# ONLY web-accessible folder (document root)  
│   ├── index.php                        \# front controller  
│   └── .htaccess                        \# Apache rewrite to index.php (Nginx: try\_files)  
│  
├── bootstrap/  
│   ├── app.php                          \# load .env, build container, create Kernel  
│   ├── container.php                    \# DI bindings (PDO, mailer, storage, repositories)  
│   └── error\_handler.php                \# exception to JSON mapper  
│  
├── config/  
│   ├── app.php                          \# name, env, debug, timezone Asia/Manila  
│   ├── database.php                     \# host, port, name, charset, PDO options  
│   ├── auth.php                         \# JWT TTLs, lockout rules, password policy  
│   ├── cors.php                         \# allowed origins, headers, methods  
│   ├── mail.php                         \# SMTP settings  
│   ├── filesystems.php                  \# storage paths, allowed MIME types, max sizes  
│   ├── payments.php                     \# gateway keys (GCash / Maya / PayMongo), optional  
│   └── permissions.php                  \# role to permission map (seeded into DB)  
│  
├── routes/  
│   ├── api.php                          \# mounts /api/v1 and includes the files below  
│   ├── public.php                       \# no auth: landing data, barangay list, verify document  
│   ├── auth.php                         \# register, login, refresh, logout, password reset  
│   ├── resident.php                     \# resident portal endpoints  
│   ├── admin.php                        \# officials admin endpoints  
│   └── chairman.php                     \# budget, analytics, chairman reports  
│  
├── src/  
│   ├── Core/                            \# tiny framework kernel (no business logic)  
│   │   ├── Kernel.php  
│   │   ├── Router.php  
│   │   ├── Pipeline.php                 \# runs middleware in order  
│   │   ├── Request.php  
│   │   ├── Response.php  
│   │   ├── Database.php                 \# PDO factory \+ transaction helper  
│   │   ├── Validator.php  
│   │   └── Paginator.php  
│   │  
│   ├── Http/  
│   │   ├── Controllers/  
│   │   │   ├── BaseController.php  
│   │   │   ├── Public/  
│   │   │   │   ├── LandingController.php              \# stats, services, community board  
│   │   │   │   ├── BarangayController.php             \# barangay dropdown for Register  
│   │   │   │   └── DocumentVerificationController.php \# verify QR / serial number  
│   │   │   ├── Auth/  
│   │   │   │   ├── AuthController.php                 \# login, refresh, logout, me  
│   │   │   │   ├── RegistrationController.php  
│   │   │   │   ├── PasswordController.php             \# forgot, reset, change  
│   │   │   │   └── EmailVerificationController.php  
│   │   │   ├── Resident/  
│   │   │   │   ├── ResidentDashboardController.php    \# Home  
│   │   │   │   ├── ResidentProfileController.php  
│   │   │   │   ├── ResidentDocumentController.php     \# Documents  
│   │   │   │   ├── ResidentBlotterController.php      \# Blotter  
│   │   │   │   ├── ResidentAnnouncementController.php \# Announcements  
│   │   │   │   ├── ResidentTrackingController.php     \# Track  
│   │   │   │   └── ResidentNotificationController.php  
│   │   │   ├── Admin/  
│   │   │   │   ├── AdminDashboardController.php  
│   │   │   │   ├── ResidentManagementController.php  
│   │   │   │   ├── ResidentVerificationController.php  
│   │   │   │   ├── HouseholdController.php  
│   │   │   │   ├── PurokController.php  
│   │   │   │   ├── DocumentRequestController.php  
│   │   │   │   ├── DocumentTypeController.php         \# fees and processing days  
│   │   │   │   ├── PaymentController.php  
│   │   │   │   ├── BlotterController.php  
│   │   │   │   ├── BlotterHearingController.php  
│   │   │   │   ├── AnnouncementController.php  
│   │   │   │   ├── ProgramController.php  
│   │   │   │   ├── ReportController.php  
│   │   │   │   ├── UserController.php                 \# staff accounts and roles  
│   │   │   │   ├── SettingsController.php  
│   │   │   │   ├── AuditLogController.php  
│   │   │   │   └── NotificationController.php  
│   │   │   └── Chairman/  
│   │   │       ├── ChairmanDashboardController.php  
│   │   │       ├── FiscalYearController.php  
│   │   │       ├── BudgetController.php               \# allocations by sector  
│   │   │       ├── OutlayController.php               \# itemized outlays  
│   │   │       ├── PopulationAnalyticsController.php  \# Residents analytics page  
│   │   │       └── ChairmanReportController.php  
│   │   ├── Middleware/  
│   │   │   ├── CorsMiddleware.php  
│   │   │   ├── RateLimitMiddleware.php  
│   │   │   ├── AuthMiddleware.php                     \# verifies JWT  
│   │   │   ├── TenantMiddleware.php                   \# sets barangay\_id  
│   │   │   ├── PermissionMiddleware.php               \# route requires permission code  
│   │   │   ├── JsonBodyMiddleware.php  
│   │   │   └── SecurityHeadersMiddleware.php  
│   │   ├── Requests/                    \# validation, grouped by module  
│   │   │   ├── Auth/ (LoginRequest, RegisterRequest, ResetPasswordRequest)  
│   │   │   ├── Resident/ (StoreResidentRequest, UpdateResidentRequest)  
│   │   │   ├── Document/ (StoreDocumentRequest, ReviewDocumentRequest)  
│   │   │   ├── Blotter/ (StoreBlotterRequest, UpdateBlotterRequest, HearingRequest)  
│   │   │   ├── Payment/ (RecordPaymentRequest)  
│   │   │   ├── Announcement/ (StoreAnnouncementRequest)  
│   │   │   └── Budget/ (StoreFiscalYearRequest, StoreAllocationRequest, StoreOutlayRequest)  
│   │   └── Resources/                   \# JSON transformers  
│   │       ├── ResidentResource.php, DocumentRequestResource.php, BlotterResource.php  
│   │       ├── PaymentResource.php, AnnouncementResource.php, BudgetResource.php  
│   │       └── DashboardResource.php  
│   │  
│   ├── Services/  
│   │   ├── AuthService.php, TokenService.php, RegistrationService.php  
│   │   ├── ResidentService.php, HouseholdService.php, ResidentVerificationService.php  
│   │   ├── DocumentRequestService.php, DocumentGeneratorService.php  
│   │   ├── PaymentService.php, BlotterService.php, AnnouncementService.php  
│   │   ├── ProgramService.php, BudgetService.php, DashboardService.php  
│   │   ├── ReportService.php, NotificationService.php, MailService.php  
│   │   ├── AuditService.php, FileStorageService.php, SettingsService.php  
│   │  
│   ├── Repositories/                    \# one per aggregate; all SQL lives here  
│   │   ├── BaseRepository.php  
│   │   ├── UserRepository.php, RoleRepository.php, TokenRepository.php  
│   │   ├── BarangayRepository.php, PurokRepository.php  
│   │   ├── ResidentRepository.php, HouseholdRepository.php  
│   │   ├── DocumentTypeRepository.php, DocumentRequestRepository.php  
│   │   ├── PaymentRepository.php, BlotterRepository.php  
│   │   ├── AnnouncementRepository.php, ProgramRepository.php  
│   │   ├── FiscalYearRepository.php, BudgetRepository.php, OutlayRepository.php  
│   │   ├── ReportRepository.php, NotificationRepository.php  
│   │   └── AuditLogRepository.php, SettingRepository.php  
│   │  
│   ├── Domain/  
│   │   ├── Enums/                       \# RoleCode, RequestStatus, PaymentStatus, BlotterStatus,  
│   │   │                                \# AnnouncementStatus, OutlayStatus, Sex, CivilStatus  
│   │   └── DTO/                         \# small readonly data carriers  
│   │  
│   ├── Policies/                        \# DocumentRequestPolicy, BlotterPolicy, ResidentPolicy, ...  
│   ├── Exceptions/                      \# ValidationException, NotFoundException,  
│   │                                    \# ForbiddenException, UnauthorizedException, ConflictException  
│   ├── Support/                         \# Arr, Str, Money, DateHelper, AgeGroup, Uuid helpers  
│   └── Jobs/                            \# CLI tasks run by cron (fallback for DB events)  
│       ├── PurgeExpiredTokensJob.php  
│       ├── NotifyDueRequestsJob.php  
│       ├── PublishScheduledAnnouncementsJob.php  
│       └── CleanReportFilesJob.php  
│  
├── database/  
│   ├── schema.sql                       \# full schema snapshot (tables, views, triggers, events)  
│   ├── migrations/                      \# ordered, forward-only  
│   │   ├── 001\_geography.sql            \# municipalities, barangays, puroks  
│   │   ├── 002\_auth.sql                 \# roles, permissions, users, tokens, audit, notifications  
│   │   ├── 003\_residents.sql  
│   │   ├── 004\_documents\_payments.sql  
│   │   ├── 005\_blotter.sql  
│   │   ├── 006\_announcements\_programs.sql  
│   │   ├── 007\_budget.sql  
│   │   ├── 008\_reports\_settings.sql  
│   │   ├── 009\_views.sql  
│   │   ├── 010\_triggers.sql  
│   │   └── 011\_events.sql  
│   ├── views/                           \# one .sql file per view (source of truth)  
│   ├── triggers/                        \# one .sql file per trigger  
│   ├── events/                          \# one .sql file per scheduled event  
│   └── seeds/                           \# roles, permissions, document types, sectors, demo data  
│  
├── templates/  
│   ├── pdf/                             \# certificate, clearance, residency, indigency, ID, reports  
│   └── mail/                            \# welcome, verify email, reset password, request status  
│  
├── storage/                             \# NOT web accessible  
│   ├── uploads/                         \# IDs, blotter evidence (private)  
│   ├── documents/                       \# issued certificates (PDF)  
│   ├── reports/                         \# generated reports (PDF/CSV)  
│   ├── logs/  
│   └── cache/  
│  
├── scripts/  
│   ├── migrate.php                      \# runs migrations in order  
│   ├── seed.php  
│   └── cron/                            \# cron entry points calling src/Jobs  
│  
├── tests/  
│   ├── Unit/                            \# services, enums, helpers  
│   ├── Feature/                         \# API tests per module  
│   └── bootstrap.php  
│  
├── docs/                                \# this document, OpenAPI file (openapi.yaml)  
├── .env.example  
├── .gitignore  
├── composer.json  
├── phpunit.xml  
└── README.md

### **5.2 Where things go**

| If you are adding... | Put it in... |
| :---- | :---- |
| A new URL | routes/\*.php (pick the file by audience) |
| Request validation | src/Http/Requests/\<Module\>/ |
| Business rule or transaction | src/Services/ |
| SQL | src/Repositories/ only |
| A fixed set of values (statuses, roles) | src/Domain/Enums/ |
| An HTML or PDF layout | templates/ |
| An uploaded or generated file | storage/ (never public/) |
| A schema change | New file in database/migrations/, then update database/schema.sql |
| A cron job | src/Jobs/ plus an entry script in scripts/cron/ |

