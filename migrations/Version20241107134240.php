<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107134240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE day_quest_user DROP FOREIGN KEY FK_A519FA6423D0B988');
        $this->addSql('ALTER TABLE day_quest_user DROP FOREIGN KEY FK_A519FA64A76ED395');
        $this->addSql('DROP TABLE day_quest_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE day_quest_user (day_quest_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A519FA6423D0B988 (day_quest_id), INDEX IDX_A519FA64A76ED395 (user_id), PRIMARY KEY(day_quest_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE day_quest_user ADD CONSTRAINT FK_A519FA6423D0B988 FOREIGN KEY (day_quest_id) REFERENCES day_quest (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE day_quest_user ADD CONSTRAINT FK_A519FA64A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
