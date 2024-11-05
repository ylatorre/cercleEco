<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241105144050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quest_content (id INT AUTO_INCREMENT NOT NULL, quest_id INT DEFAULT NULL, contenu VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_AAE28DE2209E9EF4 (quest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quest_content ADD CONSTRAINT FK_AAE28DE2209E9EF4 FOREIGN KEY (quest_id) REFERENCES quest (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quest_content DROP FOREIGN KEY FK_AAE28DE2209E9EF4');
        $this->addSql('DROP TABLE quest_content');
    }
}
