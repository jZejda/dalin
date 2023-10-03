<?php

declare(strict_types=1);

namespace App\Http\Components\Iofv3\Entities;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class ClassStart
{
    #[SerializedName('Class')]
    private ?SportClass $Class;
    private Course $Course;
    /** @var PersonStart[] $PersonStart */
    private array $PersonStart;

    /**
     * @param SportClass|null $Class
     * @param Course $Course
     * @param array $PersonStart
     */
    public function __construct(?SportClass $Class, Course $Course, array $PersonStart)
    {
        $this->Class = $Class;
        $this->Course = $Course;
        $this->PersonStart = $PersonStart;
    }

    public function getClass(): ?SportClass
    {
        return $this->Class;
    }

    public function getCourse(): Course
    {
        return $this->Course;
    }

    public function getPersonStart(): array
    {
        return $this->PersonStart;
    }




}
