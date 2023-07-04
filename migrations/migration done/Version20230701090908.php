<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230701090908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure ADD email_id INT NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA832C1C9 FOREIGN KEY (email_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37AA832C1C9 ON figure (email_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA832C1C9');
        $this->addSql('DROP INDEX IDX_2F57B37AA832C1C9 ON figure');
        $this->addSql('ALTER TABLE figure DROP email_id');
    }
}
