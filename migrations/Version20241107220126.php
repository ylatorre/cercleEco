<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241107220126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quetes_reponses (id INT AUTO_INCREMENT NOT NULL, quete_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, is_good_question INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D84D77A47A1B721E (quete_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quetes_reponses ADD CONSTRAINT FK_D84D77A47A1B721E FOREIGN KEY (quete_id) REFERENCES quetes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quetes_reponses DROP FOREIGN KEY FK_D84D77A47A1B721E');
        $this->addSql('DROP TABLE quetes_reponses');
    }
}
