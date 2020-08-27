<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200825155110 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_type_product (delivery_type_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_64E8925CCF52334D (delivery_type_id), INDEX IDX_64E8925C4584665A (product_id), PRIMARY KEY(delivery_type_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delivery_type_product ADD CONSTRAINT FK_64E8925CCF52334D FOREIGN KEY (delivery_type_id) REFERENCES delivery_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE delivery_type_product ADD CONSTRAINT FK_64E8925C4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE delivery_type ADD default_price DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE delivery_type_product');
        $this->addSql('ALTER TABLE delivery_type DROP default_price');
    }
}
