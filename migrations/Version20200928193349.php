<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200928193349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Change the direction of relation between gift and user';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_gift (user_id INT NOT NULL, gift_id INT NOT NULL, INDEX IDX_DEFDD5C4A76ED395 (user_id), INDEX IDX_DEFDD5C497A95A83 (gift_id), PRIMARY KEY(user_id, gift_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_gift ADD CONSTRAINT FK_DEFDD5C4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_gift ADD CONSTRAINT FK_DEFDD5C497A95A83 FOREIGN KEY (gift_id) REFERENCES gifts (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE gift_user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gift_user (gift_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7FEE241497A95A83 (gift_id), INDEX IDX_7FEE2414A76ED395 (user_id), PRIMARY KEY(gift_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE gift_user ADD CONSTRAINT FK_7FEE241497A95A83 FOREIGN KEY (gift_id) REFERENCES gifts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gift_user ADD CONSTRAINT FK_7FEE2414A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE user_gift');
    }
}
