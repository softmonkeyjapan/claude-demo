<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ContactLabel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['contact_id', 'email', 'label', 'is_primary'])]
class ContactEmail extends Model
{
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'label' => ContactLabel::class,
            'is_primary' => 'boolean',
        ];
    }
}
