<?php

declare(strict_types=1);

namespace Danek\Validator;

use Danek\Validator\Output\Structure;
use Danek\Validator\Output\Subject;
use Danek\Validator\Rule\NotEmptyRule;
use Danek\Validator\Rule\RequiredRule;
use Danek\Validator\Value\Container;

class Chain
{
    /** @var RuleFactory */
    protected $ruleFactory;

    /** @var string */
    protected $key;

    /** @var string */
    protected $name;

    /** @var array<Rule> */
    protected $rules = [];

    /** @var MessageStack */
    protected $messageStack;

    public function __construct(RuleFactory $ruleFactory, string $key, ?string $name, bool $required, bool $allowEmpty)
    {
        $this->key = $key;
        $this->name = $name;
        $this->ruleFactory = $ruleFactory;

        $this->addRule($this->ruleFactory->createByName('required', [$required]));
        $this->addRule($this->ruleFactory->createByName('not_empty', [$allowEmpty]));
    }

    /**
     * Shortcut method for storing a rule on this chain, and returning the chain.
     */
    protected function addRule(Rule $rule): self
    {
        $this->rules[] = $rule;

        return $this;
    }

    /**
     * Overwrite the default __clone behaviour to make sure the rules are cloned too.
     */
    public function __clone()
    {
        $rules = [];
        foreach ($this->rules as $rule) {
            $rules[] = clone $rule;
        }
        $this->rules = $rules;
    }

    /**
     * Set a callable or boolean value which may be used to alter the allow empty requirement on validation time.
     *
     * This may be incredibly helpful when doing conditional validation.
     *
     * @param callable|bool $allowEmpty
     */
    public function allowEmpty($allowEmpty): self
    {
        $this->getNotEmptyRule()->setAllowEmpty($allowEmpty);
        return $this;
    }

    /**
     * Returns the second rule, which is always the allow empty rule.
     */
    protected function getNotEmptyRule(): NotEmptyRule
    {
        /** @var NotEmptyRule */
        return $this->rules[1]; // set in __construct
    }

    /**
     * Validate the value to consist only out of alphanumeric characters.
     */
    public function alphaNum(bool $allowWhitespace = false): self
    {
        return $this->addRule($this->ruleFactory->createByName('alpha_num', [$allowWhitespace]));
    }

    /**
     * Validate that the value only consists our of alphabetic characters.
     */
    public function alpha(bool $allowWhitespace = false): self
    {
        return $this->addRule($this->ruleFactory->createByName('alpha', [$allowWhitespace]));
    }

    /**
     * Validate that the value is between $min and $max (inclusive).
     *
     * @param int|float $min
     * @param int|float $max
     */
    public function between($min, $max): self
    {
        return $this->addRule($this->ruleFactory->createByName('between', [$min, $max]));
    }

    /**
     * Validate that the value is a boolean.
     */
    public function bool(): self
    {
        return $this->addRule($this->ruleFactory->createByName('boolean'));
    }

    /**
     * Validate by executing a callback function, and returning its result.
     */
    public function callback(callable $callable): self
    {
        return $this->addRule($this->ruleFactory->createByName('callback', [$callable]));
    }

    /**
     * Validates that the value is a date. If format is passed, it *must* be in that format.
     */
    public function datetime(?string $format = null): self
    {
        return $this->addRule($this->ruleFactory->createByName('datetime', [$format]));
    }

    /**
     * Validates that all characters of the value are decimal digits.
     */
    public function digits(): self
    {
        return $this->addRule($this->ruleFactory->createByName('digits'));
    }

    /**
     * Validates a value to be a nested array, which can then be validated using a new Validator instance.
     */
    public function each(callable $callback): self
    {
        return $this->addRule($this->ruleFactory->createByName('each', [$callback]));
    }

    /**
     * Validates that the value is a valid email address (format only).
     */
    public function email(): self
    {
        return $this->addRule($this->ruleFactory->createByName('email'));
    }

    /**
     * Validates that the value is equal to $value.
     *
     * @param mixed $value
     */
    public function equals($value): self
    {
        return $this->addRule($this->ruleFactory->createByName('equals', [$value]));
    }

    /**
     * Validates that the value represents a float.
     */
    public function float(): self
    {
        return $this->addRule($this->ruleFactory->createByName('is_float'));
    }

    /**
     * Validates that the value is greater than $value.
     *
     * @param int|float $value
     */
    public function greaterThan($value): self
    {
        return $this->addRule($this->ruleFactory->createByName('greater_than', [$value]));
    }

