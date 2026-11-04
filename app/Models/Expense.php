<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    public const METHOD_CASH     = 'CASH';
    public const METHOD_CHEQUE   = 'CHEQUE';
    public const METHOD_TRANSFER = 'TRANSFER';
    public const METHOD_OTHER    = 'OTHER';

    public const METHODS = [
        self::METHOD_CASH     => 'Espèces',
        self::METHOD_CHEQUE   => 'Chèque',
        self::METHOD_TRANSFER => 'Virement',
        self::METHOD_OTHER    => 'Autre',
    ];

    protected $fillable = [
        'business_id',
        'expense_category_id',
        'created_by',
        'reference',
        'vendor',
        'description',
        'amount',
        'expense_date',
        'payment_method',
        'cheque_number',
        'bank_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount'       => 'decimal:2',
        ];
    }

    // ── Relations ────────────────────────────────────────────────
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ──────────────────────────────────────────────────
    public function methodLabel(): string
    {
        return self::METHODS[$this->payment_method] ?? $this->payment_method;
    }
}
