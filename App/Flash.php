<?php


namespace App;


class Flash
{
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    public static function addMessage(string $message, string $type): void
    {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }

        $_SESSION['flash_messages'][] = [
            'body' => $message,
            'type' => $type
        ];
    }

    public static function getMessages(): ?array
    {
        if (isset($_SESSION['flash_messages'])) {
            $messages = $_SESSION['flash_messages'];
            unset($_SESSION['flash_messages']);

            return $messages;
        }

        return null;
    }
}