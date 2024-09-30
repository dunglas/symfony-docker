<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926145042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incident ADD bus_id_id INT DEFAULT NULL, ADD line_id_id INT DEFAULT NULL, ADD user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT FK_3D03A11AE9123BA6 FOREIGN KEY (bus_id_id) REFERENCES bus (id)');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT FK_3D03A11A80C0B9D4 FOREIGN KEY (line_id_id) REFERENCES line (id)');
        $this->addSql('ALTER TABLE incident ADD CONSTRAINT FK_3D03A11A9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3D03A11AE9123BA6 ON incident (bus_id_id)');
        $this->addSql('CREATE INDEX IDX_3D03A11A80C0B9D4 ON incident (line_id_id)');
        $this->addSql('CREATE INDEX IDX_3D03A11A9D86650F ON incident (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE incident DROP FOREIGN KEY FK_3D03A11AE9123BA6');
        $this->addSql('ALTER TABLE incident DROP FOREIGN KEY FK_3D03A11A80C0B9D4');
        $this->addSql('ALTER TABLE incident DROP FOREIGN KEY FK_3D03A11A9D86650F');
        $this->addSql('DROP INDEX IDX_3D03A11AE9123BA6 ON incident');
        $this->addSql('DROP INDEX IDX_3D03A11A80C0B9D4 ON incident');
        $this->addSql('DROP INDEX IDX_3D03A11A9D86650F ON incident');
        $this->addSql('ALTER TABLE incident DROP bus_id_id, DROP line_id_id, DROP user_id_id');
    }
}
