<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210910211831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paste ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE paste ADD CONSTRAINT FK_9C567898A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9C567898A76ED395 ON paste (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paste DROP FOREIGN KEY FK_9C567898A76ED395');
        $this->addSql('DROP INDEX IDX_9C567898A76ED395 ON paste');
        $this->addSql('ALTER TABLE paste DROP user_id');
    }
}
