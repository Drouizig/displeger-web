<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200407122010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $stmt = $this->connection->prepare("select id, dialect_code from verblocalization where dialect_code != '';");
        $res = $stmt->execute();
        foreach($stmt->fetchAll() as $tuple) {
            $dialect = serialize([$tuple['dialect_code']]);
            $stmt = $this->connection->prepare("UPDATE verblocalization set dialect_code = '".$dialect."' WHERE id = ".$tuple['id'].";");
            $stmt->execute();
        };
        $this->addSql('ALTER TABLE verblocalization ALTER dialect_code TYPE TEXT');
        $this->addSql('ALTER TABLE verblocalization ALTER dialect_code DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN verblocalization.dialect_code IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE VerbLocalization ALTER dialect_code TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE VerbLocalization ALTER dialect_code DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN VerbLocalization.dialect_code IS NULL');
    }
}
