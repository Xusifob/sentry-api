<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240315195533 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add support for slack webhook url';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD slack_webhook_url VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `user` DROP slack_webhook_url');
    }
}
