<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Cafe extends Model
{
    use HasFactory;

    // 定义与 BrewMethod 模型间的多对多关联
    public function brewMethods()
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
}
