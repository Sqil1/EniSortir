<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231113180714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant CHANGE campus_id campus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant_sortie DROP FOREIGN KEY FK_8E436D739D1C3019');
        $this->addSql('ALTER TABLE participant_sortie ADD CONSTRAINT FK_8E436D739D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant_sortie DROP FOREIGN KEY FK_8E436D739D1C3019');
        $this->addSql('ALTER TABLE participant_sortie ADD CONSTRAINT FK_8E436D739D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE participant CHANGE campus_id campus_id INT NOT NULL');
    }
}
