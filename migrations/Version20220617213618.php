<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220617213618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE "category_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "category" (id INT NOT NULL, created_by_id INT NOT NULL, updated_by_id INT NOT NULL, title VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, slug VARCHAR(255) NOT NULL, uuid UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON "category" (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1D17F50A6 ON "category" (uuid)');
        $this->addSql('CREATE INDEX IDX_64C19C1B03A8386 ON "category" (created_by_id)');
        $this->addSql('CREATE INDEX IDX_64C19C1896DBBDE ON "category" (updated_by_id)');
        $this->addSql('COMMENT ON COLUMN "category".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "category".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "category" ADD CONSTRAINT FK_64C19C1B03A8386 FOREIGN KEY (created_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "category" ADD CONSTRAINT FK_64C19C1896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "category_id_seq" CASCADE');
        $this->addSql('DROP TABLE "category"');
        $this->addSql('DROP INDEX UNIQ_64C19C1989D9B62');
        $this->addSql('DROP INDEX UNIQ_64C19C1D17F50A6');
    }
}
