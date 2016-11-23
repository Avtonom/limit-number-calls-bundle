Defender for Symfony 2, of the large number of requests
===================================

Defender for Symfony 2, of the large number of requests. It protects against multiple processing one value. Guard, aims to protect against brute force and dictionary attacks.

Page bundle: https://github.com/Avtonom/limit-number-calls-bundle

## Features

* Multiple rules for blocking
* Combine rules into groups
* Has a Symfony Security Voter
* Ready set console commands (CLI) to view, add, and delete statistics and locks
* Easy to expand
* Uses a fast pRedis
* Supports up to a microsecond

Maybe in the future:

* Do the work with the console commands more convenient
* Expose the core of the application in a simple version which does not depend on the Symfony
* minor edits. I will be grateful for the help

#### List console commands (CLI)
* avtonom:limit-calls:add - add the execution of the request in the statistics (does not establish a lock. But check for blocking)
* avtonom:limit-calls:block - add value to the list of locks on value
* avtonom:limit-calls:clear - remove statistics for the values for
* avtonom:limit-calls:rules - Open the list current words for checking locks
* avtonom:limit-calls:status - View a list of blocked values and statistics list

**for details, add "-h" after the command name**

#### Parameters of the rule settings
* enabled: true - [OPTIONAL] rule off
* maximum_number: 1 - maximum number of requests that value
* time_period: 60000000 # microsecond ( 1s = 1000 000 microsecond ) - for a period to allow to carry out a specified number of actions
* blocking_duration: 600 # second ( 1m = 60s ) - [OPTIONAL] blocking duration
* group: sms_group or [sms_group, other_group] - [OPTIONAL] association in a list or group with several groups
* subject_class: Avtonom\*****\ObjectInterface - Retreiving class or interface to run Symfony Security Voter
* subject_method: [getParameter, phone] or getParameter - [OPTIONAL] The method or the method for obtaining attribute values of object

#### To Install

Run the following in your project root, assuming you have composer set up for your project

```sh

composer.phar require avtonom/limit-number-calls-bundle ~1.1

```

Switching `~1.1` for the most recent tag.

Add the bundle to app/AppKernel.php

```php

$bundles(
    ...
        new Snc\RedisBundle\SncRedisBundle(),
        new Avtonom\LimitNumberCallsBundle\AvtonomLimitNumberCallsBundle(),
    ...
);

```

Configuration options (config.yaml):

``` yaml

snc_redis:
    clients:
        default:
            type: predis
            logging: true # OPTIONAL
            alias: snc_redis_lnc
            dsn: redis://localhost
            options: # OPTIONAL
                throw_errors: true # OPTIONAL

avtonom_limit_number_calls:
    voter_default: false # OPTIONAL. default true - include %avtonom_limit_number_calls.voter.class%
    rules: "%avtonom_limit_number_calls.rules%" # REQUIRED
```

Configuration options (parameters.yaml):

``` yaml

parameters:
    avtonom_limit_number_calls.rules:
        sms_1m_rule:
            time_period: 60000000 # microsecond ( 1m = 60s * 1000 000 microsecond )
            maximum_number: 1
            blocking_duration: 600 # second ( 1m = 60s )
            group: sms_group
            subject_class: *****\ObjectInterface
            subject_method: [getParameter, phone]
        sms_30m_rule:
            time_period: 1800000000 # microsecond ( 30m = 1m * 30 = 30 * 60s * 1000 000 microsecond )
            maximum_number: 3
            blocking_duration: 86400 # second ( 1d = 86400 second = 25h * 60m * 60s )
            group: [sms_group, other]
            subject_class: *****\ObjectInterface
            subject_method: [getParameter, phone]

        test_minimum:
            time_period: 1800
            maximum_number: 3
            subject_class: *****\Object
        test_minimum_disabled:
            enabled: true
            time_period: 1800
            maximum_number: 3
            subject_class: *****\Object
    
```

#### Use
Use the name of the rule or group of rules for checking the limit is exceeded:

``` php
# for Symfony > 3.0
if (!$this->get('security.authorization_checker')->isGranted('sms_group', $Object)) {
    throw new \Exception('Too Many Requests', 429);
}
# for Symfony < 3.0
if (!$this->get('security.context')->isGranted('sms_1m_rule', $Object)) {
    throw new \Exception('Too Many Requests', 429);
}
```

### Need Help?

1. Create an issue if you've found a bug,