<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107131302 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE day_quest DROP FOREIGN KEY FK_FADA44F3A76ED395');
        $this->addSql('DROP INDEX IDX_FADA44F3A76ED395 ON day_quest');
        $this->addSql('ALTER TABLE day_quest DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE day_quest ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE day_quest ADD CONSTRAINT FK_FADA44F3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_FADA44F3A76ED395 ON day_quest (user_id)');
    }
}
