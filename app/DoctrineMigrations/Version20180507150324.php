<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180507150324 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE surname surname VARCHAR(255) DEFAULT NULL, CHANGE permission_denied_at permission_denied_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_address CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT NULL, CHANGE street_address street_address VARCHAR(255) DEFAULT NULL, CHANGE floor floor INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_order ADD invoice_id VARCHAR(255) DEFAULT NULL, CHANGE status_id status_id INT DEFAULT NULL, CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE address_id address_id INT DEFAULT NULL, CHANGE closed_at closed_at DATETIME DEFAULT NULL, CHANGE rejected_at rejected_at DATETIME DEFAULT NULL, CHANGE invoice_sent invoice_sent TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE employee CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE expires_at expires_at DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE credentials_expire_at credentials_expire_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE product CHANGE order_id order_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE address_id address_id INT DEFAULT NULL, CHANGE measurement_at measurement_at DATETIME DEFAULT NULL, CHANGE installation_at installation_at DATETIME DEFAULT NULL, CHANGE material material VARCHAR(255) DEFAULT NULL, CHANGE manufacturer manufacturer VARCHAR(255) DEFAULT NULL, CHANGE price price INT DEFAULT NULL, CHANGE vendor_code vendor_code VARCHAR(255) DEFAULT NULL, CHANGE sketch sketch VARCHAR(255) DEFAULT NULL, CHANGE width width INT DEFAULT NULL, CHANGE height height INT DEFAULT NULL, CHANGE markup markup INT DEFAULT NULL, CHANGE installation_price installation_price INT DEFAULT NULL, CHANGE delivery_price delivery_price INT DEFAULT NULL, CHANGE additional_markup additional_markup INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer CHANGE name name VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE surname surname VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE permission_denied_at permission_denied_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE customer_address CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE city city VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE street_address street_address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE floor floor INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_order DROP invoice_id, CHANGE customer_id customer_id INT DEFAULT NULL, CHANGE status_id status_id INT DEFAULT NULL, CHANGE address_id address_id INT DEFAULT NULL, CHANGE closed_at closed_at DATETIME DEFAULT \'NULL\', CHANGE rejected_at rejected_at DATETIME DEFAULT \'NULL\', CHANGE invoice_sent invoice_sent TINYINT(1) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE employee CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE expires_at expires_at DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE credentials_expire_at credentials_expire_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product CHANGE order_id order_id INT DEFAULT NULL, CHANGE type_id type_id INT DEFAULT NULL, CHANGE address_id address_id INT DEFAULT NULL, CHANGE measurement_at measurement_at DATETIME DEFAULT \'NULL\', CHANGE installation_at installation_at DATETIME DEFAULT \'NULL\', CHANGE material material VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE manufacturer manufacturer VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE price price INT DEFAULT NULL, CHANGE markup markup INT DEFAULT NULL, CHANGE installation_price installation_price INT DEFAULT NULL, CHANGE delivery_price delivery_price INT DEFAULT NULL, CHANGE additional_markup additional_markup INT DEFAULT NULL, CHANGE width width INT DEFAULT NULL, CHANGE height height INT DEFAULT NULL, CHANGE vendor_code vendor_code VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE sketch sketch VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
