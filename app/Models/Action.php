<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Action extends Model
{
    use HasFactory;

    const STATUS_PENDING = 0;   // 待审核
    const STATUS_APPROVED = 1;  // 已通过
    const STATUS_DENIED = 2;    // 已拒绝

    /**
     * 该更新动作所属咖啡店
     * @return BelongsTo
     */
    public function cafe()
    {
        return $this->belongsTo(Cafe::class, 'cafe_id', 'id');
    }

    /**
     * 对应前台操作用户
     * @return BelongsTo
     */
    public function by()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * 对应后台处理管理员
     * @return BelongsTo
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }
}
