<?php
/**
 * Created by PhpStorm.
 * User: zero
 * Date: 6/9/17
 * Time: 12:30 PM
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="Email already taken")
 * @UniqueEntity(fields="username", message="Username already taken")
 */
class User implements UserInterface
{

    CONST USER_ROLE_ADMIN = "ROLE_ADMIN";
    CONST USER_ROLE_USER = "ROLE_USER";
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Password should by at least 6 chars long"
     * )
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * The below length depends on the "algorithm" you use for encoding
     * the password, but this works well with bcrypt.
     * @ORM\Column(type="string", length=64)
     */
    private $password;


    /**
     * @ORM\Column(type="integer")
     */
    private $activeUser;

    /**
     * @return mixed
     */
    public function getActiveUser()
    {
        return $this->activeUser;
    }

    /**
     * @param mixed $activeUser
     */
    public function setActiveUser($activeUser)
    {
        $this->activeUser = $activeUser;
    }


    // other properties and methods

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    // other methods, including security methods like getRoles()

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     * @ORM\Column(type="string")
     */

    private $role;

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }


    public function getRoles()
    {
        return [$this->getRole()];
    }


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AppRole", inversedBy="roleUser");
     */
    private $appRole;


    /**
     * @return mixed
     */
    public function getAppRole()
    {
        return $this->appRole;
    }

    /**
     * @param mixed $appRole
     */
    public function setAppRole($appRole)
    {
        $this->appRole = $appRole;
    }


    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return mixed
     */
    public function getCompanyBranch()
    {
        return $this->companyBranch;
    }

    /**
     * @param mixed $companyBranch
     */
    public function setCompanyBranch($companyBranch)
    {
        $this->companyBranch = $companyBranch;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Branch", inversedBy="branchUser")
     */
    private $companyBranch;


    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Company", mappedBy="CompanyManager")
     */
    private $ManagedCompany;

    /**
     * @return mixed
     */
    public function getManagedCompany()
    {
        return $this->ManagedCompany;
    }

    /**
     * @param mixed $ManagedCompany
     */
    public function setManagedCompany($ManagedCompany)
    {
        $this->ManagedCompany = $ManagedCompany;
    }

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\SubLicense", inversedBy="SubLicenseUser")
     */
    private $SubLicense;

    /**
     * @return SubLicense
     */
    public function getSubLicense()
    {
        return $this->SubLicense;
    }

    /**
     * @param mixed $SubLicense
     */
    public function setSubLicense($SubLicense)
    {
        $this->SubLicense = $SubLicense;
    }

}