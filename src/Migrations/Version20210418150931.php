<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210418150931 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE description_translation_source (description_translation_id INT NOT NULL, source_id INT NOT NULL, PRIMARY KEY(description_translation_id, source_id))');
        $this->addSql('CREATE INDEX IDX_A999CD8C7388930E ON description_translation_source (description_translation_id)');
        $this->addSql('CREATE INDEX IDX_A999CD8C953C1C61 ON description_translation_source (source_id)');
        $this->addSql('ALTER TABLE description_translation_source ADD CONSTRAINT FK_A999CD8C7388930E FOREIGN KEY (description_translation_id) REFERENCES description_translation (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE description_translation_source ADD CONSTRAINT FK_A999CD8C953C1C61 FOREIGN KEY (source_id) REFERENCES Source (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE description_translation_source');
    }
}
