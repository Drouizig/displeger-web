<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201122221554 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE verb_tag_sources (verb_tag_verb_id INT NOT NULL, verb_tag_tag_id INT NOT NULL, source_id INT NOT NULL, PRIMARY KEY(verb_tag_verb_id, verb_tag_tag_id, source_id))');
        $this->addSql('CREATE INDEX IDX_B15F4EF4A2D21F85BE95BC0C ON verb_tag_sources (verb_tag_verb_id, verb_tag_tag_id)');
        $this->addSql('CREATE INDEX IDX_B15F4EF4953C1C61 ON verb_tag_sources (source_id)');
        $this->addSql('ALTER TABLE verb_tag_sources ADD CONSTRAINT FK_B15F4EF4A2D21F85BE95BC0C FOREIGN KEY (verb_tag_verb_id, verb_tag_tag_id) REFERENCES verb_tag (verb_id, tag_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag_sources ADD CONSTRAINT FK_B15F4EF4953C1C61 FOREIGN KEY (source_id) REFERENCES Source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT fk_1c2dddbe953c1c61');
        $this->addSql('ALTER TABLE verb_tag DROP source_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE verb_tag_sources');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT fk_1c2dddbebad26311');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT fk_1c2dddbec1d03483');
        $this->addSql('DROP INDEX verb_tag_pkey');
        $this->addSql('ALTER TABLE verb_tag ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT fk_1c2dddbe953c1c61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT fk_1c2dddbebad26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT fk_1c2dddbec1d03483 FOREIGN KEY (verb_id) REFERENCES verb (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1C2DDDBE953C1C61 ON verb_tag (source_id)');
        $this->addSql('ALTER TABLE verb_tag ADD PRIMARY KEY (verb_id, tag_id)');
    }
}
