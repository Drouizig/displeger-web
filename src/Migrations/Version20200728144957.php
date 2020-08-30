<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200728144957 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE verb_tag (verb_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(verb_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_1C2DDDBEC1D03483 ON verb_tag (verb_id)');
        $this->addSql('CREATE INDEX IDX_1C2DDDBEBAD26311 ON verb_tag (tag_id)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_translation (id INT NOT NULL, tag_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, language_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A8A03F8FBAD26311 ON tag_translation (tag_id)');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT FK_1C2DDDBEC1D03483 FOREIGN KEY (verb_id) REFERENCES Verb (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT FK_1C2DDDBEBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_translation ADD CONSTRAINT FK_A8A03F8FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT FK_1C2DDDBEBAD26311');
        $this->addSql('ALTER TABLE tag_translation DROP CONSTRAINT FK_A8A03F8FBAD26311');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_translation_id_seq CASCADE');
        $this->addSql('DROP TABLE verb_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_translation');
    }
}
