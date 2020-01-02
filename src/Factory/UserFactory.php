<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Model\Form\UserModel;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use UnexpectedValueException;

final class UserFactory
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function createFromUserModel(UserModel $userModel): User
    {
        $username = $userModel->getUsername();
        if (null === $username) {
            throw new UnexpectedValueException('Expected username to be set but null was found.');
        }

        $plainPassword = $userModel->getPassword();
        if (null === $plainPassword) {
            throw new UnexpectedValueException('Expected password to be set but null was found.');
        }

        $email = $userModel->getEmail();
        if (null === $email) {
            throw new UnexpectedValueException('Expected email to be set but null was found.');
        }

        $user = new User($username, $email);
        $password = $this->encoder->encodePassword($user, $plainPassword);
        $user->setPassword($password);

        return $user;
    }
}
