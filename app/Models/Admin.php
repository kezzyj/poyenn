<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'platform_id',
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Role helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isPlatformAdmin(): bool
    {
        return $this->role === 'platform_admin';
    }

    // Relationships
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function sendPasswordResetNotification($token)
{
    $notifier = app(\App\Services\NotificationService::class);

    $resetUrl = url(route('admin.password.reset', ['token' => $token, 'email' => $this->email], false));

    $notifier->sendEmail(
        toEmail: $this->email,
        toName: $this->name,
        subject: 'Reset Password Notification',
        htmlContent: "<p>You are receiving this email because we received a password reset request for your admin account.</p><p><a href=\"{$resetUrl}\">Reset Password</a></p><p>This password reset link will expire in 60 minutes.</p><p>If you did not request a password reset, no further action is required.</p>",
    );
}
}