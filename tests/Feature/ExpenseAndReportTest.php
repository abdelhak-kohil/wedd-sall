<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseAndReportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Business $business;
    private ExpenseCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->user     = User::first();
        $this->business = Business::first();
        $this->category = ExpenseCategory::create([
            'business_id' => $this->business->id,
            'name_fr'     => 'Test Catégorie',
            'color'       => '#10b981',
        ]);
    }

    // ── Index ─────────────────────────────────────────────────────
    public function test_expense_index_loads(): void
    {
        Expense::create([
            'business_id'    => $this->business->id,
            'created_by'     => $this->user->id,
            'description'    => 'Facture électricité',
            'amount'         => 12500,
            'expense_date'   => today()->toDateString(),
            'payment_method' => 'CASH',
        ]);

        $this->actingAs($this->user)
             ->get(route('expenses.index'))
             ->assertOk()
             ->assertSee('Facture électricité');
    }

    // ── Create form ───────────────────────────────────────────────
    public function test_expense_create_form_loads(): void
    {
        $this->actingAs($this->user)
             ->get(route('expenses.create'))
             ->assertOk()
             ->assertSee('Test Catégorie');
    }

    // ── Store: valid ──────────────────────────────────────────────
    public function test_expense_can_be_stored(): void
    {
        $this->actingAs($this->user)
             ->post(route('expenses.store'), [
                 'expense_category_id' => $this->category->id,
                 'description'         => 'Loyer Octobre',
                 'vendor'              => 'Propriétaire',
                 'amount'              => 80000,
                 'expense_date'        => today()->toDateString(),
                 'payment_method'      => 'CHEQUE',
                 'cheque_number'       => 'CH-00123',
                 'bank_name'           => 'BNA',
             ])
             ->assertRedirect(route('expenses.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseHas('expenses', [
            'business_id' => $this->business->id,
            'description' => 'Loyer Octobre',
            'amount'      => 80000,
        ]);
    }

    // ── Store: validation fails ───────────────────────────────────
    public function test_expense_store_requires_description_and_amount(): void
    {
        $this->actingAs($this->user)
             ->post(route('expenses.store'), [
                 'expense_date'   => today()->toDateString(),
                 'payment_method' => 'CASH',
             ])
             ->assertSessionHasErrors(['description', 'amount']);
    }

    // ── Report ────────────────────────────────────────────────────
    public function test_profitability_report_loads(): void
    {
        Expense::create([
            'business_id'    => $this->business->id,
            'created_by'     => $this->user->id,
            'description'    => 'Salaire chef de salle',
            'amount'         => 45000,
            'expense_date'   => today()->toDateString(),
            'payment_method' => 'TRANSFER',
        ]);

        $this->actingAs($this->user)
             ->get(route('expenses.report'))
             ->assertOk()
             ->assertSee((string) now()->year);
    }

    // ── Unauthenticated access ────────────────────────────────────
    public function test_expense_pages_require_auth(): void
    {
        $this->get(route('expenses.index'))->assertRedirect(route('login'));
        $this->get(route('expenses.create'))->assertRedirect(route('login'));
        $this->get(route('expenses.report'))->assertRedirect(route('login'));
    }
}
