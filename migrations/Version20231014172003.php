<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231014172003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE barber_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE appointment (id UUID NOT NULL, barber_id UUID NOT NULL, provider_id UUID NOT NULL, service_id UUID NOT NULL, user_id_id UUID NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE38F844BFF2FEF2 ON appointment (barber_id)');
        $this->addSql('CREATE INDEX IDX_FE38F844A53A8AA ON appointment (provider_id)');
        $this->addSql('CREATE INDEX IDX_FE38F844ED5CA9E6 ON appointment (service_id)');
        $this->addSql('CREATE INDEX IDX_FE38F8449D86650F ON appointment (user_id_id)');
        $this->addSql('COMMENT ON COLUMN appointment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appointment.barber_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appointment.provider_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appointment.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN appointment.user_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE barber (id UUID NOT NULL, provider_id UUID NOT NULL, user_id_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7C48A9A4A53A8AA ON barber (provider_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7C48A9A49D86650F ON barber (user_id_id)');
        $this->addSql('COMMENT ON COLUMN barber.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN barber.provider_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN barber.user_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE feedback (id UUID NOT NULL, barber_id UUID NOT NULL, provider_id UUID NOT NULL, user_id_id UUID NOT NULL, service_id UUID NOT NULL, barber_note INT NOT NULL, provider_note INT NOT NULL, comment VARCHAR(255) NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D2294458BFF2FEF2 ON feedback (barber_id)');
        $this->addSql('CREATE INDEX IDX_D2294458A53A8AA ON feedback (provider_id)');
        $this->addSql('CREATE INDEX IDX_D22944589D86650F ON feedback (user_id_id)');
        $this->addSql('CREATE INDEX IDX_D2294458ED5CA9E6 ON feedback (service_id)');
        $this->addSql('COMMENT ON COLUMN feedback.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN feedback.barber_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN feedback.provider_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN feedback.user_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN feedback.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE provider (id UUID NOT NULL, manager_id UUID NOT NULL, kbis VARCHAR(255) NOT NULL, localisation VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92C4739C783E3463 ON provider (manager_id)');
        $this->addSql('COMMENT ON COLUMN provider.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN provider.manager_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE provider_request (id UUID NOT NULL, localisation VARCHAR(255) NOT NULL, kbis VARCHAR(255) NOT NULL, provider_email VARCHAR(255) NOT NULL, user_email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN provider_request.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE service (id UUID NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, duration INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN service.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE service_provider (service_id UUID NOT NULL, provider_id UUID NOT NULL, PRIMARY KEY(service_id, provider_id))');
        $this->addSql('CREATE INDEX IDX_6BB228A1ED5CA9E6 ON service_provider (service_id)');
        $this->addSql('CREATE INDEX IDX_6BB228A1A53A8AA ON service_provider (provider_id)');
        $this->addSql('COMMENT ON COLUMN service_provider.service_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN service_provider.provider_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844BFF2FEF2 FOREIGN KEY (barber_id) REFERENCES barber (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8449D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE barber ADD CONSTRAINT FK_7C48A9A4A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE barber ADD CONSTRAINT FK_7C48A9A49D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458BFF2FEF2 FOREIGN KEY (barber_id) REFERENCES barber (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944589D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D2294458ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739C783E3463 FOREIGN KEY (manager_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_provider ADD CONSTRAINT FK_6BB228A1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_provider ADD CONSTRAINT FK_6BB228A1A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE barber_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE provider_id_seq CASCADE');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F844BFF2FEF2');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F844A53A8AA');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F844ED5CA9E6');
        $this->addSql('ALTER TABLE appointment DROP CONSTRAINT FK_FE38F8449D86650F');
        $this->addSql('ALTER TABLE barber DROP CONSTRAINT FK_7C48A9A4A53A8AA');
        $this->addSql('ALTER TABLE barber DROP CONSTRAINT FK_7C48A9A49D86650F');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D2294458BFF2FEF2');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D2294458A53A8AA');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D22944589D86650F');
        $this->addSql('ALTER TABLE feedback DROP CONSTRAINT FK_D2294458ED5CA9E6');
        $this->addSql('ALTER TABLE provider DROP CONSTRAINT FK_92C4739C783E3463');
        $this->addSql('ALTER TABLE service_provider DROP CONSTRAINT FK_6BB228A1ED5CA9E6');
        $this->addSql('ALTER TABLE service_provider DROP CONSTRAINT FK_6BB228A1A53A8AA');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE barber');
        $this->addSql('DROP TABLE feedback');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE provider_request');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_provider');
        $this->addSql('DROP TABLE "user"');
    }
}
