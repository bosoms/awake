<?php

use yii\db\Migration;

/**
 * Class m200818_084539_import_sql
 */
class m200818_084539_import_sql extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = file_get_contents(__DIR__ . '/awake.sql');
        $command = Yii::$app->db->createCommand($sql);
        $command->execute();
        // Yii::$app->db->pdo->exec($sql);

        // Make sure, we fetch all errors
        while ($command->pdoStatement->nextRowSet()) {}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200818_084539_import_sql cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200818_084539_import_sql cannot be reverted.\n";

        return false;
    }
    */
}
