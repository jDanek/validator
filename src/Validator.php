<?php

declare(strict_types=1);

namespace Danek\Validator;

use Danek\Validator\Value\Container;

class Validator
{
    /**
     * The default context (if no context is currently active).
     */
    const DEFAULT_CONTEXT = 'default';
    /** @var array<string, string> */
    public static $rulesMap = [
        'alpha_num' => Rule\AlphaNumRule::class,
        'alpha' => Rule\AlphaRule::class,
        'between' => Rule\BetweenRule::class,
        'boolean' => Rule\BooleanRule::class,
        'callback' => Rule\CallbackRule::class,
        'datetime' => Rule\DateTimeRule::class,
        'digits' => Rule\DigitsRule::class,
        'each' => Rule\EachRule::class,
        'email' => Rule\EmailRule::class,
        'equals' => Rule\EqualsRule::class,
        'greater_than' => Rule\GreaterThanRule::class,
        'hash' => Rule\HashRule::class,
        'in_array' => Rule\InArrayRule::class,
        'integer' => Rule\IntegerRule::class,
        'is_array' => Rule\IsArrayRule::class,
        'is_float' => Rule\IsFloatRule::class,
        'is_string' => Rule\IsStringRule::class,
        'json' => Rule\JsonRule::class,
        'length_between' => Rule\LengthBetweenRule::class,
        'length' => Rule\LengthRule::class,
        'less_than' => Rule\LessThanRule::class,
        'not_empty' => Rule\NotEmptyRule::class,
        'numeric' => Rule\NumericRule::class,
        'regex' => Rule\RegexRule::class,
        'required' => Rule\RequiredRule::class,
        'url' => Rule\UrlRule::class,
        'uuid' => Rule\UuidRule::class,
    ];
    /** @var array<string, array<Chain>} */
    protected $chains = [
        self::DEFAULT_CONTEXT => [],
    ];
    /** @var array<string, MessageStack> */
    protected $messageStacks = [];
    /** @var string */
    protected $currentContextName;
    /** @var RuleFactory */
    protected $ruleFactory;

    public function __construct()
    {
        $this->currentContextName = self::DEFAULT_CONTEXT;
        $this->messageStacks[$this->currentContextName] = new MessageStack();

        $this->ruleFactory = new RuleFactory(self::$rulesMap);
    }

    /**
     * Creates a new required Validation Chain for the key $key.
     */
    public function required(string $key, ?string $name = null, bool $allowEmpty = false): Chain
    {
        return $this->getChain($key, $name, true, $allowEmpty);
    }

    /**
     * Retrieves a Chain object, or builds one if it doesn't exist yet.
     */
    protected function getChain(string $key, ?string $name, bool $required, bool $allowEmpty): Chain
    {
        if (isset($this->chains[$this->currentContextName][$key])) {
            /** @var Chain $chain */
            $chain = $this->chains[$this->currentContextName][$key];
            $chain->required($required);
            $chain->allowEmpty($allowEmpty);

            return $chain;
        }
        return $this->chains[$this->currentContextName][$key] = $this->buildChain($key, $name, $required, $allowEmpty);
    }

    /**
     * Build a new Chain object and return it.
     */
    protected function buildChain(string $key, ?string $name, bool $required, bool $allowEmpty): Chain
    {
        return new Chain($this->ruleFactory, $key, $name, $required, $allowEmpty);
    }

    /**
     * Creates a new optional Validation Chain for the key $key.
     */
    public function optional(string $key, string $name = null, bool $allowEmpty = true): Chain
    {
        return $this->getChain($key, $name, false, $allowEmpty);
    }

    /**
     * Validates the values in the $values array and returns a ValidationResult.
     */
    public function validate(array $values, string $context = self::DEFAULT_CONTEXT): ValidationResult
    {
        $isValid = true;
        $output = new Container();
        $input = new Container($values);
        $stack = $this->getMergedMessageStack($context);

        foreach ($this->chains[$context] as $chain) {
            /** @var Chain $chain */
            $isValid = $chain->validate($stack, $input, $output) && $isValid;
        }

        $result = new ValidationResult(
            $isValid,
            $stack->getFailures(),
            $output->getArrayCopy()
        );

        $stack->reset();

        return $result;
    }

    /**
     * Returns the message stack. If the context isn't the default context, it will merge the message of the default
     * context first.
     */
    protected function getMergedMessageStack(string $context): MessageStack
    {
        $stack = $this->getMessageStack($context);

        if ($context !== self::DEFAULT_CONTEXT) {
            $stack->merge($this->getMessageStack(self::DEFAULT_CONTEXT));
        }

        return $stack;
    }

    /**
     * Returns a message stack for the context $context.
     */
    protected function getMessageStack(string $context): MessageStack
    {
        return $this->messageStacks[$context];
    }

    /**
     * Copy the rules and messages of the context $otherContext to the current context.
     */
    public function copyContext(string $otherContext, callable $callback = null): self
    {
        $this->copyChains($otherContext, $callback);
        if ($otherContext !== self::DEFAULT_CONTEXT) {
            $this->getMessageStack($this->currentContextName)->merge($this->getMessageStack($otherContext));
        }

        return $this;
    }

    /**
     * Copies the chains of the context $otherContext to the current context.
     */
    protected function copyChains(string $otherContext, ?callable $callback): void
    {
        if (isset($this->chains[$otherContext])) {
            $clonedChains = [];
            foreach ($this->chains[$otherContext] as $key => $chain) {
                $clonedChains[$key] = clone $chain;
            }

            $this->chains[$this->currentContextName] = $this->runChainCallback($clonedChains, $callback);
        }
    }

    /**
     * Executes the callback $callback and returns the resulting chains.
     *
     * @param array<Chain> $chains
     * @return array<Chain>
     */
    protected function runChainCallback(array $chains, ?callable $callback): array
    {
        if ($callback !== null) {
            $callback($chains);
        }

        return $chains;
    }

    /**
     * Create a new validation context with the callback $callback.
     */
    public function context(string $name, callable $callback): void
    {
        $this->addMessageStack($name);

        $this->currentContextName = $name;
        call_user_func($callback, $this);
        $this->currentContextName = self::DEFAULT_CONTEXT;
    }

    /**
     * Adds a message stack.
     */
    protected function addMessageStack(string $name): void
    {
        $messageStack = new MessageStack();

        $this->messageStacks[$name] = $messageStack;
    }

    /**
     * Output the structure of the Validator by calling the $output callable with a representation of Validators'
     * internal structure.
     *
     * @return mixed
     */
    public function output(callable $output, string $context = self::DEFAULT_CONTEXT)
    {
        $stack = $this->getMessageStack($context);

        $structure = new Output\Structure();
        if (array_key_exists($context, $this->chains)) {
            /* @var Chain $chain */
            foreach ($this->chains[$context] as $chain) {
                $chain->output($structure, $stack);
            }
        }

        return call_user_func($output, $structure);
    }

    /**
     * Overwrite the messages for specific keys.
     */
    public function overwriteMessages(array $messages): self
    {
        $this->getMessageStack($this->currentContextName)->overwriteMessages($messages);
        return $this;
    }

    /**
     * Overwrite the default messages with custom messages.
     */
    public function overwriteDefaultMessages(array $messages): self
    {
        $this->getMessageStack($this->currentContextName)->overwriteDefaultMessages($messages);
        return $this;
    }
}
