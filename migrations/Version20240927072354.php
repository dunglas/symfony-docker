<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927072354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incident_photo DROP FOREIGN KEY FK_4F83267224703C3D');
        $this->addSql('ALTER TABLE incident_photo DROP FOREIGN KEY FK_4F832672C51599E0');
        $this->addSql('DROP INDEX IDX_4F832672C51599E0 ON incident_photo');
        $this->addSql('DROP INDEX IDX_4F83267224703C3D ON incident_photo');
        $this->addSql('ALTER TABLE incident_photo ADD incident_id INT DEFAULT NULL, ADD photo_id INT DEFAULT NULL, DROP incident_id_id, DROP photo_id_id');
        $this->addSql('ALTER TABLE incident_photo ADD CONSTRAINT FK_4F83267259E53FB9 FOREIGN KEY (incident_id) REFERENCES incident (id)');
        $this->addSql('ALTER TABLE incident_photo ADD CONSTRAINT FK_4F8326727E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('CREATE INDEX IDX_4F83267259E53FB9 ON incident_photo (incident_id)');
        $this->addSql('CREATE INDEX IDX_4F8326727E9E4C8C ON incident_photo (photo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incident_photo DROP FOREIGN KEY FK_4F83267259E53FB9');
        $this->addSql('ALTER TABLE incident_photo DROP FOREIGN KEY FK_4F8326727E9E4C8C');
        $this->addSql('DROP INDEX IDX_4F83267259E53FB9 ON incident_photo');
        $this->addSql('DROP INDEX IDX_4F8326727E9E4C8C ON incident_photo');
        $this->addSql('ALTER TABLE incident_photo ADD incident_id_id INT DEFAULT NULL, ADD photo_id_id INT DEFAULT NULL, DROP incident_id, DROP photo_id');
        $this->addSql('ALTER TABLE incident_photo ADD CONSTRAINT FK_4F83267224703C3D FOREIGN KEY (incident_id_id) REFERENCES incident (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE incident_photo ADD CONSTRAINT FK_4F832672C51599E0 FOREIGN KEY (photo_id_id) REFERENCES photo (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4F832672C51599E0 ON incident_photo (photo_id_id)');
        $this->addSql('CREATE INDEX IDX_4F83267224703C3D ON incident_photo (incident_id_id)');
    }
}
