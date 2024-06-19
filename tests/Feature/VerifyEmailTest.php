<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

// php artisan test --filter=VerifyEmailTest
class VerifyEmailTest extends TestCase
{
    protected $user;
    protected $verificationUrl;

    /**
     * Устанавливает начальные условия перед запуском каждого теста.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Создаёт нового пользователя с неподтверждённым email.
        $this->user = User::factory()->create(['email_verified_at' => null]);

        // Генерирует временную подписанную ссылку для верификации email.
        $this->verificationUrl = URL::temporarySignedRoute(
            'verification.verify', // Имя маршрута.
            now()->addMinutes(60), // Время действия ссылки.
            ['id' => $this->user->id, 'hash' => sha1($this->user->email)] // Параметры маршрута.
        );
    }

    /** @test */
    public function invalid_signature()
    {
        // Создаёт URL с недействительной подписью.
        $invalidUrl = str_replace('signature=', 'signature=invalid', $this->verificationUrl);

        // Выполняет GET-запрос по недействительному URL.
        $response = $this->get($invalidUrl);

        // Проверяет, что происходит перенаправление на страницу логина.
        $response->assertRedirect('/login');

        // Проверяет, что сессия содержит ошибку "Invalid or expired verification link".
        $response->assertSessionHasErrors('message', 'Invalid or expired verification link');
    }

    /** @test */
    public function user_not_found()
    {
        // Создаёт временную подписанную ссылку с несуществующим ID пользователя.
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => 999, 'hash' => sha1($this->user->email)]
        );

        // Выполняет GET-запрос по URL.
        $response = $this->get($url);

        // Проверяет, что происходит перенаправление на страницу логина.
        $response->assertRedirect('/login');

        // Проверяет, что сессия содержит ошибку "User not found".
        $response->assertSessionHasErrors('message', 'User not found');
    }

    /** @test */
    public function invalid_hash()
    {
        // Создаёт временную подписанную ссылку с неправильным хешем email.
        $invalidUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->user->id, 'hash' => sha1('invalid@example.com')]
        );

        // Выполняет GET-запрос по URL.
        $response = $this->get($invalidUrl);

        // Проверяет, что происходит перенаправление на страницу логина.
        $response->assertRedirect('/login');

        // Проверяет, что сессия содержит ошибку "Invalid hash".
        $response->assertSessionHasErrors('message', 'Invalid hash');
    }

    /** @test */
    public function email_already_verified()
    {
        // Помечает email пользователя как подтверждённый.
        $this->user->markEmailAsVerified();

        // Выполняет GET-запрос по действительному URL.
        $response = $this->get($this->verificationUrl);

        // Проверяет, что происходит перенаправление на страницу логина.
        $response->assertRedirect('/login');

        // Проверяет, что сессия содержит сообщение "Email already verified".
        $response->assertSessionHas('message', 'Email already verified');
    }

    /** @test */
    public function email_successfully_verified()
    {
        // Выполняет GET-запрос по действительному URL.
        $response = $this->get($this->verificationUrl);

        // Проверяет, что происходит перенаправление на страницу логина.
        $response->assertRedirect('/login');

        // Проверяет, что сессия содержит сообщение "Email successfully verified".
        $response->assertSessionHas('message', 'Email successfully verified');

        // Проверяет, что email пользователя действительно помечен как подтверждённый в базе данных.
        $this->assertNotNull($this->user->fresh()->email_verified_at);
    }
}
