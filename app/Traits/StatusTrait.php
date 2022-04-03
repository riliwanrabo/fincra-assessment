<?php

namespace App\Traits;

use App\Events\TaskUpdateEvent;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait StatusTrait
{
    /**
     * Status relationship for Retrieving
     *
     * @return MorphToMany
     */
    public function status(): MorphToMany
    {
        return $this->morphToMany(Status::class, 'statusable');
    }

    /*
     * Set Status
     *
     * @param string|int $status
     * @return void
     */
    public function setStatus($status)
    {
        $_status = Status::where('id', $status)
            ->orWhere('name', $status)
            ->first();

        if ($_status) {
            $this->status()
                ->sync($_status->id);
            return $this;
        }
    }

    /**
     * Get with by Status
     *
     * @param string|int $statuses
     * @return void
     */
    public function scopeByStatus($query, $status)
    {
        return $query->whereHas('status', function ($q) use ($status) {
            return $q->where('name', $status);
        });
    }


    /**
     * Check has Status
     *
     * @param string|int $status
     * @return void
     */
    public function hasStatus($status)
    {
        $_status = $this->status->first();
        return  $_status ? $this->status->first()->name == $status : false;
    }

    /**
     * Check has Status
     *
     * @param string|int $status
     * @return void
     */
    public function scopeDoesntHaveStatus($query, $status)
    {
        return $query->whereDoesntHave('status', function ($q) use ($status) {
            return $q->where('name', $status);
        });
    }

    /**
     * Get with Status Name
     *
     * @param string|int $statuses
     * @return void
     */
    public function scopeWithStatus()
    {
        static::retrieved(function ($model) {
            $model->current_status = $model->status->first()->name;
            $model->makeHidden('status');
        });
    }

    /**
     * Load Status Name
     *
     * @param string|int $statuses
     * @return Model
     */
    public function loadStatus()
    {
        $this->current_status = $this->status->first()->name;
        $this->makeHidden('status');

        return $this;
    }
}
