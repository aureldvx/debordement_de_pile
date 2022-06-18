<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220617225428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE login_activity_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE login_activity (id INT NOT NULL, related_user_id INT NOT NULL, connected_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ip_address BIGINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_82D029C598771930 ON login_activity (related_user_id)');
        $this->addSql('COMMENT ON COLUMN login_activity.connected_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE login_activity ADD CONSTRAINT FK_82D029C598771930 FOREIGN KEY (related_user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE login_activity_id_seq CASCADE');
        $this->addSql('DROP TABLE login_activity');
    }
}
