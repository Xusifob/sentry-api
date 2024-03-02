<?php

namespace App\Dto\Video\Input;

use Symfony\Component\Serializer\Annotation\Groups;

class GenerateIdeaDto
{
    #[Groups(['write'])]
    public ?string $subject = null;

    #[Groups(['write'])]
    public ?string $language = null;
}
