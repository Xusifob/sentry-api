<?php

namespace App\Dto\Video\Input;

use Symfony\Component\Serializer\Annotation\Groups;

class GenerateScriptDto
{
    #[Groups(['write'])]
    public ?string $subject = null;

    #[Groups(['write'])]
    public ?string $language = null;
}
