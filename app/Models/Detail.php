<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;

    public const FILLABLE = [
        'key',
        'value',
        'icon',
        'status',
        'type',
        'user_id',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = self::FILLABLE;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
