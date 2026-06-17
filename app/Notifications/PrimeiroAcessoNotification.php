<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrimeiroAcessoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private string $senhaTemporaria,
        private string $urlSistema,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Seu acesso ao ImobGestor')
            ->greeting("Olá, {$notifiable->name}.")
            ->line('Seu acesso ao ImobGestor foi criado.')
            ->line('Para entrar no sistema, utilize os dados abaixo:')
            ->line("**Email:** {$notifiable->email}")
            ->line("**Senha temporária:** {$this->senhaTemporaria}")
            ->line('No primeiro acesso, será obrigatório cadastrar uma nova senha.')
            ->action('Acessar o sistema', $this->urlSistema)
            ->line('Caso você não reconheça este convite, ignore esta mensagem.')
            ->salutation('Atenciosamente, equipe ImobGestor');
    }
}
