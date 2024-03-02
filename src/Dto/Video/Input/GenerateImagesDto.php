<?php

namespace App\Dto\Video\Input;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class GenerateClipDto
{
    #[Assert\All(
        constraints: [
            new Assert\NotBlank(),
            new Assert\Length(min: 1, max: 4096),
        ]
    )]
    #[Groups(['write'])]
    public array $texts = [];
}
