<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPerson extends Model
{
    use HasFactory;

    protected $table = 'delivery_persons';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'vehicle_number',
        'status',
        'current_lat',
        'current_lng'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
