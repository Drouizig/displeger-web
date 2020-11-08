<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201108171624 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE TagCategory_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE TagCategory (id INT NOT NULL, code VARCHAR(255) NOT NULL, name TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE tag ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_3BC4F16312469DE2 FOREIGN KEY (category_id) REFERENCES TagCategory (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3BC4F16312469DE2 ON tag (category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE Tag DROP CONSTRAINT FK_3BC4F16312469DE2');
        $this->addSql('DROP SEQUENCE TagCategory_id_seq CASCADE');
        $this->addSql('DROP TABLE TagCategory');
        $this->addSql('DROP INDEX IDX_3BC4F16312469DE2');
        $this->addSql('ALTER TABLE Tag DROP category_id');
    }
}
