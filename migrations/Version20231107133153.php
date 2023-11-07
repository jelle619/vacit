<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231107133153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate ADD first_name VARCHAR(32) NOT NULL, ADD last_name VARCHAR(32) NOT NULL, ADD image LONGBLOB DEFAULT NULL, ADD email VARCHAR(64) NOT NULL, ADD password_hash VARCHAR(255) NOT NULL, ADD birth_date DATE NOT NULL, ADD phone_number VARCHAR(16) NOT NULL, ADD address VARCHAR(128) NOT NULL, ADD postal_code VARCHAR(8) NOT NULL, ADD city VARCHAR(64) NOT NULL, ADD cover_letter LONGTEXT DEFAULT NULL, ADD cv LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD name VARCHAR(32) NOT NULL, ADD image LONGBLOB DEFAULT NULL');
        $this->addSql('ALTER TABLE employer ADD company_id INT NOT NULL');
        $this->addSql('ALTER TABLE employer ADD CONSTRAINT FK_DE4CF066979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE INDEX IDX_DE4CF066979B1AD6 ON employer (company_id)');
        $this->addSql('ALTER TABLE submission ADD vacancy_id INT NOT NULL, ADD candidate_id INT NOT NULL, ADD date DATE NOT NULL, ADD invited TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF3433B78C4 FOREIGN KEY (vacancy_id) REFERENCES vacancy (id)');
        $this->addSql('ALTER TABLE submission ADD CONSTRAINT FK_DB055AF391BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('CREATE INDEX IDX_DB055AF3433B78C4 ON submission (vacancy_id)');
        $this->addSql('CREATE INDEX IDX_DB055AF391BD8781 ON submission (candidate_id)');
        $this->addSql('ALTER TABLE vacancy ADD employer_id INT NOT NULL, ADD name VARCHAR(64) NOT NULL, ADD image LONGBLOB DEFAULT NULL, ADD summary VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD date DATE NOT NULL, ADD level VARCHAR(16) NOT NULL, ADD location VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE vacancy ADD CONSTRAINT FK_A9346CBD41CD9E7A FOREIGN KEY (employer_id) REFERENCES employer (id)');
        $this->addSql('CREATE INDEX IDX_A9346CBD41CD9E7A ON vacancy (employer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate DROP first_name, DROP last_name, DROP image, DROP email, DROP password_hash, DROP birth_date, DROP phone_number, DROP address, DROP postal_code, DROP city, DROP cover_letter, DROP cv');
        $this->addSql('ALTER TABLE company DROP name, DROP image');
        $this->addSql('ALTER TABLE employer DROP FOREIGN KEY FK_DE4CF066979B1AD6');
        $this->addSql('DROP INDEX IDX_DE4CF066979B1AD6 ON employer');
        $this->addSql('ALTER TABLE employer DROP company_id');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF3433B78C4');
        $this->addSql('ALTER TABLE submission DROP FOREIGN KEY FK_DB055AF391BD8781');
        $this->addSql('DROP INDEX IDX_DB055AF3433B78C4 ON submission');
        $this->addSql('DROP INDEX IDX_DB055AF391BD8781 ON submission');
        $this->addSql('ALTER TABLE submission DROP vacancy_id, DROP candidate_id, DROP date, DROP invited');
        $this->addSql('ALTER TABLE vacancy DROP FOREIGN KEY FK_A9346CBD41CD9E7A');
        $this->addSql('DROP INDEX IDX_A9346CBD41CD9E7A ON vacancy');
        $this->addSql('ALTER TABLE vacancy DROP employer_id, DROP name, DROP image, DROP summary, DROP description, DROP date, DROP level, DROP location');
    }
}
