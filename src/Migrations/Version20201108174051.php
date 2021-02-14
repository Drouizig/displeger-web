<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201108174051 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tag_category_translation_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tag_category_translation (id INT NOT NULL, tag_category_id INT DEFAULT NULL, label VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, language_code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_29C5130FE8FE702 ON tag_category_translation (tag_category_id)');
        $this->addSql('ALTER TABLE tag_category_translation ADD CONSTRAINT FK_29C5130FE8FE702 FOREIGN KEY (tag_category_id) REFERENCES TagCategory (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tag_category_translation_id_seq CASCADE');
        $this->addSql('DROP TABLE tag_category_translation');
    }
}
