<?php

namespace Danek\Validator;

/**
 * Mapped Validator supporting associative array rules and Laravel-style string rules.
 */
class MappedValidator extends Validator
{
    /**
     * Sets validation rules using either an associative array or Laravel-style string notation.
     *
     * Two supported formats:
     *
     * 1. Associative array of rules:
     * <code>
     * [
     *   'name' => [
     *     'required' => true,
     *     'string' => true,
     *     'length_between' => [2, 255]
     *   ]
     * ]
     * </code>
     *
     * 2. Laravel-style string notation:
     * <code>
     * [
     *   'name' => 'required|string|length_between:2,255'
     * ]
     * </code>
     *
     * - The array key corresponds to the field name to validate.
     * - The value can be either an associative array of rules or a string with rules separated by `|`.
     * - Rule parameters can be provided after a colon, multiple parameters separated by commas.
     *
     * @param array<string, array<string, mixed>|string> $rules Array of rules, where each key is a field name and the value is the rule definition.
     */
    public function setRules(array $rules): self
    {
        foreach ($rules as $field => $fieldRules) {
            $this->parseRulesForField($field, $fieldRules);
        }
        return $this;
    }

    private function parseRulesForField(string $field, $fieldRules): void
    {
        if (is_array($fieldRules)) {
            $this->applyArrayRulesToField($field, $fieldRules);
        } elseif (is_string($fieldRules)) {
            $this->applyStringRulesToField($field, $fieldRules);
        }
    }

    private function applyArrayRulesToField(string $field, array $fieldRules): void
    {
        // normalize rule names to lowercase for case-insensitive matching
        $fieldRules = array_change_key_case($fieldRules, CASE_LOWER);

        $isRequired = $fieldRules['required'] ?? false;
        $allowEmpty = !$isRequired;

        $chain = $this->getChain($field, null, $isRequired, $allowEmpty);

        foreach ($fieldRules as $ruleName => $ruleValue) {
            if ($ruleName === 'required' || $ruleName === 'optional') {
                continue;
            }
            $this->applyRuleToChain($chain, $ruleName, $ruleValue);
        }
    }

    private function applyStringRulesToField(string $field, string $fieldRules): void
    {
        // normalize rules to lowercase for case-insensitive matching
        $rulesArray = array_map('strtolower', explode('|', $fieldRules));

        $isRequired = in_array('required', $rulesArray, true);
        $isOptional = in_array('optional', $rulesArray, true);

        // if neither 'required' nor 'optional' is specified, default to optional
        if (!$isRequired && !$isOptional) {
            $isRequired = false;
        }

        $allowEmpty = !$isRequired;
        $chain = $this->getChain($field, null, $isRequired, $allowEmpty);

        foreach ($rulesArray as $singleRule) {
            $this->parseAndApplyStringRule($chain, $singleRule);
        }
    }

    private function parseAndApplyStringRule(Chain $chain, string $singleRule): void
    {
        if (strpos($singleRule, ':') !== false) {
            [$ruleName, $ruleValue] = explode(':', $singleRule, 2);

            if (strpos($ruleValue, ',') !== false) {
                $values = array_map('trim', explode(',', $ruleValue));
                $this->applyRuleToChain($chain, $ruleName, $values);
            } else {
                $this->applyRuleToChain($chain, $ruleName, $ruleValue);
            }
        } else {
            if ($singleRule !== 'required' && $singleRule !== 'optional') {
                $this->applyRuleToChain($chain, $singleRule, true);
            }
        }
    }

    private function applyRuleToChain(Chain $chain, string $ruleName, $ruleValue): void
    {
        $methodName = $this->convertToMethodName($ruleName);
        if (method_exists($chain, $methodName)) {
            if ($ruleValue === true || $ruleValue === null) {
                $chain->$methodName();
            } elseif (is_array($ruleValue)) {
                $chain->$methodName(...$ruleValue);
            } else {
                $chain->$methodName($ruleValue);
            }
        }
    }

    private function convertToMethodName(string $ruleKey): string
    {
        // Convert snake_case to camelCase
        return lcfirst(str_replace('_', '', ucwords($ruleKey, '_')));
    }
}