<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302172641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add one signal device';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE one_signal_device (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', owner_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', token VARCHAR(255) NOT NULL, created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_C04535825F37A13B (token), INDEX IDX_C04535827E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE one_signal_device ADD CONSTRAINT FK_C04535827E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE one_signal_device DROP FOREIGN KEY FK_C04535827E3C61F9');
        $this->addSql('DROP TABLE one_signal_device');
    }
}
