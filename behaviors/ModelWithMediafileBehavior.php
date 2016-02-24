<?php
/*****************************************************************************
 *                                                                           *
 * Behavior for adding and removing Mediafile objects to objects of another  *
 * class. There should be a join table {{%modeltable_mediafile}} with two    *
 * columns, namely parentid containing the id of the parent object and       *
 * mediafileid containing the id of the joined media file. The addMediafile  *
 * and removeMediafile can then be used to assign or unassign media files to *
 * the parent object. To retrieve all of the media files for a parent        *
 * object, access its mediafiles attribute.                                  *
 *                                                                           *
 ****************************************************************************/
namespace icalab\mediafile\behaviors;

use Yii;
use yii\db\ActiveRecord;
use icalab\mediafile\models\Mediafile;
use icalab\mediafile\MediafileModule;
use yii\base\Exception;
use yii\base\Behavior;

class ModelWithMediafileBehavior extends Behavior
{

    /**
     * This attribute can be used to deal with uploading files in
     * controllers and views. It is NOT used inside the model itself.
     * @var UploadedFile|Null newFiles attribute
     */
    public $newFiles;

    private $_viaTableName = NULL;
    protected function getViaTableName()
    {
        if(preg_match('/}}$/', $this->owner->tableName()))
        {
            $this->_viaTableName = preg_replace(
                '/(}})$/', '_mediafile$1', $this->owner->tableName());
        }
        else
        {
            $this->_viaTableName = preg_replace(
                '/$/', '_mediafile', $this->owner->tableName());
        }
        return $this->_viaTableName;
    }

    /**
     * Return currently assigned media files.
     * @return the media files that are currently assigned to this model
     */
    public function getMediafiles()
    {
        return $this->owner->hasMany(
            Mediafile::className(), ['id' => 'mediafileid'])
            ->viaTable(
                $this->getViaTableName(), ['parentid' => 'id'])->all();
    }

    /**
     * Assign a new media file to a model.
     * @param mediafile the mediafile to assign
     */
    public function addMediafile($mediafile)
    {
        foreach($this->getMediafiles() as $assigned)
        {
            if(! $assigned)
            {
                echo "Assigned is empty???\n";
                echo '<pre>'. print_r($this->getMediafiles(), true) . '</pre>';
                exit();
            }
            if($assigned->primaryKey == $mediafile->primaryKey)
            {
                return;
            }
        }

        \Yii::$app->db->createCommand()->insert($this->getViaTableName(), [
                    'parentid' => $this->owner->primaryKey,
                    'mediafileid' => $mediafile->primaryKey
                    ])->execute();
    }

    /**
     * Remove a media file from a model.
     * @param mediafile the media file to unassign / remove
     */
    public function removeMediafile($mediafile)
    {
        $viaTableName = $this->getViaTableName();

        \Yii::$app->db->createCommand()->delete(
            $viaTableName,
            'parentid=' . $this->owner->primaryKey
            . ' AND mediafileid='
            . $mediafile->primaryKey
        )->execute();
    }

}



