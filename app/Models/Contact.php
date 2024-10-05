<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->BelongsTo(Contact::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class, 'contact_id');
    }
}
