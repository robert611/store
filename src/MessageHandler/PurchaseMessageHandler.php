<?php

namespace App\MessageHandler;

use App\Message\PurchaseMessage;
use App\Repository\PurchaseProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Notification\SoldProductNotification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

class PurchaseMessageHandler implements MessageHandlerInterface
{
    private $entityManager;
    private $commentRepository;
    private $mailer;
    private $bus;
    private $adminEmail;
    private $notifier;

    public function __construct(EntityManagerInterface $entityManager, PurchaseProductRepository $purchaseProductRepository, MessageBusInterface $bus, NotifierInterface $notifier, $adminEmail, MailerInterface $mailer)
    {
        $this->entityManager = $entityManager;
        $this->purchaseProductRepository = $purchaseProductRepository;
        $this->bus = $bus;
        $this->notifier = $notifier;
        $this->adminEmail = $adminEmail;
        $this->mailer = $mailer;
    }

    public function __invoke(PurchaseMessage $message)
    {
        $purchaseProduct = $this->purchaseProductRepository->find($message->getId());
        
        if (!$purchaseProduct) {
            return;
        }

        $this->mailer->send((new NotificationEmail())
            ->subject('New product sold')
            ->htmlTemplate('emails/sold_product_notification.html.twig')
            ->from($this->adminEmail)
            ->to($purchaseProduct->getPurchase()->getUser()->getEmail())
            ->context(['product' => $purchaseProduct->getProduct()])
        );
    }
}