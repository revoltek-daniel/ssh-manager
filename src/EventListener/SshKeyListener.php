<?php

namespace App\EventListener;

use App\Entity\SshKey;
use App\Entity\User;
use App\Service\SshAuthService;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class SshKeyListener
{
    /**
     * @var SshAuthService
     */
    private SshAuthService $sshAuthService;
    /**
     * @var User|null
     */
    private User $user;

    /**
     * SshKeyListener constructor.
     * @param SshAuthService $sshAuthService
     */
    public function __construct(SshAuthService $sshAuthService)
    {
        $this->sshAuthService = $sshAuthService;
    }

    /**
     * @param SshKey $sshKey
     * @param LifecycleEventArgs $event
     */
    public function preUpdate(SshKey $sshKey, LifecycleEventArgs $event): void
    {
        $change = $event->getEntityChangeSet();

        if (isset($change['user'])) {
            $this->user = $change['user'][0];
        }
    }

    /**
     * @param SshKey $sshKey
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(SshKey $sshKey, LifecycleEventArgs $event): void
    {
        $user = $this->user ?? $sshKey->getUser();

        if ($user instanceof User) {
            $this->sshAuthService->update($user);
        }
    }

    /**
     * @param SshKey $sshKey
     * @param LifecycleEventArgs $event
     */
    public function postRemove(SshKey $sshKey, LifecycleEventArgs $event): void
    {
        $user = $sshKey->getUser();

        if ($user instanceof User) {
            $this->sshAuthService->remove($user);
        }
    }

    /**
     * @param SshKey $sshKey
     * @param LifecycleEventArgs $event
     */
    public function postPersist(SshKey $sshKey, LifecycleEventArgs $event): void
    {
        $user = $sshKey->getUser();

        $this->sshAuthService->create($user);
    }
}