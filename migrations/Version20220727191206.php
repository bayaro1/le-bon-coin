<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220727191206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E94584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_8A8E26E94584665A ON conversation (product_id)');
        $this->addSql('ALTER TABLE message ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307F4584665A ON message (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E94584665A');
        $this->addSql('DROP INDEX IDX_8A8E26E94584665A ON conversation');
        $this->addSql('ALTER TABLE conversation DROP product_id');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F4584665A');
        $this->addSql('DROP INDEX IDX_B6BD307F4584665A ON message');
        $this->addSql('ALTER TABLE message DROP product_id');
    }
}
