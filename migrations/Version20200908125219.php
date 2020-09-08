<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200908125219 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add relation between book and collection entity';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books ADD collection_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE books ADD CONSTRAINT FK_4A1B2A92514956FD FOREIGN KEY (collection_id) REFERENCES collections (id)');
        $this->addSql('CREATE INDEX IDX_4A1B2A92514956FD ON books (collection_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE books DROP FOREIGN KEY FK_4A1B2A92514956FD');
        $this->addSql('DROP INDEX IDX_4A1B2A92514956FD ON books');
        $this->addSql('ALTER TABLE books DROP collection_id');
    }
}
