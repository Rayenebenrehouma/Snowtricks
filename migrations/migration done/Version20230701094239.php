<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230701094239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure ADD user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AA832C1C9 FOREIGN KEY (email_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37A9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_2F57B37AA832C1C9 ON figure (email_id)');
        $this->addSql('CREATE INDEX IDX_2F57B37A9D86650F ON figure (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AA832C1C9');
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37A9D86650F');
        $this->addSql('DROP INDEX IDX_2F57B37AA832C1C9 ON figure');
        $this->addSql('DROP INDEX IDX_2F57B37A9D86650F ON figure');
        $this->addSql('ALTER TABLE figure DROP user_id_id');
    }
}
