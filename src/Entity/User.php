<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Doctrine\EntityListener\UserListener;
use App\Dto\User\Input\SignupInputDto;
use App\Dto\User\Input\UpdatePasswordDto;
use App\Dto\User\Output\SignupOutputDto;
use App\Entity\Enum\UserRole;
use App\Entity\Trait\EntityTrait;
use App\Repository\UserRepository;
use App\State\User\MeProvider;
use App\State\User\SignupProcessor;
use App\State\User\UpdatePasswordProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    uriTemplate: '/users/me',
    operations: [new Get()],
    security: 'is_granted("ROLE_USER")',
    provider: MeProvider::class
)]
#[ApiResource(
    operations: [new Patch()],
    security: 'is_granted("UPDATE", object)',
)]
#[ApiResource(
    uriTemplate: '/register',
    operations: [new Post()],
    input: SignupInputDto::class,
    output: SignupOutputDto::class,
    security: 'is_granted("PUBLIC_ACCESS")',
    processor: SignupProcessor::class,
)]
#[ApiResource(
    uriTemplate: '/update_password',
    operations: [new Post()],
    input: UpdatePasswordDto::class,
    security: 'is_granted("PUBLIC_ACCESS")',
    processor: UpdatePasswordProcessor::class,
)]
#[UniqueEntity('email', message: 'user.email.unique')]
#[ORM\EntityListeners([UserListener::class])]
class User extends Entity implements IEntity, UserInterface, PasswordAuthenticatedUserInterface
{
    use EntityTrait;

    #[Groups(['user:item'])]
    #[ApiProperty(schema: [
        'type' => 'string',
        'maxLength' => 180,
        'example' => 'john@doe.fr',
        'required' => true,
    ], iris: ['https://schema.org/email'])]
    #[Email(message: 'user.email.invalid')]
    #[ORM\Column(length: 180, unique: true)]
    public ?string $email = null;

    /**
     * @var string[]
     */
    #[ORM\Column(name: 'roles', type: 'simple_array', options: ['jsonb' => true])]
    #[ApiProperty(readable: false, writable: false)]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column(name: 'password', nullable: false)]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $password = null;

    #[Groups('user:post')]
    #[SerializedName('password')]
    #[ApiProperty(schema: [
        'type' => 'string',
        'maxLength' => 30,
        'example' => 'My@WesomeP@$$w0rd',
        'required' => true,
    ], iris: ['https://schema.org/accessCode'])]
    #[NotBlank(message: 'user.password.not_blank', groups: ['user:post'])]
    #[Length(
        min: 8,
        max: 30,
        minMessage: 'user.password.min_length',
        maxMessage: 'user.password.max_length'
    )]
    public ?string $plainPassword = null;

    #[Groups(['user:item'])]
    #[ApiProperty(schema: [
        'type' => 'string',
        'maxLength' => 255,
        'example' => 'John',
        'required' => false,
    ], iris: ['https://schema.org/givenName'])]
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $givenName = null;

    #[Groups(['user:item'])]
    #[ApiProperty(schema: [
        'type' => 'string',
        'maxLength' => 255,
        'example' => 'DOE',
        'required' => false,
    ], iris: ['https://schema.org/familyName'])]
    #[ORM\Column(length: 255, nullable: true)]
    public ?string $familyName = null;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    #[Groups(['user:item'])]
    #[ApiProperty(schema: [
        'type' => 'string',
        'example' => 'John DOE',
        'writable' => false,
    ], iris: ['https://schema.org/name'])]
    public function getFullName(): string
    {
        return "$this->givenName $this->familyName";
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[ApiProperty(readable: false, writable: false)]
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = UserRole::ROLE_USER->value;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function setRole(string|UserRole $role): self
    {
        $roles = $this->roles;

        $roles[] = $role instanceof UserRole ? $role->value : $role;

        return $this->setRoles($roles);
    }

    #[Groups(['user:item'])]
    #[ApiProperty(schema: [
        'type' => 'string',
        'enum' => UserRole::class,
        'example' => UserRole::ROLE_USER,
        'required' => false,
    ], iris: ['https://schema.org/roleName'])]
    public function getRole(): UserRole
    {
        $role = $this->getRoles()[0];

        return UserRole::from($role);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
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
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    private function isRole(UserRole $role): bool
    {
        return in_array($role->value, $this->roles);
    }

    #[ApiProperty(readable: false, writable: false)]
    public function isAdmin(): bool
    {
        return $this->isRole(UserRole::ROLE_ADMIN);
    }

    #[ApiProperty(readable: false, writable: false)]
    public function getUsername(): string
    {
        return $this->email;
    }
}
