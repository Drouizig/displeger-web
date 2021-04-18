<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210418150251 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE description_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE description_translation (id INT NOT NULL, verb_id INT DEFAULT NULL, verb_localization_id INT DEFAULT NULL, language_code VARCHAR(255) DEFAULT NULL, content VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_58B55B9EC1D03483 ON description_translation (verb_id)');
        $this->addSql('CREATE INDEX IDX_58B55B9E811F889A ON description_translation (verb_localization_id)');
        $this->addSql('ALTER TABLE description_translation ADD CONSTRAINT FK_58B55B9EC1D03483 FOREIGN KEY (verb_id) REFERENCES Verb (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE description_translation ADD CONSTRAINT FK_58B55B9E811F889A FOREIGN KEY (verb_localization_id) REFERENCES VerbLocalization (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE description_translation_id_seq CASCADE');
        $this->addSql('DROP TABLE description_translation');
    }
}
