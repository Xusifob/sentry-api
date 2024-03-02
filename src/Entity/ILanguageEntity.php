<?php

namespace App\Entity;

interface ILanguageEntity
{
    public function getLanguage(): ?Language;

    public function setLanguage(?Language $language): self;
}
