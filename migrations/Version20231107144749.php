<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231107144749 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE password_hash password VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE employer ADD first_name VARCHAR(32) NOT NULL, ADD last_name VARCHAR(32) NOT NULL, ADD image LONGBLOB DEFAULT NULL, ADD email VARCHAR(180) NOT NULL, ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', ADD password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DE4CF066E7927C74 ON employer (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate DROP roles, CHANGE password password_hash VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_DE4CF066E7927C74 ON employer');
        $this->addSql('ALTER TABLE employer DROP first_name, DROP last_name, DROP image, DROP email, DROP roles, DROP password');
    }
}
