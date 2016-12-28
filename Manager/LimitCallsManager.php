<?php
/**
 * @author Anton U <avtonomspb@gmail.com>
 */
namespace Avtonom\LimitNumberCallsBundle\Manager;

class LimitCallsManager implements LimitCallsManagerInterface
{
    /**
     * @var LimitCallsRepositoryInterface
     */
    protected $limitCallsRepository;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var array
     */
    protected $names;

    /**
     * @param LimitCallsRepositoryInterface $limitCallsRepository
     */
    public function __construct(LimitCallsRepositoryInterface $limitCallsRepository)
    {
        $this->limitCallsRepository = $limitCallsRepository;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param array $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
        $this->prepareRules();
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    public function supports($attribute, $subject)
    {
        if (!array_key_exists($attribute, $this->names)) {
            return false;
        }

        $instance = $this->names[$attribute];
        if($instance && !class_exists($instance)){
            throw new \LogicException('class '.$instance.' is not exists');
        }
        if ($instance && !$subject instanceof $instance) {
            return false;
        }
        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string         $ruleName
     * @param mixed          $subject
     *
     * @return bool
     */
    public function voteOnAttribute($ruleName, $subject)
    {
        $rules = $this->rules[$ruleName];
        if(is_array(current($rules))){
            foreach ($rules as $ruleNameFromGroup => $rule) {
                if(!$result = $this->check($subject, $ruleNameFromGroup, $rule)){
                    return false;
                }
            }
        } else {
            return $this->check($subject, $ruleName, $rules);
        }
        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param mixed          $subject
     * @param string         $ruleName
     * @param array          $rule
     *
     * @return bool
     */
    protected function check($subject, $ruleName, $rule)
    {
        $value = '';
        if(array_key_exists('subject_method', $rule)){
            $arguments = [];
            if(is_array($rule['subject_method'])){
                if(!is_string($rule['subject_method'][0]) && is_array($rule['subject_method'][0])){
                    $subjectMethod = [];
                    foreach ($rule['subject_method'] as $method) {
                        $subjectMethod[] = $method[0];
                        $arguments[] = [$method[1]];
                    }
                } else {
                    $subjectMethod = $rule['subject_method'][0];
                    $arguments[] = $rule['subject_method'][1];
                }
            } else {
                $subjectMethod = $rule['subject_method'];
            }
            if(!is_string($subjectMethod) && is_array($subjectMethod)){
                foreach ($subjectMethod as $key => $subjectMethodOne) {
                    $value .= $this->getSubjectValue($subject, $subjectMethodOne, $arguments[$key]);
                }
            } else {
                $value = $this->getSubjectValue($subject, $subjectMethod, $arguments);
            }
        } else {
            $value = $subject;
        }
        $count = $this->limitCallsRepository->add($ruleName, $value, $rule['time_period']);
        if($count === false){
            return false;
        }
        if($rule['maximum_number'] < $count){
            $blockingDuration = array_key_exists('blocking_duration', $rule) ? $rule['blocking_duration'] : null;
            $this->limitCallsRepository->block($ruleName, $value, $blockingDuration);
            return false;
        }
        return true;
    }

    /**
     * @param Object $subject
     * @param string $subjectMethod
     * @param array $arguments
     *
     * @return mixed
     */
    protected function getSubjectValue($subject, $subjectMethod, $arguments)
    {
        if(!is_callable([$subject, $subjectMethod])){
            throw new \LogicException(vsprintf('Method %s for class %s not callable', array($subjectMethod, get_class($subject))));
        }
        return call_user_func_array(array($subject, $subjectMethod), $arguments);
    }

    protected function prepareRules()
    {
        foreach($this->rules as $name => $rule){
            if(array_key_exists('enabled', $rule) && !$rule['enabled']){
                continue;
            }
            $this->names[$name] = (array_key_exists('class', $rule) ? $rule['class'] : null);
            if(array_key_exists('group', $rule)){
                $groups = is_array($rule['group']) ? $rule['group'] : [$rule['group']];
                foreach($groups as $group) {
                    $this->names[$group] = $this->names[$name];
                    $this->rules[$group][$name] = $rule;
                }
            }
        }
    }
}
