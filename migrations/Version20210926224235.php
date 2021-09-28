<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210926224235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_reset ADD user_id INT NOT NULL, ADD hashed_token VARCHAR(255) NOT NULL, ADD requested_at DATETIME NOT NULL, ADD expire_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE password_reset ADD CONSTRAINT FK_B1017252A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1017252A76ED395 ON password_reset (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE password_reset DROP FOREIGN KEY FK_B1017252A76ED395');
        $this->addSql('DROP INDEX UNIQ_B1017252A76ED395 ON password_reset');
        $this->addSql('ALTER TABLE password_reset DROP user_id, DROP hashed_token, DROP requested_at, DROP expire_at');
    }
}
