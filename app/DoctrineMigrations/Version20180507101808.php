<?php

namespace Application\Migrations;

use AppBundle\Entity\CustomerOrderStatus;
use AppBundle\Entity\ProductType;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180507101808 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, permission_to_use_personal_info TINYINT(1) NOT NULL, permission_received_at DATETIME NOT NULL, permission_denied_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_81398E09E7927C74 (email), UNIQUE INDEX UNIQ_81398E09444F97DD (phone), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_address (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, street_address VARCHAR(255) DEFAULT NULL, floor INT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_1193CB3F9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_order (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, status_id INT DEFAULT NULL, created_at DATETIME NOT NULL, closed_at DATETIME DEFAULT NULL, requested_measurement_at DATETIME NOT NULL, requested_installation_at DATETIME NOT NULL, requested_door_amount INT NOT NULL, requested_window_amount INT NOT NULL, requested_jalousie_amount INT NOT NULL, requested_gate_amount INT NOT NULL, INDEX IDX_3B1CE6A39395C3F3 (customer_id), INDEX IDX_3B1CE6A36BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer_order_status (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_5D9F75A192FC23A8 (username_canonical), UNIQUE INDEX UNIQ_5D9F75A1A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, type_id INT DEFAULT NULL, address_id INT DEFAULT NULL, measurement_at DATETIME DEFAULT NULL, installation_at DATETIME DEFAULT NULL, material VARCHAR(255) DEFAULT NULL, manufacturer VARCHAR(255) DEFAULT NULL, price INT DEFAULT NULL, vendor_code VARCHAR(255) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, sketch LONGBLOB DEFAULT NULL, INDEX IDX_D34A04AD8D9F6D38 (order_id), INDEX IDX_D34A04ADC54C8C93 (type_id), INDEX IDX_D34A04ADF5B7AF75 (address_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_136758877153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer_address ADD CONSTRAINT FK_1193CB3F9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE customer_order ADD CONSTRAINT FK_3B1CE6A36BF700BD FOREIGN KEY (status_id) REFERENCES customer_order_status (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD8D9F6D38 FOREIGN KEY (order_id) REFERENCES customer_order (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADC54C8C93 FOREIGN KEY (type_id) REFERENCES product_type (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF5B7AF75 FOREIGN KEY (address_id) REFERENCES customer_address (id)');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $status = new CustomerOrderStatus();
        $status->setCode('new');
        $status->setTitle('New');
        $em->persist($status);
        $status = new CustomerOrderStatus();
        $status->setCode('awaitingMeasurement');
        $status->setTitle('Awaiting Measurement');
        $em->persist($status);
        $status = new CustomerOrderStatus();
        $status->setCode('awaitingPricing');
        $status->setTitle('Awaiting Pricing');
        $em->persist($status);
        $status = new CustomerOrderStatus();
        $status->setCode('awaitingPrepayment');
        $status->setTitle('Awaiting Prepayment');
        $em->persist($status);
        $status = new CustomerOrderStatus();
        $status->setCode('awaitingDelivery');
        $status->setTitle('Awaiting Delivery');
        $em->persist($status);
        $status = new CustomerOrderStatus();
        $status->setCode('awaitingInstallation');
        $status->setTitle('Awaiting Installation');
        $em->persist($status);
        $status = new CustomerOrderStatus();
        $status->setCode('closed');
        $status->setTitle('Closed');
        $em->persist($status);
        $status = new CustomerOrderStatus();
        $status->setCode('rejected');
        $status->setTitle('Rejected');
        $em->persist($status);
        $em->flush();

        $em = $this->container->get('doctrine.orm.entity_manager');
        $status = new ProductType();
        $status->setCode('door');
        $status->setTitle('Door');
        $em->persist($status);
        $status = new ProductType();
        $status->setCode('window');
        $status->setTitle('Window');
        $em->persist($status);
        $status = new ProductType();
        $status->setCode('jalousie');
        $status->setTitle('Jalousie');
        $em->persist($status);
        $status = new ProductType();
        $status->setCode('gate');
        $status->setTitle('Gate');
        $em->persist($status);

        $em->flush();

        // create admin user
        shell_exec('app/console fos:user:create admin admin@admin.lv default --super-admin');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer_address DROP FOREIGN KEY FK_1193CB3F9395C3F3');
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A39395C3F3');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADF5B7AF75');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD8D9F6D38');
        $this->addSql('ALTER TABLE customer_order DROP FOREIGN KEY FK_3B1CE6A36BF700BD');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADC54C8C93');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_address');
        $this->addSql('DROP TABLE customer_order');
        $this->addSql('DROP TABLE customer_order_status');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_type');
    }
}
