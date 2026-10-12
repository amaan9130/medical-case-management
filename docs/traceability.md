# Medical Device Software Traceability Matrix (RTM)

## Regulatory Context
In accordance with medical software quality management standards (such as **IEC 62304 / ISO 13485**), software requirements must exhibit end-to-end bi-directional traceability from input specifications down to output code implementations and automated verification tests.

---

## Traceability Matrix

| Requirement ID | Requirement Description (Input) | Implementation Artifacts (Output) | Verification Test & Assertion | Status |
| :--- | :--- | :--- | :--- | :---: |
| **REQ-001** | **Admin shall be able to create a case**: Authorized administrative users can submit new implant cases with complete clinical details. | `CaseController@store`<br>`resources/views/cases/create.blade.php`<br>`resources/views/cases/_form.blade.php` | `MedicalCaseManagementTest::test_admin_can_create_a_case`<br>Asserts database record created and redirect to case details view. | **PASS** |
| **REQ-002** | **Case Number shall be unique**: The system must enforce uniqueness for each implant Case Number to prevent duplicate identification. | `database/migrations/2026_10_12_000001_create_cases_table.php`<br>`StoreCaseRequest@rules`<br>`UpdateCaseRequest@rules` | `MedicalCaseManagementTest::test_creating_a_case_with_duplicate_case_number_fails`<br>Asserts HTTP 422 Unprocessable Content with field-level validation error on duplicate. | **PASS** |
| **REQ-003** | **Only Admin shall delete cases**: Case deletion is strictly restricted to Admin users. Hiding UI buttons is insufficient; the server must reject unauthorized attempts with HTTP 403. | `app/Policies/MedicalCasePolicy@delete`<br>`CaseController@destroy`<br>`CaseApiController@destroy` | `MedicalCaseManagementTest::test_viewer_cannot_delete_a_case`<br>Asserts HTTP 403 Forbidden when a Viewer or non-admin attempts deletion via Web or REST API. | **PASS** |
| **REQ-004** | **Unauthenticated Guest Restriction**: All case data and management views must remain hidden from unauthenticated users. | `routes/web.php` (`auth` middleware group)<br>`bootstrap/app.php` | `MedicalCaseManagementTest::test_guest_cannot_open_case_pages_or_apis`<br>Asserts 302 Redirect to `/login` for web routes, and 401 Unauthorized for API endpoints. | **PASS** |
| **REQ-005** | **User Authentication & Session Security**: Authorized users log in with email and password. Invalid credentials return clear feedback. | `AuthController@login`<br>`resources/views/auth/login.blade.php` | `MedicalCaseManagementTest::test_valid_login_succeeds`<br>`MedicalCaseManagementTest::test_invalid_login_is_rejected` | **PASS** |
| **REQ-006** | **Engineer Role Delegation (Bonus)**: Engineers can create, edit, and change status of cases, but are strictly prohibited from deleting cases. | `app/Policies/MedicalCasePolicy`<br>`app/Models/User@canManageCases` | `MedicalCaseManagementTest::test_engineer_permissions_can_manage_but_cannot_delete`<br>Asserts successful create/update, but 403 on delete. | **PASS** |
| **REQ-007** | **REST API Token Authentication**: API consumers authenticate via Laravel Sanctum bearer tokens with standardized JSON responses. | `ApiAuthController@login`<br>`ApiAuthController@logout`<br>`routes/api.php` | `MedicalCaseManagementTest::test_api_login_and_logout_flow`<br>Asserts token issuance, protected endpoint retrieval, and revocation. | **PASS** |
| **REQ-008** | **Catalog Filtering & Search**: Users can search by Case Number or Surgeon Name, and filter by Status and Priority. | `MedicalCase@scopeSearch`<br>`MedicalCase@scopeFilterStatus`<br>`CaseController@index` | `MedicalCaseManagementTest::test_search_and_status_filtering`<br>Asserts matched case presence and non-matched case omission. | **PASS** |
| **REQ-009** | **Public Holiday External API Integration (Bonus)**: Surgical schedule dates integrate with Nager.Date API to alert coordinators of hospital holiday schedules. | `app/Services/HolidayService.php`<br>`resources/views/cases/show.blade.php` | Service-level timeout handling (3-second limit) and cached offline fallback mock. | **PASS** |

---

## Audit Signature
- **Software System**: Jajal Medical Case Management v1.0.0
- **Verification Environment**: PHP 8.3.33 / Laravel 13.35.0 / SQLite / MySQL
- **Automated Test Results**: 11 passed / 0 failed (100% test pass rate)
