<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230728091754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE verb_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE verb_tag ADD id INT');
        $this->addSql('UPDATE verb_tag SET id = nextval(\'verb_tag_id_seq\')');
        $this->addSql('ALTER TABLE verb_tag ALTER id SET NOT NULL');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT uq_id UNIQUE(id)');

        $this->addSql('CREATE TABLE verb_tag_sources_new (verb_tag_id INT NOT NULL, source_id INT NOT NULL, PRIMARY KEY(verb_tag_id, source_id))');
        $this->addSql('CREATE INDEX IDX_D4CDBE04D8C8F111 ON verb_tag_sources_new (verb_tag_id)');
        $this->addSql('CREATE INDEX IDX_D4CDBE04953C1C61 ON verb_tag_sources_new (source_id)');
        $this->addSql('ALTER TABLE verb_tag_sources_new ADD CONSTRAINT FK_D4CDBE04D8C8F111 FOREIGN KEY (verb_tag_id) REFERENCES verb_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag_sources_new ADD CONSTRAINT FK_D4CDBE04953C1C61 FOREIGN KEY (source_id) REFERENCES Source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('INSERT INTO verb_tag_sources_new (verb_tag_id, source_id)
                            SELECT vt.id, vts.source_id
                            FROM verb_tag AS vt
                            JOIN verb_tag_sources AS vts ON vt.verb_id = vts.verb_tag_verb_id AND vt.tag_id = vts.verb_tag_tag_id;
                            ');
        $this->addSql('ALTER TABLE verb_tag_sources DROP CONSTRAINT fk_b15f4ef4953c1c61');
        $this->addSql('ALTER TABLE verb_tag_sources DROP CONSTRAINT fk_b15f4ef4a2d21f85be95bc0c');
        $this->addSql('DROP TABLE verb_tag_sources');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT FK_1C2DDDBEBAD26311');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT FK_1C2DDDBEC1D03483');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT verb_tag_pkey');
        $this->addSql('ALTER TABLE verb_tag ALTER verb_id DROP NOT NULL');
        $this->addSql('ALTER TABLE verb_tag ALTER tag_id DROP NOT NULL');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT FK_1C2DDDBEBAD26311 FOREIGN KEY (tag_id) REFERENCES Tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT FK_1C2DDDBEC1D03483 FOREIGN KEY (verb_id) REFERENCES Verb (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE verb_tag_id_seq CASCADE');
        $this->addSql('CREATE TABLE verb_tag_sources (verb_tag_verb_id INT NOT NULL, verb_tag_tag_id INT NOT NULL, source_id INT NOT NULL, PRIMARY KEY(verb_tag_verb_id, verb_tag_tag_id, source_id))');
        $this->addSql('CREATE INDEX idx_b15f4ef4a2d21f85be95bc0c ON verb_tag_sources (verb_tag_verb_id, verb_tag_tag_id)');
        $this->addSql('CREATE INDEX idx_b15f4ef4953c1c61 ON verb_tag_sources (source_id)');
        $this->addSql('ALTER TABLE verb_tag_sources ADD CONSTRAINT fk_b15f4ef4953c1c61 FOREIGN KEY (source_id) REFERENCES source (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag_sources ADD CONSTRAINT fk_b15f4ef4a2d21f85be95bc0c FOREIGN KEY (verb_tag_verb_id, verb_tag_tag_id) REFERENCES verb_tag (verb_id, tag_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag_sources_new DROP CONSTRAINT FK_D4CDBE04D8C8F111');
        $this->addSql('ALTER TABLE verb_tag_sources_new DROP CONSTRAINT FK_D4CDBE04953C1C61');
        $this->addSql('DROP TABLE verb_tag_sources_new');
        $this->addSql('ALTER TABLE configuration_translation ADD thanks TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE Verb ALTER enabled SET DEFAULT true');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT fk_1c2dddbebad26311');
        $this->addSql('ALTER TABLE verb_tag DROP CONSTRAINT fk_1c2dddbec1d03483');
        $this->addSql('DROP INDEX verb_tag_pkey');
        $this->addSql('ALTER TABLE verb_tag DROP id');
        $this->addSql('ALTER TABLE verb_tag ALTER tag_id SET NOT NULL');
        $this->addSql('ALTER TABLE verb_tag ALTER verb_id SET NOT NULL');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT fk_1c2dddbebad26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD CONSTRAINT fk_1c2dddbec1d03483 FOREIGN KEY (verb_id) REFERENCES verb (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE verb_tag ADD PRIMARY KEY (verb_id, tag_id)');
    }
}
