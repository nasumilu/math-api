<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220410093345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_user (id BIGSERIAL NOT NULL, profile BIGINT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON app_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E98157AA0F ON app_user (profile)');
        $this->addSql('CREATE TABLE client (id VARCHAR(128) NOT NULL, name VARCHAR(64) NOT NULL, secret VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE profile (id BIGSERIAL NOT NULL, name VARCHAR(128) NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE token (id VARCHAR(128) NOT NULL, app_user BIGINT DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F37A13B88BDF3E9 ON token (app_user)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E98157AA0F FOREIGN KEY (profile) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13B88BDF3E9 FOREIGN KEY (app_user) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE token DROP CONSTRAINT FK_5F37A13B88BDF3E9');
        $this->addSql('ALTER TABLE app_user DROP CONSTRAINT FK_88BDF3E98157AA0F');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE token');
    }
}
