<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200924122526 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create gift table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gifts (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION DEFAULT NULL, image_name VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gift_user (gift_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7FEE241497A95A83 (gift_id), INDEX IDX_7FEE2414A76ED395 (user_id), PRIMARY KEY(gift_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gift_user ADD CONSTRAINT FK_7FEE241497A95A83 FOREIGN KEY (gift_id) REFERENCES gifts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gift_user ADD CONSTRAINT FK_7FEE2414A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gift_user DROP FOREIGN KEY FK_7FEE241497A95A83');
        $this->addSql('DROP TABLE gifts');
        $this->addSql('DROP TABLE gift_user');
    }
}
