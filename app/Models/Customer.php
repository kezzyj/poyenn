<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'platform_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationships
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function sendEmailVerificationNotification()
{
    $notifier = app(\App\Services\NotificationService::class);

    $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
    );

    $notifier->sendEmail(
        toEmail: $this->email,
        toName: $this->first_name,
        subject: 'Verify Your Email Address',
        htmlContent: "<p>Please click the link below to verify your email address.</p><p><a href=\"{$verificationUrl}\">Verify Email</a></p><p>If you did not create an account, no further action is required.</p>",
        customerId: $this->id,
    );
}

public function sendPasswordResetNotification($token)
{
    $notifier = app(\App\Services\NotificationService::class);

    $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $this->email], false));

    $notifier->sendEmail(
        toEmail: $this->email,
        toName: $this->first_name,
        subject: 'Reset Password Notification',
        htmlContent: "<p>You are receiving this email because we received a password reset request for your account.</p><p><a href=\"{$resetUrl}\">Reset Password</a></p><p>This password reset link will expire in 60 minutes.</p><p>If you did not request a password reset, no further action is required.</p>",
        customerId: $this->id,
    );
}
}