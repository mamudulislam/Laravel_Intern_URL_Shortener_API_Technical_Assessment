<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'original_url', 'short_code', 'click_count'];

    protected $casts = ['click_count' => 'integer'];

    protected $appends = ['short_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getShortUrlAttribute(): string
    {
        return url('/'.$this->short_code);
    }
}
