<?php declare(strict_types = 1);

namespace HnutiBrontosaurus\Theme\UI\EventDetail\DTO;

use HnutiBrontosaurus\Theme\UI\EventDetail\EmailSettings;
use Nette\Mail\Mailer;
use Nette\Mail\Message;


final class NotificationSender
{

	private Mailer $mailer;
	private Message $message;


	private function __construct(Mailer $mailer, EmailSettings $emailSettings, string $subject, string $body, ?string $replyTo)
	{
		$this->mailer = $mailer;

		$this->message = new Message();

		$this->message->setFrom($emailSettings->getFromAddress(), $emailSettings->getFromName());
		$this->message->setSubject($subject);
		$this->message->setBody($body);

		if ($replyTo !== null) {
			$this->message->addReplyTo($replyTo);
		}
	}


	public static function prepareMessage(Mailer $mailer, EmailSettings $emailSettings, string $subject, string $body, ?string $replyTo): self
	{
		return new self($mailer, $emailSettings, $subject, $body, $replyTo);
	}


	public function send(string $recipient): void
	{
		$message = clone $this->message; // we need to clone it, otherwise there would be more recipients
		$message->addTo($recipient);

		$this->mailer->send($message);
	}

}
