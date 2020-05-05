<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200422144510 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE configuration_translation ADD text TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE configuration_translation DROP intro');
        $this->addSql('ALTER TABLE configuration_translation DROP thanks');
        $this->addSql('ALTER TABLE configuration ADD code VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A5E2A5D777153098 ON configuration (code)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_A5E2A5D777153098');
        $this->addSql('ALTER TABLE configuration DROP code');
        $this->addSql('ALTER TABLE configuration_translation ADD thanks TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE configuration_translation RENAME COLUMN text TO intro');
    }
}
