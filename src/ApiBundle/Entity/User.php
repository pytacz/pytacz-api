<?php

namespace ApiBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=20, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=64)
     */
    private $password;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="register_date", type="datetime")
     */
    private $registerDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_date", type="datetime", nullable=true)
     */
    private $lastDate;

    /**
     * @var string
     *
     * @ORM\Column(name="last_ip", type="string", length=15, nullable=true)
     */
    private $lastIp;

    /**
     * @var string
     *
     * @ORM\Column(name="register_ip", type="string", length=15)
     */
    private $registerIp;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="register_hash", type="string", length=30, nullable=true, unique=true)
     */
    private $registerHash;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=20, nullable=true, unique=true)
     */
    private $apiToken;

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set registerDate
     *
     * @param DateTime $registerDate
     *
     * @return User
     */
    public function setRegisterDate(DateTime $registerDate): User
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return DateTime
     */
    public function getRegisterDate(): DateTime
    {
        return $this->registerDate;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return User
     */
    public function setIsActive(bool $isActive): User
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Set lastDate
     *
     * @param DateTime $lastDate
     *
     * @return User
     */
    public function setLastDate(DateTime $lastDate): User
    {
        $this->lastDate = $lastDate;

        return $this;
    }

    /**
     * Get lastDate
     *
     * @return DateTime
     */
    public function getLastDate(): DateTime
    {
        return $this->lastDate;
    }

    /**
     * Set lastIp
     *
     * @param string $lastIp
     *
     * @return User
     */
    public function setLastIp(string $lastIp): User
    {
        $this->lastIp = $lastIp;

        return $this;
    }

    /**
     * Get lastIp
     *
     * @return string
     */
    public function getLastIp(): string
    {
        return $this->lastIp;
    }

    /**
     * Set registerIp
     *
     * @param string $registerIp
     *
     * @return User
     */
    public function setRegisterIp(string $registerIp): User
    {
        $this->registerIp = $registerIp;

        return $this;
    }

    /**
     * Get registerIp
     *
     * @return string
     */
    public function getRegisterIp(): string
    {
        return $this->registerIp;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName(string $name): User
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set registerHash
     *
     * @param string $registerHash
     *
     * @return User
     */
    public function setRegisterHash(?string $registerHash): User
    {
        $this->registerHash = $registerHash;

        return $this;
    }

    /**
     * Get registerHash
     *
     * @return string
     */
    public function getRegisterHash(): ?string
    {
        return $this->registerHash;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return User
     */
    public function setApiToken(string $apiToken): User
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    public function isAccountNonExpired(): bool
    {
        return true;
    }

    public function isAccountNonLocked(): bool
    {
        return true;
    }

    public function isCredentialsNonExpired(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return $this->isActive;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        return null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize()
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
}
