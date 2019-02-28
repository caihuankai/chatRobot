<?php

namespace app\common\model;

class CommentAudi extends \app\common\model\ModelBs
{
    /**
     * 改变评论审核状态，默认屏蔽
     *
     * @param $ids
     * @param int $status
     * @return $this
     */
    public function closeStatus($ids, $status = 2, $operateId = 0)
    {
        $ids = array_filter((array)$ids);
        if (empty($ids)) {
            return $this;
        }

        $event = $this->getLocalEvent();
        if (!is_null($event)) {
            $event->liveIds = $ids;
            $event->operateId = $operateId;
        }

        return $this->update(['state' => $status], ['id' => ['in', $ids]]);
    }
}