<?php

namespace App\Models\Traits\Scope;

use App\Models\User;

trait UserScope 
{
    /**
     * User is active
     */
    public function scopeIsActive($query)
    {
        return $query->where('status', User::IS_ACTIVE);
    }

    /**
     * User is customer
     */
    public function scopeIsCustomer($query)
    {
        return $query->where('user_type', User::IS_USER);
    }

    /**
     * User is worker
     */
    public function scopeIsWorker($query)
    {
        return $query->where('user_type', User::IS_WORKER);
    }

    /**
     * User is pending
     */
    public function scopeIsPending($query)
    {
        return $query->where('status', User::IS_PENDING)->where('created_at', '>=', now()->subMonths(6)->format('Y-m-d'));
    }

    /**
     * Worker is online
     */
    public function scopeWorkerOn($query)
    {
        return $query->where('worker_status', User::WORKER_ON);
    }

    /**
     * User working off
     */
    public function scopeWorkingOff($query)
    {
        return $query->where('is_working', User::WORKING_OFF);
    }
}