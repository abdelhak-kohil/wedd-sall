<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const TYPE_DEPOSIT = 'DEPOSIT';
    public const TYPE_INSTALLMENT = 'INSTALLMENT';
    public const TYPE_FINAL_BALANCE = 'FINAL_BALANCE';
    public const TYPE_REFUND = 'REFUND';

    public const TYPES = [
        self::TYPE_DEPOSIT => ['label_fr' => 'Acompte (Erboun)', 'label_ar' => 'عربون الحجز', 'color' => 'emerald'],
        self::TYPE_INSTALLMENT => ['label_fr' => 'Tranche intermédiaire', 'label_ar' => 'دفعة وسيطة', 'color' => 'blue'],
        self::TYPE_FINAL_BALANCE => ['label_fr' => 'Solde final', 'label_ar' => 'تسديد باقي المبلغ', 'color' => 'teal'],
        self::TYPE_REFUND => ['label_fr' => 'Remboursement', 'label_ar' => 'إرجاع مبلغ', 'color' => 'rose'],
    ];

    public const METHOD_CASH = 'CASH';
    public const METHOD_CHEQUE = 'CHEQUE';
    public const METHOD_TRANSFER = 'TRANSFER';
    public const METHOD_OTHER = 'OTHER';

    public const METHODS = [
        self::METHOD_CASH => ['label_fr' => '💵 Espèces', 'label_ar' => 'نقداً'],
        self::METHOD_CHEQUE => ['label_fr' => '📑 Chèque bancaire', 'label_ar' => 'شيك بنكي'],
        self::METHOD_TRANSFER => ['label_fr' => '🏦 Virement bancaire', 'label_ar' => 'تحويل بنكي'],
        self::METHOD_OTHER => ['label_fr' => '📌 Autre mode', 'label_ar' => 'طريقة أخرى'],
    ];

    protected $fillable = [
        'business_id',
        'reservation_id',
        'cash_register_id',
        'receipt_number',
        'payment_type',
        'payment_method',
        'amount',
        'cheque_number',
        'bank_name',
        'payment_date',
        'notes',
        'received_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function register(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class, 'cash_register_id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->payment_type]['label_fr'] ?? $this->payment_type;
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->payment_method]['label_fr'] ?? $this->payment_method;
    }
}
