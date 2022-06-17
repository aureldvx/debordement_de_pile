<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220617221850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE "vote_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "vote" (id INT NOT NULL, ticket_id INT DEFAULT NULL, comment_id INT DEFAULT NULL, author_id INT NOT NULL, type INT NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5A108564700047D2 ON "vote" (ticket_id)');
        $this->addSql('CREATE INDEX IDX_5A108564F8697D13 ON "vote" (comment_id)');
        $this->addSql('CREATE INDEX IDX_5A108564F675F31B ON "vote" (author_id)');
        $this->addSql('COMMENT ON COLUMN "vote".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "vote" ADD CONSTRAINT FK_5A108564700047D2 FOREIGN KEY (ticket_id) REFERENCES "ticket" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "vote" ADD CONSTRAINT FK_5A108564F8697D13 FOREIGN KEY (comment_id) REFERENCES "comment" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "vote" ADD CONSTRAINT FK_5A108564F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "vote_id_seq" CASCADE');
        $this->addSql('DROP TABLE "vote"');
    }
}
