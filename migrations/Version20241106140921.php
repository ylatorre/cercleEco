<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106140921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etat (id INT AUTO_INCREMENT NOT NULL, quest_id INT DEFAULT NULL, user_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, code VARCHAR(255) DEFAULT NULL, is_finish TINYINT(1) NOT NULL, INDEX IDX_55CAF762209E9EF4 (quest_id), INDEX IDX_55CAF762A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE etat ADD CONSTRAINT FK_55CAF762209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id)');
        $this->addSql('ALTER TABLE etat ADD CONSTRAINT FK_55CAF762A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat DROP FOREIGN KEY FK_55CAF762209E9EF4');
        $this->addSql('ALTER TABLE etat DROP FOREIGN KEY FK_55CAF762A76ED395');
        $this->addSql('DROP TABLE etat');
    }
}
