<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230515072948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire RENAME INDEX idx_67f068bc85f5ad92 TO IDX_67F068BC5C011B5');
        $this->addSql('ALTER TABLE figure ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD test LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaire RENAME INDEX idx_67f068bc5c011b5 TO IDX_67F068BC85F5AD92');
        $this->addSql('ALTER TABLE figure DROP created_at, DROP test');
    }
}
