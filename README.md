# Medical Case Management System

A production-ready **Medical Case Management Web Application & REST API** built with **Laravel, MySQL, and Blade** for medical device manufacturers to track customized and standard patient implant cases from initial draft to surgical completion.

Designed and developed to satisfy all core requirements and bonus objectives of the **Jajal Medical Software Developer Assignment**.

---

## Tech Stack & Architecture

- **Backend Framework**: Laravel 13.x (PHP 8.3+)
- **Database**: MySQL 8.0 / SQLite (zero-config local testing and CI)
- **Frontend / Templating**: Laravel Blade + Tailwind CSS (responsive on laptops, tablets, and phones)
- **Authentication**: Session-based auth for web; **Laravel Sanctum** bearer token auth for REST APIs
- **Authorization**: Laravel Policies & Gates (`MedicalCasePolicy`)
- **Validation**: Specialized Laravel Form Requests (`StoreCaseRequest`, `UpdateCaseRequest`)
- **Testing**: PHPUnit / Pest automated feature test suite (11 feature tests)
- **External API Integration**: Nager.Date Public Holiday API via Laravel HTTP Client with timeout and fallback

---

## User Roles & Permissions Matrix

| Capability / Action | Admin | Engineer *(Bonus)* | Viewer | Guest / Unauthenticated |
| :--- | :---: | :---: | :---: | :---: |
| **View Dashboard & Catalog** | Yes | Yes | Yes | Redirect to Login (302) |
| **View Case Details** | Yes | Yes | Yes | Redirect to Login (302) |
| **Create New Case** | Yes | Yes | Blocked (403) | Redirect to Login (302) |
| **Edit Case Details** | Yes | Yes | Blocked (403) | Redirect to Login (302) |
| **Change Status** | Yes | Yes | Blocked (403) | Redirect to Login (302) |
| **Delete Case** | **Yes** | Blocked (403) | Blocked (403) | Redirect to Login (302) |
| **REST API Access** | Full CRUD | View/Create/Edit | Read Only | 401 Unauthorized |

> [!IMPORTANT]
> **Server-Side Enforcement**: Case deletion and mutation are strictly blocked on the backend via `MedicalCasePolicy`. Even if a Viewer crafts a manual HTTP `DELETE` or `POST` request, the server immediately rejects the request with **HTTP 403 Forbidden**.

---

## Test Login Credentials (Dummy Data)

The database seeder automatically initializes three test accounts with pre-populated dummy implant cases:

| Role | Email Address | Password | Permissions Summary |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@jajalmedical.com` | `password123` | Full access: View, Create, Edit, Change Status, Delete |
| **Viewer** | `viewer@jajalmedical.com` | `password123` | Read-only access: View Dashboard, List, and Details only |
| **Engineer** *(Bonus)* | `engineer@jajalmedical.com` | `password123` | Technical access: View, Create, Edit, Change Status (Cannot Delete) |

*(Quick-fill buttons are also conveniently provided on the web login screen for instant one-click testing.)*

---

## Getting Started & Local Setup

### 1. Prerequisites
- **PHP**: 8.2 or 8.3 with extensions (`pdo`, `sqlite3` or `pdo_mysql`, `curl`, `mbstring`, `openssl`, `fileinfo`)
- **Composer**: 2.x
- **Git**

### 2. Clone and Install Dependencies
```bash
git clone <repository-url> medical-case-management
cd medical-case-management
composer install
```

### 3. Environment Configuration
Copy the template `.env.example` to `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Setup & Seeding

#### Option A: Zero-Configuration SQLite (Default & Instant)
By default, the `.env` is pre-configured with SQLite. Run migrations and the seeder:
```bash
touch database/database.sqlite   # On Windows PowerShell: New-Item -ItemType File -Path database/database.sqlite -Force
php artisan migrate:fresh --seed
```

