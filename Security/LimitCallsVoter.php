<?php
/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Security;

use Avtonom\LimitNumberCallsBundle\Manager\LimitCallsManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LimitCallsVoter extends Voter
{
    /**
     * @var LimitCallsManagerInterface
     */
    protected $limitCallsManager;

    /**
     * @param LimitCallsManagerInterface $limitCallsManager
     * @param TokenStorageInterface $tokenStorage
     * @param string $secret
     */
    public function __construct(LimitCallsManagerInterface $limitCallsManager, TokenStorageInterface $tokenStorage, $secret)
    {
        $this->limitCallsManager = $limitCallsManager;
        if(!$tokenStorage->getToken()){
            $tokenStorage->setToken(new AnonymousToken($secret, 'anon.', array()));
        }
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        return $this->limitCallsManager->supports($attribute, $subject);
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        return $this->limitCallsManager->voteOnAttribute($attribute, $subject);
    }
}
