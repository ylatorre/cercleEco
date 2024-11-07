<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107095413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat ADD quest_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE etat ADD CONSTRAINT FK_55CAF762209E9EF4 FOREIGN KEY (quest_id) REFERENCES quests (id)');
        $this->addSql('ALTER TABLE etat ADD CONSTRAINT FK_55CAF762A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_55CAF762209E9EF4 ON etat (quest_id)');
        $this->addSql('CREATE INDEX IDX_55CAF762A76ED395 ON etat (user_id)');
        $this->addSql('ALTER TABLE user ADD token VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE etat DROP FOREIGN KEY FK_55CAF762209E9EF4');
        $this->addSql('ALTER TABLE etat DROP FOREIGN KEY FK_55CAF762A76ED395');
        $this->addSql('DROP INDEX IDX_55CAF762209E9EF4 ON etat');
        $this->addSql('DROP INDEX IDX_55CAF762A76ED395 ON etat');
        $this->addSql('ALTER TABLE etat DROP quest_id, DROP user_id');
        $this->addSql('ALTER TABLE user DROP token');
    }
}
