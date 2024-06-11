<?php
namespace Magento\GiftCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Mail\Template\TransportBuilder;
use Psr\Log\LoggerInterface;

class SendEmail implements ObserverInterface
{
    protected $transportBuilder;
    protected $logger;

    public function __construct(
        TransportBuilder $transportBuilder,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $title = $observer->getEvent()->getTitle();
            $code = $observer->getEvent()->getCode();
            $balance = $observer->getEvent()->getBalance();
            $email = $observer->getEvent()->getEmail();

            $templateId = 'giftcard_email_template';
            $from = ['email' => "sender@example.com", 'name' => "Gift Card"];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => 1])
                ->setTemplateVars([
                    'title' => $title,
                    'code' => $code,
                    'balance' => $balance
                ])
                ->setFrom($from)
                ->addTo($email)
                ->getTransport();

            $transport->sendMessage();
            $this->logger->info('Email sent successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
