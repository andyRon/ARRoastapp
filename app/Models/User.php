<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;  // TODO

//use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_GENERAL_USER = 0;  // 普通用户
    const ROLE_SHOP_OWNER = 1;    // 商家用户
    const ROLE_ADMIN = 2;         // 管理员
    const ROLE_SUPER_ADMIN = 3;   // 超级管理员

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * 上传的咖啡店图片
     * @return HasMany
     */
    public function cafePhotos()
    {
        return $this->hasMany(CafePhoto::class, 'id', 'cafe_id');
    }

    /**
     * User与Cafe间的多对多关联
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(Cafe::class, 'users_cafes_likes', 'user_id', 'cafe_id');
    }

    /**
     * 归属此用户的公司
     * @return BelongsToMany
     */
    public function companiesOwned(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_owners', 'user_id', 'company_id');
    }

    /**
     * 该用户名下所有动作
     * @return HasMany
     */
    public function actions()
    {
        return $this->hasMany(Action::class, 'id', 'user_id');
    }

    /**
     * 该用户名下所有处理的后台审核动作
     * @return HasMany
     */
    public function actionsProcessed()
    {
        return $this->hasMany(Action::class, 'id', 'processed_by');
    }
}
