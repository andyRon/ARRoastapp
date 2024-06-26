<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Cafe extends Model
{
    use HasFactory, SoftDeletes;

    /**
     *  Cafe与rewMethod之间的多对多关联
     * @return BelongsToMany
     */
    public function brewMethods(): BelongsToMany
    {
        return $this->belongsToMany(BrewMethod::class, 'cafes_brew_methods', 'cafe_id', 'brew_method_id');
    }

    public function test()
    {

    }

    /**
     * 关联分店
     * @return HasMany
     */
    public function children()
    {
        return $this->hasMany(Cafe::class, 'parent', 'id');
    }

    /**
     * 归属总店
     * @return HasOne
     */
    public function parent()
    {
        return $this->hasOne(Cafe::class, 'id', 'parent');
    }

    /**
     * 咖啡店与标签之间的多对多关联方法
     * @return BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'cafes_users_tags', 'cafe_id', 'tag_id');
    }

    /**
     * 咖啡店图片
     * @return HasMany
     */
    public function photos()
    {
        return $this->hasMany(CafePhoto::class, 'id', 'cafe_id');
    }

    /**
     * cafe与User间的多对对关联
     * @return BelongsToMany
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_cafes_likes', 'cafe_id', 'user_id');
    }

    public function userLike(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_cafes_likes', 'cafe_id', 'user_id')->where('user_id', auth()->id());
    }

    /**
     * 归属公司
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
