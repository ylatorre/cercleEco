<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106094715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dons ADD token VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE quests DROP ordre, DROP xp_give');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quests ADD ordre INT NOT NULL, ADD xp_give DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE dons DROP token');
    }
}
