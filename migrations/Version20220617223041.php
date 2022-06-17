<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220617223041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "report_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "report" (id INT NOT NULL, ticket_id INT DEFAULT NULL, comment_id INT DEFAULT NULL, author_id INT NOT NULL, description TEXT NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, resolved_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C42F7784700047D2 ON "report" (ticket_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784F8697D13 ON "report" (comment_id)');
        $this->addSql('CREATE INDEX IDX_C42F7784F675F31B ON "report" (author_id)');
        $this->addSql('COMMENT ON COLUMN "report".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "report".resolved_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "report" ADD CONSTRAINT FK_C42F7784700047D2 FOREIGN KEY (ticket_id) REFERENCES "ticket" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "report" ADD CONSTRAINT FK_C42F7784F8697D13 FOREIGN KEY (comment_id) REFERENCES "comment" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "report" ADD CONSTRAINT FK_C42F7784F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "report_id_seq" CASCADE');
        $this->addSql('DROP TABLE "report"');
    }
}
