<?php

use yii\db\Migration;

class m230301_072121_create_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable(table: '{{%client}}', columns: [
            'id' => $this->primaryKey(),
            'last_name' => $this->string(length: 30)->notNull()->comment(comment: 'Фамилия'),
            'first_name' => $this->string(length: 30)->notNull()->comment(comment: 'Имя'),
            'patronymic' => $this->string(length: 30)->notNull()->comment(comment: 'Отчество'),
            'mobile_phone' => $this->integer(length: 10)->notNull()->comment(comment: 'Мобильный телефон'),
            'email' => $this->string()->null(),
            'birth_date' => $this->date()->notNull()->comment(comment: 'Дата рождения'),
            'act_region_code' => $this->integer(length: 2)->notNull()->comment(comment: 'Код региона проживания'),
            'fact_city_name' => $this->string(length: 100)->notNull()->comment(comment: 'Фактический город проживания'),
            'fact_city_street' => $this->string(length: 100)->notNull()->comment(comment: 'Фактическая улица проживания'),
            'fact_house' => $this->string(length: 100)->notNull()->comment(comment: 'Фактический дом проживания'),
            'reg_housing' => $this->string(100)->null()->comment(comment: 'Фактическое строение проживания'),
            'reg_flat' => $this->integer(length: 4)->null()->comment(comment: 'Фактическая квартира регистрации'),
            'passport_id' => $this->integer()->notNull()->comment(comment: 'Id паспорта'),
            'loan_id' => $this->integer()->notNull()->comment(comment: 'Id займа'),
        ]);

        $this->createTable('{{%loan}}', [
            'id' => $this->primaryKey(),
            'issuing_reg' => $this->integer(length: 2)->notNull()->comment(comment: 'Регион выдачи заема'),
            'credit_sum' => $this->integer(length: 6)->notNull()->comment(comment: 'Сумма заема'),
            'client_id' => $this->integer()->notNull(),
        ]);

        $this->createTable('{{%passport}}', [
            'id' => $this->primaryKey(),
            'passport_number' => $this->integer(length: 10)->notNull()->comment(comment: 'Номер паспорта'),
            'passport_fms' => $this->string(length: 300)->notNull()->comment(comment: 'Кем выдан паспорт'),
            'passport_code' => $this->integer(length: 6)->notNull()->comment(comment: 'Код подразделения'),
            'passport_date' => $this->date()->notNull()->comment(comment: 'Дата выдачи'),
            'reg_region_name' => $this->string(length: 100)->notNull()->comment(comment: 'Регион регистрации'),
            'reg_city_name' => $this->string(length: 100)->notNull()->comment(comment: 'Город регистрации'),
            'reg_city_street' => $this->string(length: 100)->notNull()->comment(comment: 'Улица регистрации'),
            'reg_house' => $this->string(length: 100)->notNull()->comment(comment: 'Дом регистрации'),
            'reg_housing' => $this->string(100)->null()->comment(comment: 'Строение регистрации'),
            'reg_flat' => $this->integer(length: 4)->null()->comment(comment: 'Квартира регистрации'),
            'birth_place' => $this->string(length: 100)->Null()->comment(comment: 'Дом регистрации'),
            'client_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            name: 'fk-loan-client_id',
            table: 'loan',
            columns: 'client_id',
            refTable: 'client',
            refColumns: 'id',
            delete: 'CASCADE'
        );

        $this->addForeignKey(
            name: 'fk-passport-client_id',
            table: 'passport',
            columns: 'client_id',
            refTable: 'client',
            refColumns: 'id',
            delete: 'CASCADE'
        );

        $this->addForeignKey(
            name: 'fk-client-loan_id',
            table: 'client',
            columns: 'loan_id',
            refTable: 'loan',
            refColumns: 'id',
            delete: 'CASCADE'
        );

        $this->addForeignKey(
            name: 'fk-client-passport_id',
            table: 'client',
            columns: 'passport_id',
            refTable: 'passport',
            refColumns: 'id',
            delete: 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable(table: '{{%client}}');
        $this->dropTable(table: '{{%loan}}');
    }
}
