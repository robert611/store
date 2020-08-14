<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200814131438 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_basic_property ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_basic_property ADD CONSTRAINT FK_3DA2466E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_3DA2466E4584665A ON product_basic_property (product_id)');
        $this->addSql('ALTER TABLE product_specific_property ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_specific_property ADD CONSTRAINT FK_85CF71A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_85CF71A4584665A ON product_specific_property (product_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_basic_property DROP FOREIGN KEY FK_3DA2466E4584665A');
        $this->addSql('DROP INDEX IDX_3DA2466E4584665A ON product_basic_property');
        $this->addSql('ALTER TABLE product_basic_property DROP product_id');
        $this->addSql('ALTER TABLE product_specific_property DROP FOREIGN KEY FK_85CF71A4584665A');
        $this->addSql('DROP INDEX IDX_85CF71A4584665A ON product_specific_property');
        $this->addSql('ALTER TABLE product_specific_property DROP product_id');
    }
}
