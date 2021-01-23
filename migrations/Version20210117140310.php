<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210117140310 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_opinion (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, user_id INT NOT NULL, mark SMALLINT NOT NULL, opinion VARCHAR(1024) DEFAULT NULL, advantages VARCHAR(1024) DEFAULT NULL, flaws VARCHAR(1024) DEFAULT NULL, INDEX IDX_7ADBAB974584665A (product_id), INDEX IDX_7ADBAB97A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_polish_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_opinion ADD CONSTRAINT FK_7ADBAB974584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_opinion ADD CONSTRAINT FK_7ADBAB97A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(191) NOT NULL, CHANGE username username VARCHAR(191) DEFAULT NULL');
        $this->addSql('ALTER TABLE ext_translations CHANGE object_class object_class VARCHAR(191) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE product_opinion');
        $this->addSql('ALTER TABLE ext_log_entries CHANGE object_class object_class VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE username username VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ext_translations CHANGE object_class object_class VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
