<?php

namespace App\Transformers;

use App\Models\User;

class UsersTransformer
{
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'dial_code' => $user->dial_code,
            'mobile_number' => $user->mobile_number,
            'role_id' => $user->role_id,
            'role_id_name' => $user->role_id_name,
            'status' => (bool) $user->status,
            'email_otp_verified' => (bool) $user->email_otp_verified,
            'mobile_otp_verified' => (bool) $user->mobile_otp_verified,
            'photo_path' => $user->photo_path,
            'address' => $user->address,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
