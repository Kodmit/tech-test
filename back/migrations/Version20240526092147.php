<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240526092147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'init';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE trace_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE contact (id UUID NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN contact.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tag_contact (tag_id INT NOT NULL, contact_id UUID NOT NULL, PRIMARY KEY(tag_id, contact_id))');
        $this->addSql('CREATE INDEX IDX_7E53CB92BAD26311 ON tag_contact (tag_id)');
        $this->addSql('CREATE INDEX IDX_7E53CB92E7A1254A ON tag_contact (contact_id)');
        $this->addSql('COMMENT ON COLUMN tag_contact.contact_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE trace (id INT NOT NULL, author_id UUID NOT NULL, action VARCHAR(200) NOT NULL, class_name VARCHAR(200) NOT NULL, property VARCHAR(200) NOT NULL, old_value VARCHAR(255) DEFAULT NULL, new_value VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_315BD5A1F675F31B ON trace (author_id)');
        $this->addSql('COMMENT ON COLUMN trace.author_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN trace.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tag_contact ADD CONSTRAINT FK_7E53CB92BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tag_contact ADD CONSTRAINT FK_7E53CB92E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trace ADD CONSTRAINT FK_315BD5A1F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE trace_id_seq CASCADE');
        $this->addSql('ALTER TABLE tag_contact DROP CONSTRAINT FK_7E53CB92BAD26311');
        $this->addSql('ALTER TABLE tag_contact DROP CONSTRAINT FK_7E53CB92E7A1254A');
        $this->addSql('ALTER TABLE trace DROP CONSTRAINT FK_315BD5A1F675F31B');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_contact');
        $this->addSql('DROP TABLE trace');
        $this->addSql('DROP TABLE "user"');
    }
}
