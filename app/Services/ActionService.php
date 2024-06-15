<?php

namespace App\Services;

use App\Models\Action;
use Carbon\Carbon;

/**
 * 封装 actions表新增记录逻辑
 */
class ActionService
{
    /**
     * 创建一条待审核记录
     * @param $cafeID
     * @param $companyID
     * @param $type
     * @param $content
     * @param $userId
     * @return void
     */
    public function createPendingAction($cafeID, $companyID, $type, $content, $userId)
    {
        $action = new Action();

        $action->cafe_id = $cafeID;
        $action->company_id = $companyID;
        $action->user_id = $userId;
        $action->status = Action::STATUS_PENDING;
        $action->type = $type;
        $action->content = json_encode($content);

        $action->save();
    }

    /**
     * 创建一条已处理操作
     * @param $cafeID
     * @param $companyID
     * @param $type
     * @param $content
     * @param $userId
     * @return void
     */
    public function createApprovedAction($cafeID, $companyID, $type, $content, $userId)
    {
        $action = new Action();

        $action->cafe_id = $cafeID;
        $action->company_id = $companyID;
        $action->user_id = $userId;
        $action->status = Action::STATUS_APPROVED;
        $action->type = $type;
        $action->content = json_encode($content);
        $action->processed_by = $userId;
        $action->processed_on = Carbon::now();

        $action->save();
    }

    /**
     * 批准动作
     * @param $action
     * @param $processedBy
     * @return void
     */
    public function approveAction($action, $processedBy)
    {
        $action->status = Action::STATUS_APPROVED;
        $action->processed_by = $processedBy;
        $action->processed_on = Carbon::now();
        $action->save();
    }

    /**
     * 拒绝动作审核，标记为不通过
     * @param Action $action
     * @param int $processedBy
     */
    public function denyAction($action, $processedBy)
    {
        $action->status = Action::STATUS_DENIED;
        $action->processed_by = $processedBy;
        $action->processed_on = Carbon::now();
        $action->save();
    }
}
