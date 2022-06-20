<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220617213623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE ticket_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ticket (id INT NOT NULL, category_id INT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, enabled BOOLEAN NOT NULL, closed BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, slug VARCHAR(255) NOT NULL, uuid UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97A0ADA312469DE2 ON ticket (category_id)');
        $this->addSql('CREATE INDEX IDX_97A0ADA3F675F31B ON ticket (author_id)');
        $this->addSql('COMMENT ON COLUMN ticket.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN ticket.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA312469DE2 FOREIGN KEY (category_id) REFERENCES "category" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3F675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97A0ADA3989D9B62 ON ticket (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_97A0ADA3D17F50A6 ON ticket (uuid)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA312469DE2');
        $this->addSql('DROP INDEX UNIQ_97A0ADA3989D9B62');
        $this->addSql('DROP INDEX UNIQ_97A0ADA3D17F50A6');
        $this->addSql('DROP SEQUENCE ticket_id_seq CASCADE');
        $this->addSql('DROP TABLE ticket');
    }
}