    /**
     * Validates that the value is in the array with optional "loose" checking.
     *
     * @see \Danek\Validator\Rule\HashRule
     */
    public function hash(string $hashAlgorithm, bool $allowUppercase = false): self
    {
        return $this->addRule($this->ruleFactory->createByName('hash', [$hashAlgorithm, $allowUppercase]));
    }

    /**
     * Validates that the value is in the array with optional "loose" checking.
     */
    public function inArray(array $array, bool $strict = true): self
    {
        return $this->addRule($this->ruleFactory->createByName('in_array', [$array, $strict]));
    }

    /**
     * Validates the value represents a valid integer
     */
    public function integer(bool $strict = false): self
    {
        return $this->addRule($this->ruleFactory->createByName('integer', [$strict]));
    }

    /**
     * Validates the value is an array
     */
    public function isArray(): self
    {
        return $this->addRule($this->ruleFactory->createByName('is_array'));
    }

    /**
     * Validates that the value represents a valid JSON string
     */
    public function json(): self
    {
        return $this->addRule($this->ruleFactory->createByName('json'));
    }

    /**
     * Validate the value to be of precisely length $length.
     *
     * @param int|float $length
     */
    public function length($length): self
    {
        return $this->addRule($this->ruleFactory->createByName('length', [$length]));
    }

    /**
     * Validates that the length of the value is between $min and $max.
     *
     * If $max is null, it has no upper limit. The default is inclusive.
     *
     * @param int|float $min
     * @param int|float|null $max
     */
    public function lengthBetween($min, $max = null): self
    {
        return $this->addRule($this->ruleFactory->createByName('length_between', [$min, $max]));
    }

    /**
     * Validates that the value is less than $value.
     *
     * @param int|float $value
     */
    public function lessThan($value): self
    {
        return $this->addRule($this->ruleFactory->createByName('less_than', [$value]));
    }

    /**
     * Mount a rule object onto this chain.
     */
    public function mount(Rule $rule): self
    {
        return $this->addRule($rule);
    }

    /**
     * Validates that the value is either a integer or a float.
     */
    public function numeric(): self
    {
        return $this->addRule($this->ruleFactory->createByName('numeric'));
    }

    /**
     * Validates that the value matches the regular expression $regex.
     */
    public function regex(string $regex): self
    {
        return $this->addRule($this->ruleFactory->createByName('regex', [$regex]));
    }

    /**
     * Set a callable or boolean value which may be used to alter the required requirement on validation time.
     *
     * This may be incredibly helpful when doing conditional validation.
     *
     * @param callable|bool $required
     */
    public function required($required): self
    {
        $this->getRequiredRule()->setRequired($required);
        return $this;
    }

    /**
     * Returns the first rule, which is always the required rule.
     */
    protected function getRequiredRule(): RequiredRule
    {
        /** @var RequiredRule */
        return $this->rules[0]; // set in __construct
    }

    /**
     * Validates that the value represents a string.
     */
    public function string(): self
    {
        return $this->addRule($this->ruleFactory->createByName('is_string'));
    }

    /**
     * Validates that the value is a valid URL. The schemes array is to selectively whitelist URL schemes.
     */
    public function url(array $schemes = []): self
    {
        return $this->addRule($this->ruleFactory->createByName('url', [$schemes]));
    }

    /**
     * Validates that the value is a valid UUID
     */
    public function uuid(int $version = Rule\UuidRule::UUID_VALID): self
    {
        return $this->addRule($this->ruleFactory->createByName('uuid', [$version]));
    }

    /**
     * Attach a representation of this Chain to the Output\Structure $structure.
     *
     * @internal
     */
    public function output(Structure $structure, MessageStack $messageStack): Structure
    {
        $subject = new Subject($this->key, $this->name);

        foreach ($this->rules as $rule) {
            $rule->output($subject, $messageStack);
        }

        $structure->addSubject($subject);

        return $structure;
    }

    /**
     * Validates the values in the $values array and appends messages to $messageStack. Returns the result as a bool.
     */
    public function validate(MessageStack $messageStack, Container $input, Container $output): bool
    {
        $valid = true;
        foreach ($this->rules as $rule) {
            $rule->setMessageStack($messageStack);
            $rule->setParameters($this->key, $this->name);

            $valid = $rule->isValid($this->key, $input) && $valid;

            if (!$valid && $rule->shouldBreakChainOnError() || $rule->shouldBreakChain()) {
                break;
            }
        }

        if ($valid && $input->has($this->key)) {
            $output->set($this->key, $input->get($this->key));
        }
        return $valid;
    }
}
