<?php

/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Manager;

interface LimitCallsManagerInterface
{
    /**
     * @return array
     */
    public function getRules();

    /**
     * @param array $rules
     */
    public function setRules($rules);

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    public function supports($attribute, $subject);

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $ruleName
     * @param mixed          $subject
     *
     * @return bool
     */
    public function voteOnAttribute($ruleName, $subject);
}