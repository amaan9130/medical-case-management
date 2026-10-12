<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MedicalCaseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $viewer;
    protected User $engineer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'viewer',
        ]);

        $this->engineer = User::create([
            'name' => 'Engineer User',
            'email' => 'engineer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'engineer',
        ]);
    }

    /**
     * Requirement 1: Valid login succeeds.
     */
    public function test_valid_login_succeeds(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    /**
     * Requirement 2: Invalid login is rejected.
     */
    public function test_invalid_login_is_rejected(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Requirement 3: A guest cannot open case pages or APIs (401 / redirect).
     */
    public function test_guest_cannot_open_case_pages_or_apis(): void
    {
        // Web pages redirect unauthenticated guest to login
        $webPages = ['/dashboard', '/cases', '/cases/create'];
        foreach ($webPages as $url) {
            $response = $this->get($url);
            $response->assertRedirect('/login');
        }

        // API endpoints return 401 Unauthorized for unauthenticated requests
        $apiEndpoints = [
            'get' => ['/api/cases', '/api/cases/1'],
            'post' => ['/api/cases'],
        ];

        foreach ($apiEndpoints['get'] as $url) {
            $response = $this->getJson($url);
            $response->assertStatus(401)
                     ->assertJson(['success' => false]);
        }

        $postResponse = $this->postJson('/api/cases', [
            'case_number' => 'CASE-TEST',
        ]);
        $postResponse->assertStatus(401)
                     ->assertJson(['success' => false]);
    }

    /**
     * Requirement 4: Admin can create a case.
     */
    public function test_admin_can_create_a_case(): void
    {
        $caseData = [
            'case_number' => 'CASE-TEST-001',
            'patient_reference' => 'PAT-1001',
            'surgeon_name' => 'Dr. Eleanor Vance',
            'implant_type' => 'Titanium Cranial Plate',
            'status' => 'Planning',
            'priority' => 'High',
            'surgery_date' => '2026-11-15',
        ];

        // Web creation
        $response = $this->actingAs($this->admin)->post('/cases', $caseData);

        $createdCase = MedicalCase::where('case_number', 'CASE-TEST-001')->first();
        $this->assertNotNull($createdCase);
        $response->assertRedirect(route('cases.show', $createdCase));

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-TEST-001',
            'patient_reference' => 'PAT-1001',
            'surgeon_name' => 'Dr. Eleanor Vance',
            'created_by' => $this->admin->id,
        ]);
    }

    /**
     * Requirement 5: Creating a case with a duplicate Case Number fails (422).
     */
    public function test_creating_a_case_with_duplicate_case_number_fails(): void
    {
        // First create an initial case
        MedicalCase::create([
            'case_number' => 'CASE-DUPLICATE',
            'patient_reference' => 'PAT-0001',
            'surgeon_name' => 'Dr. Marcus Thorne',
            'implant_type' => 'Acetabular Cup',
            'status' => 'Planning',
            'priority' => 'Medium',
            'created_by' => $this->admin->id,
        ]);

        $duplicatePayload = [
            'case_number' => 'CASE-DUPLICATE',
            'patient_reference' => 'PAT-0002',
            'surgeon_name' => 'Dr. Sarah Jenkins',
            'implant_type' => 'Spine Cage',
            'status' => 'Approved',
            'priority' => 'High',
        ];

        // Web form submission should fail validation with session errors
        $webResponse = $this->actingAs($this->admin)->post('/cases', $duplicatePayload);
        $webResponse->assertSessionHasErrors('case_number');

        // API submission should return HTTP 422 with field-level errors
        $token = $this->admin->createToken('admin-token')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cases', $duplicatePayload);

        $apiResponse->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonStructure([
                'errors' => ['case_number'],
            ]);
    }

    /**
     * Requirement 6: Viewer cannot delete a case (403).
     */
    public function test_viewer_cannot_delete_a_case(): void
    {
        $case = MedicalCase::create([
            'case_number' => 'CASE-NODELETE',
            'patient_reference' => 'PAT-9999',
            'surgeon_name' => 'Dr. Robert Chen',
            'implant_type' => 'Shoulder Glenoid',
            'status' => 'Planning',
            'priority' => 'Low',
            'created_by' => $this->admin->id,
        ]);

        // Web request: Viewer attempting to delete must get 403 Forbidden
        $response = $this->actingAs($this->viewer)->delete('/cases/' . $case->id);
        $response->assertStatus(403);
        $this->assertDatabaseHas('cases', ['id' => $case->id]);

        // API request: Viewer attempting to delete must get 403 Forbidden
        $viewerToken = $this->viewer->createToken('viewer-token')->plainTextToken;
        $apiResponse = $this->withHeader('Authorization', 'Bearer ' . $viewerToken)
            ->deleteJson('/api/cases/' . $case->id);

        $apiResponse->assertStatus(403)
                    ->assertJson(['success' => false]);
        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Bonus Test 1: Engineer can create and update a case, but cannot delete.
     */
    public function test_engineer_permissions_can_manage_but_cannot_delete(): void
    {
        // 1. Engineer can create
        $createResponse = $this->actingAs($this->engineer)->post('/cases', [
            'case_number' => 'CASE-ENG-01',
            'patient_reference' => 'PAT-ENG',
            'surgeon_name' => 'Dr. Engineer Check',
            'implant_type' => 'Mandibular Scaffold',
            'status' => 'Planning',
            'priority' => 'High',
        ]);
        $case = MedicalCase::where('case_number', 'CASE-ENG-01')->first();
        $this->assertNotNull($case);
        $createResponse->assertRedirect(route('cases.show', $case));

        // 2. Engineer can update
        $updateResponse = $this->actingAs($this->engineer)->put('/cases/' . $case->id, [
            'case_number' => 'CASE-ENG-01',
            'patient_reference' => 'PAT-ENG',
            'surgeon_name' => 'Dr. Engineer Check',
            'implant_type' => 'Mandibular Scaffold',
            'status' => 'Approved',
            'priority' => 'High',
        ]);
        $updateResponse->assertRedirect(route('cases.show', $case));
        $this->assertDatabaseHas('cases', ['id' => $case->id, 'status' => 'Approved']);

        // 3. Engineer cannot delete (403 Forbidden)
        $deleteResponse = $this->actingAs($this->engineer)->delete('/cases/' . $case->id);
        $deleteResponse->assertStatus(403);
        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Bonus Test 2: REST API login, token issuance, and logout revocation.
     */
    public function test_api_login_and_logout_flow(): void
    {
        // 1. Valid API login
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Authentication successful.',
            ])
            ->assertJsonStructure([
                'data' => ['token', 'user'],
            ]);

        $token = $loginResponse->json('data.token');

        // 2. Access protected endpoint with token
        $casesResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/cases');
        $casesResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // 3. Logout revokes token
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');
        $logoutResponse->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    /**
     * Bonus Test 3: Search and status filter on case catalog.
     */
    public function test_search_and_status_filtering(): void
    {
        MedicalCase::create([
            'case_number' => 'CASE-ALPHA',
            'patient_reference' => 'PAT-1',
            'surgeon_name' => 'Dr. Gregory House',
            'implant_type' => 'Titanium Plate',
            'status' => 'Planning',
            'priority' => 'High',
            'created_by' => $this->admin->id,
        ]);

        MedicalCase::create([
            'case_number' => 'CASE-BETA',
            'patient_reference' => 'PAT-2',
            'surgeon_name' => 'Dr. Allison Cameron',
            'implant_type' => 'Hip Joint',
            'status' => 'Approved',
            'priority' => 'Low',
            'created_by' => $this->admin->id,
        ]);

        // Search by Surgeon Name
        $response = $this->actingAs($this->admin)->get('/cases?search=House');
        $response->assertOk();
        $response->assertSee('CASE-ALPHA');
        $response->assertDontSee('CASE-BETA');

        // Filter by Status
        $filterResponse = $this->actingAs($this->admin)->get('/cases?status=Approved');
        $filterResponse->assertOk();
        $filterResponse->assertSee('CASE-BETA');
        $filterResponse->assertDontSee('CASE-ALPHA');
    }
}
