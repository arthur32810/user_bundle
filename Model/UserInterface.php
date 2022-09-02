<?php

namespace ArtDevelopp\UserBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface as UserUserInterface;

interface UserInterface extends UserUserInterface
{
    public function getUsername(): ?string;
    public function setUsername(string $username): self;

    public function getEmail(): ?string;
    public function setEmail(string $email): self;

    public function getPlainPassword(): ?string;
    public function setPlainPassword(string $plainPassword): self;

    public function getPassword(): ?string;
    public function setPassword(string $password): self;

    public function getRoles(): array;
    public function setRoles(array $roles): self;

    public function getUserActivated(): ?bool;
    public function setUserActivated(bool $user_activated): self;

    public function getActivationToken(): ?string;
    public function setActivationToken(?string $activation_token): self;

    public function getResetToken(): ?string;
    public function setResetToken(string $reset_token): self;

    public function getRegistrationDate();
    public function setRegistrationDate($registrationDate): self;
}