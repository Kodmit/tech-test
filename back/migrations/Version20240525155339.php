<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525155339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add traceable';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE trace_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE trace (id INT NOT NULL, author_id UUID NOT NULL, class_name VARCHAR(200) NOT NULL, property VARCHAR(200) NOT NULL, old_value VARCHAR(255) NOT NULL, new_value VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_315BD5A1F675F31B ON trace (author_id)');
        $this->addSql('COMMENT ON COLUMN trace.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN trace.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE trace ADD CONSTRAINT FK_315BD5A1F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE trace_id_seq CASCADE');
        $this->addSql('ALTER TABLE trace DROP CONSTRAINT FK_315BD5A1F675F31B');
        $this->addSql('DROP TABLE trace');
    }
}
