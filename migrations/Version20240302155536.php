<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302155536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sentry_exception (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', owner_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, event_id VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, release_name VARCHAR(255) DEFAULT NULL, platform VARCHAR(255) NOT NULL, exception JSON NOT NULL, tags JSON NOT NULL, level LONGTEXT NOT NULL, location VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, request JSON NOT NULL, archived TINYINT(1) NOT NULL, created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_date DATETIME DEFAULT NULL, INDEX IDX_CA664F767E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', password VARCHAR(255) NOT NULL, given_name VARCHAR(255) DEFAULT NULL, family_name VARCHAR(255) DEFAULT NULL, created_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sentry_exception ADD CONSTRAINT FK_CA664F767E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sentry_exception DROP FOREIGN KEY FK_CA664F767E3C61F9');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE sentry_exception');
        $this->addSql('DROP TABLE `user`');
    }
}
