<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded=['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function vendorAddress()
    {
        return $this->hasOne(VendorAddress::class, 'user_id', 'id');
    }

    // Vendors I follow
    public function followingVendors()
    {
        return $this->belongsToMany(
            User::class,
            'vendor_followers',
            'customer_id',
            'vendor_id'
        );
    }
    
    // My followers (if vendor)
    public function followers()
    {
        return $this->belongsToMany(
            User::class,
            'vendor_followers',
            'vendor_id',
            'customer_id'
        );
    }

}
