<?php

declare(strict_types=1);

namespace App\Listener;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordSubscriber implements EventSubscriberInterface
{

    private $passwordEncoder;
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['prePersist'],
            BeforeEntityUpdatedEvent::class => ['preUpdate']
        ];
    }
    public function prePersist(BeforeEntityPersistedEvent $event): void
    {
        $this->encodePassword($event->getEntityInstance());
    }

    public function preUpdate(BeforeEntityUpdatedEvent $event): void
    {
        $this->encodePassword($event->getEntityInstance());
    }

    private function encodePassword($entity)
    {
        if (!$entity instanceof User) {
            return;
        }
        if (null !== $entity->getPlainPassword() && '' != $entity->getPlainPassword()) {
            $hashedPassword = $this->passwordEncoder->encodePassword(
                $entity,
                $entity->getPlainPassword()
            );
            $entity->setPassword($hashedPassword);
        }
    }
}
