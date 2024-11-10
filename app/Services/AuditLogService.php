<?php 
namespace App\Services;
use App\Models\AuditLog;

class AuditLogService{

    static public function makeEntry(string $action, string $user_id ){
        AuditLog::create([
            'action'=>$action,
            'action_by'=> $user_id
        ]);

    }

}