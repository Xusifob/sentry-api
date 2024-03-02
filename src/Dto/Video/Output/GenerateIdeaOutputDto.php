<?php

namespace App\Dto\Video\Output;

use Symfony\Component\Serializer\Annotation\Groups;

class GenerateIdeaOutputDto
{
    #[Groups(['read'])]
    public array|null $ideas = [];
}
