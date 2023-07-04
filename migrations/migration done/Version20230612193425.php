<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612193425 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE illustration DROP FOREIGN KEY FK_D67B9A42ADA40271');
        $this->addSql('DROP INDEX IDX_D67B9A42ADA40271 ON illustration');
        $this->addSql('ALTER TABLE illustration CHANGE link_id link INT NOT NULL');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A4236AC99F1 FOREIGN KEY (link) REFERENCES figure (id)');
        $this->addSql('CREATE INDEX IDX_D67B9A4236AC99F1 ON illustration (link)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE illustration DROP FOREIGN KEY FK_D67B9A4236AC99F1');
        $this->addSql('DROP INDEX IDX_D67B9A4236AC99F1 ON illustration');
        $this->addSql('ALTER TABLE illustration CHANGE link link_id INT NOT NULL');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A42ADA40271 FOREIGN KEY (link_id) REFERENCES figure (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D67B9A42ADA40271 ON illustration (link_id)');
    }
}
