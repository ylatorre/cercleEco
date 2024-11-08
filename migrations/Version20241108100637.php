<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108100637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dons ADD acheteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE dons ADD CONSTRAINT FK_E4F955FA96A7BB5F FOREIGN KEY (acheteur_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E4F955FA96A7BB5F ON dons (acheteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dons DROP FOREIGN KEY FK_E4F955FA96A7BB5F');
        $this->addSql('DROP INDEX IDX_E4F955FA96A7BB5F ON dons');
        $this->addSql('ALTER TABLE dons DROP acheteur_id');
    }
}
