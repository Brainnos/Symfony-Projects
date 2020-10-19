<?php 

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EmailService {

	private $mailer;

	public function __construct(
		MailerInterface $mailer
	) {
		$this->mailer = $mailer;
		$this->data = array(
			'from' => false,
			'to' => false,
			'replyTo' => false,
			'subject' => false,
			'template' => false,
			'context' => false,
		);
	}

	public function send($data)
	{
		$data = array_merge($this->data, $data);
		if(!$data['from']) { $data['from'] = $_ENV['MY_EMAIL']; }
		if(!$data['to']) { $data['to'] = $_ENV['MY_EMAIL']; }
		if(!$data['replyTo']) { $data['replyTo'] = $data['from']; }

		$email = (new TemplatedEmail())
            ->from($data['from'])
            ->to($data['to'])
            ->replyTo($data['replyTo'])
            ->subject($data['subject'])
            ->htmlTemplate($data['template'])
            ->context($data['context'])
        ;

        // dump($email); die();

        $this->mailer->send($email);
	}

	public function register($user) {
		$data = array(
			'to' => $user->getEmail(),
			'subject' => 'Confirmez votre inscription',
			'template' => 'emails/security/register.email.twig',
			'context' =>['user' => $user]
		);

		$this->send($data);
	}

	public function password_forgotten($user, $link) {
		$data = array(
			'to' => $user->getEmail(),
			'subject' => 'Modifier votre mot de passe',
			'template' => 'emails/security/password_forgotten.email.twig',
			'context' =>['user' => $user, 'link' => $link]
		);

		$this->send($data);
	}


}











