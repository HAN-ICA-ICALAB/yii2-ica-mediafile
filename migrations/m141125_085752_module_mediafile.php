<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\db\QueryBuilder;

class m141125_085752_module_mediafile extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%mediafiletype}}', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'mimetype' => Schema::TYPE_STRING . ' NOT NULL',
            'extension' => Schema::TYPE_STRING . ' NOT NULL',
            'CONSTRAINT uc_mediafiletype_name UNIQUE(name)',
            'CONSTRAINT uc_mediafiletype_mimetype UNIQUE(mimetype)',
            'CONSTRAINT uc_mediafiletype_extension UNIQUE(extension)',
        ], $tableOptions);

        // Create some default file types.
        $connection = Yii::$app->db;
        $connection->createCommand('INSERT INTO {{%mediafiletype}}'
            . ' (name, mimetype, extension)'
            . " VALUES('PNG file', 'image/png', 'png')")->execute();
        $connection->createCommand('INSERT INTO {{%mediafiletype}}'
            . ' (name, mimetype, extension)'
            . " VALUES('GIF file', 'image/gif', 'gif')")->execute();
        $connection->createCommand('INSERT INTO {{%mediafiletype}}'
            . ' (name, mimetype, extension)'
            . " VALUES('JPEG file', 'image/jpeg', 'jpg')")->execute();

        // Create the media file table.
        $this->createTable('{{%mediafile}}', [
            'id' => Schema::TYPE_PK,
            'mediafiletypeid' => Schema::TYPE_INTEGER . ' NOT NULL',
            'title' => Schema::TYPE_STRING,
            'notes' => Schema::TYPE_STRING,
            'uid' => Schema::TYPE_STRING . '(255) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'CONSTRAINT uc_mediafile_uid UNIQUE(uid)',
        ], $tableOptions);

        $this->addForeignKey('fk_mediafile_mediafiletypeid',
            '{{%mediafile}}', 'mediafiletypeid',
            '{{%mediafiletype}}', 'id',
            'NO ACTION', 'NO ACTION');

    }

    public function down()
    {
        $this->dropTable('{{%mediafile}}');
        $this->dropTable('{{%mediafiletype}}');
        return true;
    }
}
