<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class ClassResult
{
    private Course $Course;
    /** @var PersonResult[] $PersonResult */
    private array $PersonResult;
    #[SerializedName('Class')]
    private ?SportClass $Class;

    /**
     * @param Course $Course
     * @param PersonResult[] $PersonResult
     * @param SportClass|null $Class
     */
    public function __construct(Course $Course, array $PersonResult, ?SportClass $Class)
    {
        $this->Course = $Course;
        $this->PersonResult = $PersonResult;
        $this->Class = $Class;
    }

    public function getCourse(): Course
    {
        return $this->Course;
    }

    public function getPersonResult(): array
    {
        return $this->PersonResult;
    }

    public function getClass(): ?SportClass
    {
        return $this->Class;
    }
}
