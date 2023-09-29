<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230715151525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE app_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_user (id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('DROP TABLE "User"');
        $this->addSql('INSERT INTO app_user(id, username, roles, password) VALUES (0, \'admin\', \'["ROLE_ADMIN"]\', \'$2y$13$W6Q.FFoPZmYZjDC/IBA8qebUVoM0Famj49jBuV6quRRNuLPs/v/4O\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE app_user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) NOT NULL, executed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(version))');
        $this->addSql('COMMENT ON COLUMN migration_versions.executed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "User" (id INT NOT NULL, username VARCHAR(255) DEFAULT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE app_user');
    }
}
