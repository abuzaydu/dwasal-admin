<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignRolePermissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $role;
    protected $permissions;
    /**
     * Create a new job instance.
     */
    public function __construct($role, $permissions)
    {
        $this->role = $role;
        $this->permissions = $permissions;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->role->syncPermissions();
        foreach ($this->permissions as $perm) {
            $this->role->givePermissionTo($perm);
        }
    }
}
