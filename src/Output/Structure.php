<?php

declare(strict_types=1);

namespace Danek\Validator\Output;

class Structure
{
    /** @var array<Subject> */
    protected $subjects;

    /**
     * Add a subject (representation of Chain) to the structure.
     */
    public function addSubject(Subject $subject): void
    {
        $this->subjects[] = $subject;
    }

    /**
     * @return array<Subject>
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }
}
