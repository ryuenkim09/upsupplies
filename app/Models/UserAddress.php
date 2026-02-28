<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * App\Models\UserAddress
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $label
 * @property string $address
 * @property string|null $phone
 */

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'label', 'address', 'phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