#### Option B: MySQL Setup
If using MySQL (e.g. XAMPP, Docker, or standalone MySQL):
1. Create a MySQL database:
   ```sql
   CREATE DATABASE medical_case_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. In `.env`, set:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=medical_case_management
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```
3. Run migrations and seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```

### 5. Start the Local Server
```bash
php artisan serve
```
The application will be accessible at: **[http://localhost:8000](http://localhost:8000)**

---

## Running Automated Tests

Run the full automated test suite verifying all 6 core feature requirements and bonus scenarios:
```bash
php artisan test
```

### Included Test Cases
1. `test_valid_login_succeeds` — Valid credentials redirect to dashboard and establish authenticated session.
2. `test_invalid_login_is_rejected` — Wrong password or unmapped email is rejected with error feedback.
3. `test_guest_cannot_open_case_pages_or_apis` — Unauthenticated web access redirects to login (302); unauthenticated API calls return HTTP 401 Unauthorized.
4. `test_admin_can_create_a_case` — Administrative user can successfully register an implant case.
5. `test_creating_a_case_with_duplicate_case_number_fails` — Unique database constraint and Form Request reject duplicate Case Numbers with HTTP 422.
6. `test_viewer_cannot_delete_a_case` — Policy checks block Viewer from deleting cases with HTTP 403 Forbidden.
7. `test_engineer_permissions_can_manage_but_cannot_delete` — *(Bonus)* Tests Engineer create/update permissions and delete prevention.
8. `test_api_login_and_logout_flow` — *(Bonus)* Tests Sanctum token generation, authenticated request, and token revocation.
9. `test_search_and_status_filtering` — *(Bonus)* Tests live catalog keyword search and status query filtering.

---

## REST API Specification

All API endpoints return JSON in a uniform response envelope:
```json
{
  "success": true,
  "message": "Human readable status message",
  "data": { ... }
}
```

On validation failure (**HTTP 422**):
```json
{
  "success": false,
  "message": "The given data failed validation.",
  "errors": {
    "case_number": ["The Case Number is mandatory."]
  }
}
```

### Endpoint Reference Table

| Method | Endpoint | Description | Auth Required | Authorized Roles | Status Codes |
| :--- | :--- | :--- | :---: | :---: | :---: |
| `POST` | `/api/login` | Authenticate and obtain Sanctum API token | No | Public | `200`, `401`, `422` |
| `POST` | `/api/logout` | Revoke active access token | Yes | Authenticated users | `200`, `401` |
| `GET` | `/api/cases` | List cases (paginated, search & filter) | Yes | Admin, Engineer, Viewer | `200`, `401` |
| `POST` | `/api/cases` | Register a new implant case | Yes | Admin only | `201`, `401`, `403`, `422` |
| `GET` | `/api/cases/{id}`| Fetch specific case details | Yes | Admin, Engineer, Viewer | `200`, `401`, `404` |
| `PUT` | `/api/cases/{id}`| Update an existing case | Yes | Admin only | `200`, `401`, `403`, `404`, `422` |
| `DELETE`| `/api/cases/{id}`| Permanently delete a case | Yes | Admin only | `200`, `401`, `403`, `404` |

### Testing with Postman
A ready-to-use Postman/Thunder Client collection is included at:
[`docs/postman_collection.json`](docs/postman_collection.json)

Import the collection into Postman, run `1. Login as Admin`, and the script will automatically set the `admin_token` environment variable for subsequent CRUD requests.

---

## Medical Device Software Deliverables & Traceability

1. **Database Schema & Entity Diagrams**:
   - Comprehensive documentation located at [`docs/database.md`](docs/database.md).
2. **Medical Device Software Traceability Matrix (RTM)**:
   - Full requirement-to-code-to-test mapping located at [`docs/traceability.md`](docs/traceability.md).
3. **AWS Cloud Production Architecture**:
   - Multi-AZ cloud guide covering EC2, RDS MySQL, S3, security groups, secrets, and HTTPS at [`docs/deployment.md`](docs/deployment.md).
4. **Public Holiday API Integration**:
   - Integrated via `App\Services\HolidayService` querying the **Nager.Date** API with a 3-second timeout and cached fallback to alert clinical teams of hospital operating room closures.

---

## Assumptions & Design Decisions

1. **Data Privacy (HIPAA / GDPR)**:
   - Real patient names are strictly prohibited. Patient identifiers use pseudonymized codes (e.g. `PAT-8401`).
2. **Unique Case Numbers on Edit**:
   - The validation rule in `UpdateCaseRequest` utilizes `Rule::unique('cases', 'case_number')->ignore($caseId)` so updating other case fields does not trigger duplicate errors for the current case.
3. **Mass Assignment Protection**:
   - Protected with explicit `$fillable` arrays on `MedicalCase` and `User` models.
4. **State Transitions**:
   - Valid statuses are constrained to: `Draft`, `Planning`, `In Review`, `Approved`, `Completed`, `Cancelled`.
   - Valid priorities are constrained to: `Low`, `Medium`, `High`.
