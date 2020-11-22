<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201122212743 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT FK_1C2DDDBEBAD26311');
        // $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT FK_1C2DDDBEC1D03483');
        $this->addSql('ALTER TABLE verb_tag ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT FK_1C2DDDBE953C1C61 FOREIGN KEY (source_id) REFERENCES Source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT FK_1C2DDDBEBAD26311 FOREIGN KEY (tag_id) REFERENCES Tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT FK_1C2DDDBEC1D03483 FOREIGN KEY (verb_id) REFERENCES Verb (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('CREATE INDEX IDX_1C2DDDBE953C1C61 ON verb_tag (source_id)');
        // $this->addSql('ALTER TABLE verb_tag ADD PRIMARY KEY (tag_id, verb_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT FK_1C2DDDBE953C1C61');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT fk_1c2dddbebad26311');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT fk_1c2dddbec1d03483');
        $this->addSql('DROP INDEX IDX_1C2DDDBE953C1C61');
        $this->addSql('DROP INDEX verb_tag_pkey');
        $this->addSql('ALTER TABLE verb_tag DROP source_id');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT fk_1c2dddbebad26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT fk_1c2dddbec1d03483 FOREIGN KEY (verb_id) REFERENCES verb (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD PRIMARY KEY (verb_id, tag_id)');
    }
}
