<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\VerifyEmailQueued;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class SendVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Формируем URL для верификации email
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify', // имя маршрута
            now()->addMinutes(60), // время действия ссылки
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)] // параметры маршрута
        );

        // Заменить "localhost" на ваш реальный домен
        $verificationUrl = str_replace('http://localhost', config('app.url'), $verificationUrl);

        // Отправка уведомления
        $this->user->notify(new VerifyEmailQueued($verificationUrl));
    }
}
