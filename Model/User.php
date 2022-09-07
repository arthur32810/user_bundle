<?php

namespace ArtDevelopp\UserBundle\Model;

use DateTime;
use DateTimeInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank()]
    private ?string $username = null;

    #[Assert\Length(max: 4096)]
    private ?string $plainPassword = null;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     */
    #[ORM\Column()]
    private ?string $password = null;

    #[ORM\Column()]
    private ?array $roles = null;

    #[ORM\Column()]
    private ?bool $user_activated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activation_token = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reset_token = null;

    #[ORM\Column()]
    private ?DateTimeInterface $registrationDate = null;


    // other properties and methods

    public function __construct()
    {

        $this->roles = ['ROLE_USER'];
        $this->registrationDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): self
    {
        $this->plainPassword = $password;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    /**
     * Get the value of activation_token
     */
    public function getActivationToken(): ?string
    {
        return $this->activation_token;
    }

    /**
     * Set the value of activation_token
     */
    public function setActivationToken(?string $activation_token): self
    {
        $this->activation_token = $activation_token;

        return $this;
    }

    /**
     * Get the value of user_activated
     */
    public function getUserActivated(): ?bool
    {
        return $this->user_activated;
    }

    /**
     * Set the value of user_activated
     */
    public function setUserActivated(bool $user_activated): self
    {
        $this->user_activated = $user_activated;

        return $this;
    }

    /**
     * Get the value of reset_token
     */
    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    /**
     * Set the value of reset_token
     */
    public function setResetToken($reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }

    /**
     * Get the value of registrationDate
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set the value of registrationDate
     */
    public function setRegistrationDate($registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }
}