<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926094407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo ADD add_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841817542AC5 FOREIGN KEY (add_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_14B7841817542AC5 ON photo (add_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841817542AC5');
        $this->addSql('DROP INDEX IDX_14B7841817542AC5 ON photo');
        $this->addSql('ALTER TABLE photo DROP add_by_id');
    }
}
