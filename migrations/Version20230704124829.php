<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230704124829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CADA40271');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CADA40271 FOREIGN KEY (link_id) REFERENCES figure (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2CADA40271');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2CADA40271 FOREIGN KEY (link_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
